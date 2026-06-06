<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function create()
    {
        return view('workspace.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'workspace_name' => ['required', 'string', 'max:100'],
            'brand_name'     => ['required', 'string', 'max:100'],
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'country'        => ['required', 'string', 'max:5'],
        ]);

        // Unique workspace slug
        $wsSlug = Str::slug($data['workspace_name']);
        $base = $wsSlug; $i = 2;
        while (Workspace::where('slug', $wsSlug)->exists()) {
            $wsSlug = $base . '-' . $i++;
        }

        $workspace = Workspace::create([
            'name'                => $data['workspace_name'],
            'slug'                => $wsSlug,
            'owner_email'         => $data['email'],
            'country'             => strtoupper($data['country']),
            'timezone'            => $this->timezoneFor(strtoupper($data['country'])),
            'plan'                => 'starter',
            'subscription_status' => 'trialing',
            'trial_ends_at'       => now()->addDays(7),
        ]);

        $user = $workspace->users()->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'owner',
        ]);

        // Create the first brand (same name as workspace by default)
        $brandSlug = Str::slug($data['brand_name']);
        $brand = $workspace->brands()->create([
            'name'     => $data['brand_name'],
            'slug'     => $brandSlug,
            'language' => 'en',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard', ['brand' => $brand->slug])
            ->with('success', 'Welcome to Brandara! Your workspace is ready.');
    }

    // Redirect logged-in user to their first brand's dashboard
    public function home()
    {
        $brand = auth()->user()->workspace->brands()->first();

        if (! $brand) {
            return redirect()->route('workspace.create');
        }

        return redirect()->route('dashboard', ['brand' => $brand->slug]);
    }

    private function timezoneFor(string $country): string
    {
        return match($country) {
            'GH' => 'Africa/Accra',
            'KE' => 'Africa/Nairobi',
            'ZA' => 'Africa/Johannesburg',
            default => 'Africa/Lagos',
        };
    }
}
