<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        // Brand is resolved by ResolveBrand middleware and bound to container
        /** @var \App\Models\Brand $brand */
        $brand     = app('current.brand');
        $workspace = auth()->user()->workspace;

        // All queries scoped to brand_id — data cannot leak to other brands
        $postsThisMonth    = $brand->posts()->whereMonth('published_at', now()->month)->where('status', 'published')->count();
        $activeConnections = $brand->platformConnections()->where('status', 'connected')->count();
        $warmLeads         = $brand->leads()->where('tag', 'warm_lead')->count();

        return view('dashboard', compact('brand', 'workspace', 'postsThisMonth', 'activeConnections', 'warmLeads'));
    }
}
