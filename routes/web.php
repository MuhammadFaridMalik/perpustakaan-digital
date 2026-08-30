<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('admins', AdminManagementController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('admins/{admin}/toggle-active', [AdminManagementController::class, 'toggleActive'])
            ->name('admins.toggle-active');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::get('settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SystemSettingController::class, 'update'])->name('settings.update');
    });
});
