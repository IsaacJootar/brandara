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
            return response()->view('billing.upgrade', [
                'reason' => $workspace->subscription_status === 'trialing'
                    ? 'Your free trial has ended.'
                    : 'Your subscription is inactive.',
                'workspace' => $workspace,
            ]);
        }

        return $next($request);
    }
}
