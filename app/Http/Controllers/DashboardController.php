<?php

namespace App\Http\Controllers;

use App\Models\Brand;

class DashboardController extends Controller
{
    public function index()
    {
        // Brand is resolved by ResolveBrand middleware and bound to container
        /** @var Brand $brand */
        // Metrics now load lazily via App\Livewire\Dashboard\Metrics — no eager DB hits here.
        $brand = app('current.brand');
        $workspace = auth()->user()->workspace;

        return view('dashboard', compact('brand', 'workspace'));
    }
}
