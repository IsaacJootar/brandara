<?php

namespace App\Livewire\Admin;

use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $workspaces = Workspace::all();
        $totalWorkspaces = $workspaces->count();
        $byPlan = $workspaces->groupBy('plan')->map->count();
        $trialing = $workspaces->where('subscription_status', 'trialing')->count();
        $active = $workspaces->where('subscription_status', 'active')->count();
        $expired = $workspaces->filter(fn ($w) => ! $w->isActive())->count();

        // MRR estimate from active subscriptions
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('period_end', '>', now())
            ->get();
        $mrr = $activeSubscriptions->sum(function ($sub) {
            return $sub->interval === 'yearly' ? $sub->amount / 12 : $sub->amount;
        });

        // Recent subscriptions
        $recentPayments = Subscription::latest()->take(5)->get();

        return view('livewire.admin.dashboard', [
            'totalWorkspaces' => $totalWorkspaces,
            'byPlan' => $byPlan,
            'trialing' => $trialing,
            'active' => $active,
            'expired' => $expired,
            'mrr' => $mrr,
            'recentPayments' => $recentPayments,
        ]);
    }
}
