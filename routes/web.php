<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,pegawai'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| Admin-only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Akan diisi: kelola pegawai, layanan, laporan
});

/*
|--------------------------------------------------------------------------
| Pegawai Routes (pegawai dan admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,pegawai'])->group(function () {
    // Akan diisi: transaksi, pembayaran, status cucian, pelanggan
});
