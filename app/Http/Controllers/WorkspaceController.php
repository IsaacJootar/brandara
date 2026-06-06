<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
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
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:255'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'country'        => ['required', 'string', 'size:2'],
        ]);

        $slug = Str::slug($data['workspace_name']);
        $baseSlug = $slug;
        $i = 2;
        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $workspace = Workspace::create([
            'name'                  => $data['workspace_name'],
            'slug'                  => $slug,
            'owner_email'           => $data['email'],
            'country'               => strtoupper($data['country']),
            'timezone'              => $this->timezoneForCountry(strtoupper($data['country'])),
            'plan'                  => 'starter',
            'subscription_status'   => 'trialing',
            'trial_ends_at'         => now()->addDays(7),
        ]);

        // Register the full domain for tenant routing
        $centralHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
        $workspace->domains()->create(['domain' => $slug . '.' . $centralHost]);

        // Create the owner user inside the tenant database
        tenancy()->initialize($workspace);

        $user = \App\Models\User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'owner',
        ]);

        tenancy()->end();

        // Redirect to tenant login
        $tenantHost = $slug . '.localhost:8000';
        return redirect("http://{$tenantHost}/login")->with('workspace_created', true);
    }

    private function timezoneForCountry(string $country): string
    {
        return match($country) {
            'GH' => 'Africa/Accra',
            'KE' => 'Africa/Nairobi',
            'ZA' => 'Africa/Johannesburg',
            default => 'Africa/Lagos',
        };
    }
}
