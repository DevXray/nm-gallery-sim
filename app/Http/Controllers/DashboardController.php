<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $barangTersedia = Barang::where('status_barang', 'Tersedia')->count();
        $barangDisewa = Barang::where('status_barang', 'Disewa')->count();
        $barangLaundry = Barang::where('status_barang', 'Laundry')->count();
        $barangRusak = Barang::where('status_barang', 'Rusak')->count();
        
        $transaksiAktif = Transaksi::where('status_transaksi', 'Diproses')->count();
        
        $pendapatanHariIni = Transaksi::whereDate('created_at', today())
            ->sum('total_biaya');
        
        $transaksiTerbaru = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboard.index', compact(
            'totalBarang', 
            'barangTersedia',
            'barangDisewa',
            'barangLaundry',
            'barangRusak',
            'transaksiAktif',
            'pendapatanHariIni', 
            'transaksiTerbaru'
        ));
    }
}