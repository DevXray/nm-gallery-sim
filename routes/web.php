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
    
    // SEMUA ROLE (READ ONLY untuk barang)
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    
    // ONLY OWNER (CREATE, UPDATE, DELETE)
    Route::middleware(['role:Owner'])->group(function () {
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
        
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        
        Route::post('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.update.profile');
        Route::post('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.update.password');
        Route::post('/pengaturan/tambah-user', [PengaturanController::class, 'tambahUser'])->name('pengaturan.tambah.user');
        Route::post('/pengaturan/update-tarif', [PengaturanController::class, 'updateTarif'])->name('pengaturan.update.tarif');
        Route::post('/pengaturan/update-profil-toko', [PengaturanController::class, 'updateProfilToko'])->name('pengaturan.update.profil_toko');
    });
    
    // ONLY KARYAWAN
    Route::middleware(['role:Karyawan'])->group(function () {
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::put('/transaksi/{transaksi}', [TransaksiController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
        
        // Cetak PDF transaksi
        Route::get('/transaksi/{id}/print', [TransaksiController::class, 'printPdf'])->name('transaksi.print');
        Route::post('/transaksi/preview-pdf', [TransaksiController::class, 'previewPdf'])->name('transaksi.preview-pdf');
    });
    
    // SEMUA ROLE - Pelanggan
    Route::get('/pelanggan/export-pdf', [PelangganController::class, 'exportPDF'])->name('pelanggan.export.pdf');
    Route::resource('pelanggan', PelangganController::class);
});