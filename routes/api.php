<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PosyanduController;
use App\Http\Middleware\ApiTokenAuth;

// Public routes (tidak memerlukan autentikasi)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Posyandu routes (development - tanpa middleware)
Route::apiResource('posyandu', PosyanduController::class);
Route::get('/posyandu/user/{userId}', [PosyanduController::class, 'getByUser']);

// Protected routes (memerlukan autentikasi dengan token)
Route::middleware('api.token')->group(function () {
    // Auth routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Test route untuk memastikan API berjalan
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()
    ]);
});
