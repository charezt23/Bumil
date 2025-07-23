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
Route::get('/posyandu/search', [PosyanduController::class, 'search']);
Route::get('/posyandu/with-balita-count/{posyanduId}', [PosyanduController::class, 'getWithBalitaCount']);

// Balita routes (development - tanpa middleware)
Route::apiResource('balita', BalitaController::class);
Route::get('/balita/posyandu/{posyandu_id}', [BalitaController::class, 'getAllBalitaByPosyandu']);
Route::get('/balita/posyandu/{posyandu_id}/user/{user_id}', [BalitaController::class, 'getAllBalitaByPosyanduAndUser']);
Route::get('/balita/search', [BalitaController::class, 'search']);
Route::get('/balita/user/{user_id}', [BalitaController::class, 'getAllBalitaByUser']);
Route::get('/balita/aktif/{posyandu_id}', [BalitaController::class, 'getBalitaAktifByPosyandu']);
Route::get('/balita/inaktif/{posyandu_id}', [BalitaController::class, 'getBalitaInAktifByPosyandu']);
Route::get('/balita/notimunisasi/{posyandu_id}', [BalitaController::class, 'getAllBalitaWithNotImunisasi']);
Route::get('/balita/notimunisasi/user/{user_id}', [BalitaController::class, 'getAllBalitaWithNotImunisasiByUser']);

// Kunjungan Balita routes (development - tanpa middleware)
Route::apiResource('kunjungan-balita', Kunjungan_BalitaController::class);
Route::get('/kunjungan-balita/balita/{balitaId}', [Kunjungan_BalitaController::class, 'getByBalita']);

// Imunisasi routes (development - tanpa middleware)
Route::apiResource('imunisasi', ImunisasiController::class);
Route::get('/imunisasi/balita/{balitaId}', [ImunisasiController::class, 'GetImunisasibyBalita']);
Route::get('/imunisasi/user/{user_id}', [ImunisasiController::class, 'getImunisasiByUser']);

// Kematian routes (development - tanpa middleware)
Route::apiResource('kematian', KematianController::class);
Route::get('/kematian/balita/{balitaId}', [KematianController::class, 'getByBalita']);
Route::get('/kematian/user/{user_id}', [KematianController::class, 'getKematianByUser']);


// Protected routes (memerlukan autentikasi dengan token)
Route::middleware('api.token')->group(function () {
    // Auth routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/posyandu/user/{userId}', [PosyanduController::class, 'getByUser']);
    Route::apiResource('posyandu', PosyanduController::class);
});

// Test route untuk memastikan API berjalan
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now()
    ]);
});
