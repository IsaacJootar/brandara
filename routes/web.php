<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CanvaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
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

// ── Canva webhook — no auth, Canva calls this directly ───────────────────────
Route::post('/webhooks/canva/{brand}', [CanvaController::class, 'webhook'])->name('canva.webhook');

// ── Authenticated — workspace-level ───────────────────────────────────────────

// ── Web Push subscription ─────────────────────────────────────────────────────
Route::middleware('auth')->post('/push/subscribe', function (Request $request) {
    $request->user()->updatePushSubscription(
        $request->input('endpoint'),
        $request->input('keys.p256dh'),
        $request->input('keys.auth')
    );

    return response()->json(['ok' => true]);
})->name('push.subscribe');

Route::middleware(['auth', 'workspace.active'])->group(function () {

    Route::get('/home', [WorkspaceController::class, 'home'])->name('home');

    // ── Brand management ──────────────────────────────────────────────────────
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brand.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brand.store');

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
        Route::get('/create/tiktok', fn () => view('create.tiktok', ['brand' => currentBrand()]))->name('create.tiktok');
        Route::get('/create/carousel', fn () => view('create.carousel', ['brand' => currentBrand()]))->name('create.carousel');
        Route::get('/create/whatsapp', fn () => view('create.whatsapp', ['brand' => currentBrand()]))->name('create.whatsapp');
        Route::get('/media', fn () => view('media.index', ['brand' => currentBrand()]))->name('media');
        Route::post('/canva/link', [CanvaController::class, 'link'])->name('canva.link');
        Route::get('/plan', fn () => view('plan.index'))->name('plan');
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
        Route::get('/grow', fn () => view('grow.index'))->name('grow');
        Route::get('/results', fn () => view('results.index'))->name('results');
        Route::get('/my-brand', fn () => view('my-brand.index'))->name('my-brand');
        Route::get('/trends', fn () => view('trends.index'))->name('trends');
        Route::get('/ai-presence', fn () => view('ai-presence.index'))->name('ai-presence');
    });
});

require __DIR__.'/auth.php';
