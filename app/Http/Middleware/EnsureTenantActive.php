<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $workspace = tenant();

        if (! $workspace) {
            return redirect('/');
        }

        $status = $workspace->subscription_status;

        if ($status === 'trialing' && $workspace->trial_ends_at && $workspace->trial_ends_at->isPast()) {
            return response()->view('billing.upgrade', ['reason' => 'Your free trial has ended.']);
        }

        if (in_array($status, ['past_due', 'cancelled'])) {
            return response()->view('billing.upgrade', ['reason' => 'Your subscription is inactive.']);
        }

        return $next($request);
    }
}
