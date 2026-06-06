<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Landing page → workspace registration
Route::get('/', function () {
    return redirect()->route('workspace.create');
});

// Workspace registration (central domain)
Route::get('/get-started', [WorkspaceController::class, 'create'])->name('workspace.create');
Route::post('/get-started', [WorkspaceController::class, 'store'])->name('workspace.store');
