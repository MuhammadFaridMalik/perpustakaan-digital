<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Operasional — Admin & Super Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('books', BookController::class);
        Route::post('books/{book}/copies', [BookCopyController::class, 'store'])->name('book-copies.store');
        Route::patch('book-copies/{copy}/status', [BookCopyController::class, 'updateStatus'])->name('book-copies.update-status');
        Route::delete('book-copies/{copy}', [BookCopyController::class, 'destroy'])->name('book-copies.destroy');

        Route::get('master/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('master/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('master/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('master/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('master/authors', [AuthorController::class, 'index'])->name('authors.index');
        Route::post('master/authors', [AuthorController::class, 'store'])->name('authors.store');
        Route::put('master/authors/{author}', [AuthorController::class, 'update'])->name('authors.update');
        Route::delete('master/authors/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');

        Route::get('master/publishers', [PublisherController::class, 'index'])->name('publishers.index');
        Route::post('master/publishers', [PublisherController::class, 'store'])->name('publishers.store');
        Route::put('master/publishers/{publisher}', [PublisherController::class, 'update'])->name('publishers.update');
        Route::delete('master/publishers/{publisher}', [PublisherController::class, 'destroy'])->name('publishers.destroy');

        Route::get('master/racks', [RackController::class, 'index'])->name('racks.index');
        Route::post('master/racks', [RackController::class, 'store'])->name('racks.store');
        Route::put('master/racks/{rack}', [RackController::class, 'update'])->name('racks.update');
        Route::delete('master/racks/{rack}', [RackController::class, 'destroy'])->name('racks.destroy');

        Route::get('members', [MemberController::class, 'index'])->name('members.index');
        Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::get('members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::patch('members/{member}/toggle-active', [MemberController::class, 'toggleActive'])->name('members.toggle-active');
    });

    // Khusus Super Admin
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('admins', AdminManagementController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('admins/{admin}/toggle-active', [AdminManagementController::class, 'toggleActive'])
            ->name('admins.toggle-active');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SystemSettingController::class, 'update'])->name('settings.update');
    });
});
