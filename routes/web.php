<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────

Route::get('/', fn() => redirect()->route('workspace.create'));

// Workspace registration
Route::get('/get-started', [WorkspaceController::class, 'create'])->name('workspace.create');
Route::post('/get-started', [WorkspaceController::class, 'store'])->name('workspace.store');

// Auth
Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout',[AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// ── Authenticated — workspace-level ───────────────────────────────────────────

Route::middleware(['auth', 'workspace.active'])->group(function () {

    // Brand switcher / home — redirect to first brand or brand creation
    Route::get('/home', [WorkspaceController::class, 'home'])->name('home');

    // ── Brand-scoped routes — all prefixed with /{brand} ─────────────────────
    Route::prefix('{brand}')->middleware('brand')->group(function () {

        Route::get('/dashboard',   [DashboardController::class, 'index'])->name('dashboard');

        // Module placeholders — filled by later modules
        Route::get('/create',      fn() => view('create.index'))->name('create');
        Route::get('/plan',        fn() => view('plan.index'))->name('plan');
        Route::get('/schedule',    fn() => view('schedule.index'))->name('schedule');
        Route::get('/grow',        fn() => view('grow.index'))->name('grow');
        Route::get('/results',     fn() => view('results.index'))->name('results');
        Route::get('/my-brand',    fn() => view('my-brand.index'))->name('my-brand');
        Route::get('/connections', fn() => view('connections.index'))->name('connections');
        Route::get('/ai-presence', fn() => view('ai-presence.index'))->name('ai-presence');
    });
});

require __DIR__.'/auth.php';
