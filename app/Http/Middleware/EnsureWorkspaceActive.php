<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $workspace = auth()->user()?->workspace;

        if (! $workspace) {
            return redirect()->route('login');
        }

        if (! $workspace->isActive()) {
            $reason = $workspace->subscription_status === 'trialing'
                ? 'Your free trial has ended. Choose a plan to keep publishing.'
                : 'Your subscription is inactive. Choose a plan to reactivate your account.';

            return redirect()->route('billing')->with('expired_reason', $reason);
        }

        return $next($request);
    }
}
