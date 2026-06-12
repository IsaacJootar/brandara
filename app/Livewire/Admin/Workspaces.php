<?php

namespace App\Livewire\Admin;

use App\Models\Workspace;
use Illuminate\View\View;
use Livewire\Component;

class Workspaces extends Component
{
    public string $search = '';

    public string $filterPlan = '';

    public string $filterStatus = '';

    public function changePlan(string $workspaceId, string $newPlan): void
    {
        $workspace = Workspace::findOrFail($workspaceId);

        if (! in_array($newPlan, ['starter', 'pro', 'agency'])) {
            return;
        }

        $workspace->update(['plan' => $newPlan]);
        $this->dispatch('show-toast', message: "Changed {$workspace->name} to {$newPlan}.", type: 'success');
    }

    public function extendTrial(string $workspaceId, int $days = 7): void
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $workspace->update([
            'trial_ends_at' => ($workspace->trial_ends_at ?? now())->addDays($days),
            'subscription_status' => 'trialing',
        ]);
        $this->dispatch('show-toast', message: "Extended trial for {$workspace->name} by {$days} days.", type: 'success');
    }

    public function render(): View
    {
        $query = Workspace::where('slug', '!=', 'brandara-admin');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('owner_email', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterPlan) {
            $query->where('plan', $this->filterPlan);
        }

        if ($this->filterStatus) {
            $query->where('subscription_status', $this->filterStatus);
        }

        return view('livewire.admin.workspaces', [
            'workspaces' => $query->latest()->get(),
        ]);
    }
}
