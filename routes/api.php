<?php

use App\Http\Controllers\KateringController;
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
Route::get('/kuliner', [KateringController::class, 'index']);
Route::get('/kuliner/{id}', [KateringController::class, 'show']);

// ======================= KATERING =======================
Route::get('/katering', [KateringController::class, 'index']); 
Route::get('/katering/{id}', [KateringController::class, 'show']); 

// =================== PAKET BULANAN ======================
Route::get('/paket-bulanan', [PaketanController::class, 'index']); // ambil semua
Route::get('/paket-bulanan/{id}', [PaketanController::class, 'show']); // ambil satu by id

// ======================= PEMESANAN =====================
Route::post('/pesanan', [PemesananController::class, 'store'])->middleware('auth:sanctum');
Route::get('/pesanan', [PemesananController::class, 'index'])->middleware('auth:sanctum');
Route::get('/pesanan/{order}', [PemesananController::class, 'show'])->middleware('auth:sanctum');
