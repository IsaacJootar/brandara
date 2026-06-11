<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only allows access to users whose email is in the ADMIN_EMAILS env list.
 * This keeps the admin panel locked to Isaac (and any future admins)
 * without needing a separate "platform workspace" concept.
 */
class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $adminEmails = array_map('trim', explode(',', config('app.admin_emails', '')));

        if (! in_array($request->user()->email, $adminEmails)) {
            abort(403, 'You do not have access to the admin panel.');
        }

        return $next($request);
    }
}
