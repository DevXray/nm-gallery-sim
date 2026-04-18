<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;

// Route root (/) langsung ke halaman login
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// Guest routes (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Auth routes (sudah login)
Route::middleware('auth.session')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // ONLY OWNER
    Route::middleware(['role:Owner'])->group(function () {
        Route::resource('barang', BarangController::class);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
         
Route::post('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.update.profile');
Route::post('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.update.password');
    });
    
    // ONLY KARYAWAN
    Route::middleware(['role:Karyawan'])->group(function () {
        Route::resource('transaksi', TransaksiController::class);
    });
    
    // SEMUA ROLE
    Route::resource('pelanggan', PelangganController::class);
});