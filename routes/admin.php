<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Matches Management
    Route::resource('matches', MatchController::class)->names([
        'index' => 'admin.matches.index',
        'create' => 'admin.matches.create',
        'store' => 'admin.matches.store',
        'edit' => 'admin.matches.edit',
        'update' => 'admin.matches.update',
        'destroy' => 'admin.matches.destroy',
    ]);
});

// Super Admin Routes
Route::middleware(['web', 'auth', 'super_admin'])->prefix('admin')->group(function () {
    // Admin Users Management
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
});
