<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────

Route::get('/', fn () => redirect()->route('workspace.create'));

Route::get('/get-started', [WorkspaceController::class, 'create'])->name('workspace.create');
Route::post('/get-started', [WorkspaceController::class, 'store'])->name('workspace.store');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// ── OAuth Callbacks — fixed URLs registered with each provider ────────────────
// These cannot have /{brand} prefix because providers require static redirect URIs.
// The brand is encoded in the OAuth state parameter.
Route::middleware(['auth', 'workspace.active'])
    ->get('/oauth/callback/{platform}', [PlatformController::class, 'callback'])
    ->name('platform.callback');

// ── Authenticated — workspace-level ───────────────────────────────────────────

Route::middleware(['auth', 'workspace.active'])->group(function () {

    Route::get('/home', [WorkspaceController::class, 'home'])->name('home');

    // ── Brand-scoped routes ───────────────────────────────────────────────────
    Route::prefix('{brand}')->middleware('brand')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Connections (Platform OAuth) ──────────────────────────────────────
        Route::get('/connections', [PlatformController::class, 'index'])->name('connections');
        Route::get('/connections/{platform}/connect', [PlatformController::class, 'connect'])->name('platform.connect');
        Route::post('/connections/{platform}/disconnect', [PlatformController::class, 'disconnect'])->name('platform.disconnect');

        // ── Create (Post Composer) ────────────────────────────────────────────
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::delete('/create/{post}', [PostController::class, 'destroy'])->name('post.destroy');
        Route::get('/plan', fn () => view('plan.index'))->name('plan');
        Route::get('/schedule', fn () => view('schedule.index'))->name('schedule');
        Route::get('/grow', fn () => view('grow.index'))->name('grow');
        Route::get('/results', fn () => view('results.index'))->name('results');
        Route::get('/my-brand', fn () => view('my-brand.index'))->name('my-brand');
        Route::get('/ai-presence', fn () => view('ai-presence.index'))->name('ai-presence');
    });
});

require __DIR__.'/auth.php';
