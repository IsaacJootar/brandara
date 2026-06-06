<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveBrand
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('brand');

        // Load brand — must belong to the authenticated user's workspace
        $brand = Brand::where('slug', $slug)
            ->where('workspace_id', auth()->user()->workspace_id)
            ->first();

        if (! $brand) {
            abort(403, 'Brand not found or access denied.');
        }

        // Bind to container so controllers and services can access it without
        // re-querying. Access via currentBrand() helper or app('current.brand').
        app()->instance('current.brand', $brand);

        // Also share with all views
        view()->share('currentBrand', $brand);

        return $next($request);
    }
}
