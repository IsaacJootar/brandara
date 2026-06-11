<?php

namespace App\Livewire\Admin;

use App\Models\AdminSetting;
use Illuminate\View\View;
use Livewire\Component;

class FeatureManager extends Component
{
    /** @var array<string, array<string, mixed>> */
    public array $gates = [];

    /** @var array<string, int> */
    public array $generationLimits = [];

    /** @var array<string, int> */
    public array $brandLimits = [];

    /** @var array<string, int> */
    public array $storageLimits = [];

    public function mount(): void
    {
        $this->gates = AdminSetting::getJson('feature_gates', config('features.gates', []));
        $this->generationLimits = AdminSetting::getJson('generation_limits', config('features.generation_limits', []));
        $this->brandLimits = AdminSetting::getJson('brand_limits', config('features.brand_limits', []));
        $this->storageLimits = AdminSetting::getJson('storage_limits_mb', config('features.storage_limits_mb', []));
    }

    public function toggleFeaturePlan(string $feature, string $plan): void
    {
        if (! isset($this->gates[$feature])) {
            return;
        }

        $plans = $this->gates[$feature]['plans'] ?? [];

        if (in_array($plan, $plans)) {
            $this->gates[$feature]['plans'] = array_values(array_diff($plans, [$plan]));
        } else {
            $this->gates[$feature]['plans'][] = $plan;
        }

        AdminSetting::setJson('feature_gates', $this->gates, 'features');
        $this->dispatch('show-toast', message: 'Feature access updated.', type: 'success');
    }

    public function saveLimits(): void
    {
        AdminSetting::setJson('generation_limits', $this->generationLimits, 'features');
        AdminSetting::setJson('brand_limits', $this->brandLimits, 'features');
        AdminSetting::setJson('storage_limits_mb', $this->storageLimits, 'features');
        $this->dispatch('show-toast', message: 'Limits saved.', type: 'success');
    }

    public function render(): View
    {
        return view('livewire.admin.feature-manager');
    }
}
