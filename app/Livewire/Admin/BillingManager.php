<?php

namespace App\Livewire\Admin;

use App\Models\BillingPlan;
use App\Models\BillingSetting;
use App\Models\Subscription;
use Illuminate\View\View;
use Livewire\Component;

class BillingManager extends Component
{
    public string $defaultProvider = '';

    public string $fallbackProvider = '';

    public bool $testMode = true;

    public function mount(): void
    {
        $this->defaultProvider = BillingSetting::get('default_provider', 'flutterwave');
        $this->fallbackProvider = BillingSetting::get('fallback_provider', 'paystack');
        $this->testMode = (bool) BillingSetting::get('test_mode', true);
    }

    public function saveBillingSettings(): void
    {
        BillingSetting::set('default_provider', $this->defaultProvider);
        BillingSetting::set('fallback_provider', $this->fallbackProvider);
        BillingSetting::set('test_mode', $this->testMode ? '1' : '0');
        $this->dispatch('show-toast', message: 'Billing settings saved.', type: 'success');
    }

    public function updatePlanPrice(string $planId, string $amount): void
    {
        $plan = BillingPlan::find($planId);
        if (! $plan) {
            return;
        }

        $plan->update(['amount' => (int) $amount]);
        $this->dispatch('show-toast', message: "Price updated for {$plan->planLabel()}.", type: 'success');
    }

    public function togglePlanActive(string $planId): void
    {
        $plan = BillingPlan::find($planId);
        if (! $plan) {
            return;
        }

        $plan->update(['is_active' => ! $plan->is_active]);
        $this->dispatch('show-toast', message: ($plan->is_active ? 'Activated' : 'Deactivated')." {$plan->planLabel()}.", type: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.billing-manager', [
            'plans' => BillingPlan::orderBy('plan')->orderBy('interval')->orderBy('currency')->get(),
            'subscriptions' => Subscription::with('workspace')->latest()->take(20)->get(),
        ]);
    }
}
