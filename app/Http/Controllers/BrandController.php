<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\Plan\PlanFeatureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private readonly PlanFeatureService $plan) {}

    /**
     * Show the create brand form.
     */
    public function create(): View|RedirectResponse
    {
        $workspace = auth()->user()->workspace;

        if ($this->plan->isBrandLimitReached($workspace)) {
            $limit = $this->plan->brandLimit($workspace->plan);
            $planLabel = $this->plan->planLabel($workspace->plan);

            return redirect()->route('home')->with(
                'error',
                "You've reached the {$limit}-brand limit on the {$planLabel} plan. Upgrade to add more brands."
            );
        }

        // Share the first brand so the app layout nav can render
        $firstBrand = $workspace->brands()->first();
        if ($firstBrand) {
            view()->share('currentBrand', $firstBrand);
        }

        return view('brands.create');
    }

    /**
     * Store a new brand.
     */
    public function store(Request $request): RedirectResponse
    {
        $workspace = auth()->user()->workspace;

        if ($this->plan->isBrandLimitReached($workspace)) {
            return back()->withErrors(['name' => 'Brand limit reached. Upgrade your plan to add more brands.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $slug = Str::slug($data['name']);

        // Ensure unique slug within workspace
        $base = $slug;
        $i = 2;
        while (Brand::where('workspace_id', $workspace->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $brand = $workspace->brands()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'language' => $workspace->language ?? 'en',
        ]);

        return redirect()
            ->route('my-brand', ['brand' => $brand->slug])
            ->with('success', 'Brand created. Set up your Brand Kit and Brand Identity to get started.');
    }
}
