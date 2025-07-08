<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\ImunisasiController;
use App\Http\Controllers\KematianController;
use App\Http\Controllers\Kunjungan_BalitaController;
use App\Http\Middleware\ApiTokenAuth;

// Public routes (tidak memerlukan autentikasi)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Posyandu routes (development - tanpa middleware)
Route::apiResource('posyandu', PosyanduController::class);
Route::get('/posyandu/user/{userId}', [PosyanduController::class, 'getByUser']);
Route::get('/posyandu/search', [PosyanduController::class, 'search']);
Route::get('/posyandu/with-balita-count/{posyanduId}', [PosyanduController::class, 'getWithBalitaCount']);

// Balita routes (development - tanpa middleware)
Route::apiResource('balita', BalitaController::class);
Route::get('/balita/posyandu/{posyandu_id}', [BalitaController::class, 'getByPosyandu']);
Route::get('/balita/search', [BalitaController::class, 'search']);
Route::get('/balita/aktif/{posyandu_id}', [BalitaController::class, 'getAktifByUser']);
Route::get('/balita/inaktif/{user_id}', [BalitaController::class, 'getInaktifByPosyandu']);

// Kunjungan Balita routes (development - tanpa middleware)
Route::apiResource('kunjungan-balita', Kunjungan_BalitaController::class);
Route::get('/kunjungan-balita/balita/{balitaId}', [Kunjungan_BalitaController::class, 'getByBalita']);

// Imunisasi routes (development - tanpa middleware)
Route::apiResource('imunisasi', ImunisasiController::class);
Route::get('/imunisasi/balita/{balitaId}', [ImunisasiController::class, 'GetImunisasibyBalita']);

// Kematian routes (development - tanpa middleware)
Route::apiResource('kematian', KematianController::class);
Route::get('/kematian/balita/{balitaId}', [KematianController::class, 'getByBalita']);


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
