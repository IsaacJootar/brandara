<?php

namespace Tests\Feature;

use App\Models\BillingPlan;
use App\Models\BillingSetting;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Billing\BillingService;
use App\Services\Billing\FlutterwaveProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private function makeWorkspace(): array
    {
        $workspace = Workspace::create([
            'name' => 'Test Co', 'slug' => 'test-co',
            'owner_email' => 'owner@test.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(7),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);
        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@test.test', 'password' => bcrypt('secret'),
            'role' => 'owner',
        ]);

        \App\Models\Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Test Brand', 'slug' => 'test-brand', 'language' => 'en',
        ]);

        return [$user, $workspace];
    }

    private function seedPlans(): void
    {
        $this->artisan('db:seed', ['--class' => 'BillingSeeder'])->assertSuccessful();
    }

    public function test_billing_seeder_creates_plans_and_settings(): void
    {
        $this->seedPlans();
        $this->assertEquals(36, BillingPlan::count());
        $this->assertEquals(6, BillingSetting::count());
        $this->assertEquals('flutterwave', BillingSetting::get('default_provider'));
    }

    public function test_billing_plan_format_amount_ngn(): void
    {
        $plan = BillingPlan::create(['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 30000, 'is_active' => true]);
        $this->assertEquals('₦30,000', $plan->formattedAmount());
    }

    public function test_billing_plan_format_amount_usd(): void
    {
        $plan = BillingPlan::create(['plan' => 'starter', 'interval' => 'monthly', 'currency' => 'USD', 'amount' => 19.00, 'is_active' => true]);
        $this->assertEquals('$19.00', $plan->formattedAmount());
    }

    public function test_billing_plan_yearly_savings(): void
    {
        $this->seedPlans();
        $yearly = BillingPlan::where('plan', 'starter')->where('interval', 'yearly')->where('currency', 'USD')->first();
        $this->assertEquals('$38.00', $yearly->yearlySavings()); // 12×19=228 minus 190=38
    }

    public function test_currency_for_nigeria_workspace(): void
    {
        [, $workspace] = $this->makeWorkspace(); // country = NG
        $this->assertEquals('NGN', app(BillingService::class)->currencyForWorkspace($workspace));
    }

    public function test_plans_for_currency_returns_three_plans_per_interval(): void
    {
        $this->seedPlans();
        $plans = app(BillingService::class)->plansForCurrency('USD');
        $this->assertCount(3, $plans['monthly']);
        $this->assertCount(3, $plans['yearly']);
    }

    public function test_handle_payment_success_upgrades_workspace(): void
    {
        [, $workspace] = $this->makeWorkspace();
        app(BillingService::class)->handlePaymentSuccess([
            'workspace_id' => $workspace->id, 'plan' => 'pro',
            'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 60000,
            'reference' => 'BRD-FW-TEST123', 'customer_id' => 'cust_001',
            'provider' => 'flutterwave',
        ]);
        $workspace->refresh();
        $this->assertEquals('pro', $workspace->plan);
        $this->assertEquals('active', $workspace->subscription_status);
        $this->assertDatabaseHas('subscriptions', [
            'workspace_id' => $workspace->id, 'plan' => 'pro',
            'provider' => 'flutterwave', 'status' => 'active',
        ]);
    }

    public function test_handle_cancellation_marks_subscription_cancelled(): void
    {
        [, $workspace] = $this->makeWorkspace();
        Subscription::create([
            'workspace_id' => $workspace->id, 'plan' => 'pro', 'interval' => 'monthly',
            'currency' => 'NGN', 'amount' => 60000, 'status' => 'active',
            'provider' => 'flutterwave', 'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);
        app(BillingService::class)->handleCancellation($workspace->id);
        $workspace->refresh();
        $this->assertEquals('cancelled', $workspace->subscription_status);
        $this->assertDatabaseHas('subscriptions', ['workspace_id' => $workspace->id, 'status' => 'cancelled']);
    }

    public function test_billing_page_loads_for_authenticated_user(): void
    {
        [$user] = $this->makeWorkspace();
        $this->seedPlans();
        $this->actingAs($user)->get(route('billing'))->assertStatus(200)->assertSee('Billing', false);
    }

    public function test_billing_page_accessible_when_trial_expired(): void
    {
        [$user, $workspace] = $this->makeWorkspace();
        $workspace->update(['subscription_status' => 'cancelled', 'trial_ends_at' => now()->subDay()]);
        $this->seedPlans();
        $this->actingAs($user)->get(route('billing'))->assertStatus(200);
    }

    public function test_flutterwave_provider_initialize_payment(): void
    {
        [, $workspace] = $this->makeWorkspace();
        $plan = BillingPlan::create(['plan' => 'pro', 'interval' => 'monthly', 'currency' => 'NGN', 'amount' => 60000, 'is_active' => true]);
        $data = app(FlutterwaveProvider::class)->initializePayment($workspace, $plan);
        $this->assertArrayHasKey('tx_ref', $data);
        $this->assertStringStartsWith('BRD-FW-', $data['tx_ref']);
        $this->assertEquals('NGN', $data['currency']);
        $this->assertEquals(60000.0, $data['amount']);
    }
}
