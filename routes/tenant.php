<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Tenant auth routes
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('tenant.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('tenant.logout');

    // Authenticated tenant routes
    Route::middleware(['auth', 'tenant.active'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Module placeholders — filled in by later modules
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
