<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\BorrowingController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API perpustakaan sekolah berjalan',
    ]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes — wajib login (role apapun)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:siswa')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

        Route::get('/borrowings', [BorrowingController::class, 'index']);
    });

    // Contoh route khusus admin (admin + super_admin lolos)
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/ping', function () {
            return response()->json([
                'success' => true,
                'message' => 'Halo Admin, akses diterima.',
            ]);
        });
    });

    // Contoh route khusus super admin (hanya super_admin yang lolos)
    Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
        Route::get('/ping', function () {
            return response()->json([
                'success' => true,
                'message' => 'Halo Super Admin, akses tertinggi diterima.',
            ]);
        });
    });
});
