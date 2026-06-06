<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $workspace = tenant();
        $user      = auth()->user();

        $trialDaysLeft = $workspace->trial_ends_at
            ? max(0, (int) now()->diffInDays($workspace->trial_ends_at, false))
            : null;

        return view('dashboard', compact('workspace', 'user', 'trialDaysLeft'));
    }
}
