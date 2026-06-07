<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Metrics;
use App\Models\Brand;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    private function makeBrand(): array
    {
        $workspace = Workspace::create([
            'name' => 'Acme', 'slug' => 'acme',
            'owner_email' => 'owner@acme.test', 'country' => 'NG',
            'timezone' => 'Africa/Lagos', 'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
            'subscription_status' => 'trialing', 'language' => 'en',
        ]);

        $user = User::create([
            'workspace_id' => $workspace->id, 'name' => 'Owner',
            'email' => 'owner@acme.test', 'password' => bcrypt('secret-pass'),
            'role' => 'owner',
        ]);

        $brand = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'Acme Consulting', 'slug' => 'acme-consulting',
            'language' => 'en',
        ]);

        return [$user, $brand];
    }

    public function test_dashboard_loads_with_lazy_metrics_placeholder(): void
    {
        [$user, $brand] = $this->makeBrand();

        $this->actingAs($user)
            ->get("/{$brand->slug}/dashboard")
            ->assertOk()
            ->assertSeeLivewire(Metrics::class);
    }

    public function test_metrics_component_renders_counts(): void
    {
        [$user, $brand] = $this->makeBrand();

        $this->actingAs($user);

        Livewire::withoutLazyLoading()
            ->test(Metrics::class, ['brand' => $brand])
            ->assertSee('Posts published')
            ->assertSee('Platforms connected')
            ->assertSee('Warm leads');
    }
}
