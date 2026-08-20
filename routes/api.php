<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;

// Route Public (Tanpa Login)
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route Private (Wajib Pakai Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route CRUD Kategori & Produk (Otomatis handle GET, POST, PUT, DELETE)
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('produk', ProdukController::class);

    // Route Transaksi Pesanan
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);

});