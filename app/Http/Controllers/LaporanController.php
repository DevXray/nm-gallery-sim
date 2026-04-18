<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Filter periode
        $periode = $request->get('periode', 'bulanan');
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        
        // Data statistik
        $totalTransaksi = Transaksi::count();
        $totalPendapatan = Transaksi::sum('total_biaya');
        $totalDenda = Transaksi::sum('total_denda');
        $totalBarang = Barang::count();
        $totalPelanggan = Pelanggan::count();
        
        // Transaksi aktif (sedang disewa)
        $transaksiAktif = Transaksi::where('status_transaksi', 'Diproses')->count();
        
        // Data untuk chart (pendapatan per bulan)
        $pendapatanPerBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $total = Transaksi::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->sum('total_biaya');
            $pendapatanPerBulan[] = $total;
        }
        
        // Data transaksi terbaru
        $transaksiTerbaru = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])
            ->latest()
            ->take(10)
            ->get();
        
        // Data untuk filter
        $tahunList = Transaksi::select(DB::raw('YEAR(created_at) as tahun'))
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        
        return view('laporan.index', compact(
            'totalTransaksi',
            'totalPendapatan',
            'totalDenda',
            'totalBarang',
            'totalPelanggan',
            'transaksiAktif',
            'pendapatanPerBulan',
            'transaksiTerbaru',
            'tahunList',
            'periode',
            'tahun',
            'bulan'
        ));
    }
    
    public function exportExcel()
    {
        // Akan diimplementasikan nanti
        return back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
    }
    
    public function exportPdf()
    {
        // Akan diimplementasikan nanti
        return back()->with('info', 'Fitur export PDF sedang dalam pengembangan');
    }
}