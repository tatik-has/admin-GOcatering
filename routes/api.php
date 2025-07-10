<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaketanController;
use App\Http\Controllers\MenuController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ======================== AUTH =========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ======================= MENU ==========================
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/menu/{id}', [MenuController::class, 'show']); // Detail menu berdasarkan ID

// ======================= PAKETAN =======================
Route::get('/paketan', [PaketanController::class, 'index']);
Route::get('/paketan/{id}', [PaketanController::class, 'show']); // Detail paketan berdasarkan ID

// ======================= KULINER ========================
Route::get('/kuliner', [MenuController::class, 'kuliner']);
Route::get('/kuliner/{id}', [MenuController::class, 'showKuliner']);

// ======================= KATERING =======================
Route::get('/katering', [MenuController::class, 'katering']);
Route::get('/katering/{id}', [MenuController::class, 'showKatering']);

// =================== PAKET BULANAN ======================
Route::get('/paket-bulanan', [PaketanController::class, 'paketBulanan']);
Route::get('/paket-bulanan/{id}', [PaketanController::class, 'showPaketBulanan']);

// ======================= PEMESANAN =====================
Route::post('/pesanan', [pemesananController::class, 'store'])->middleware('auth:sanctum');
Route::get('/pesanan', [pemesananController::class, 'index'])->middleware('auth:sanctum');
Route::get('/pesanan/{order}', [pemesananController::class, 'show'])->middleware('auth:sanctum');

// ======================= PESANAN (ADMIN FILAMENT) =======
// Route khusus untuk Filament Admin - tanpa auth karena sudah di-handle di Filament
// Route::get('/pesanan', [PemesananController::class, 'getAllForAdmin']);