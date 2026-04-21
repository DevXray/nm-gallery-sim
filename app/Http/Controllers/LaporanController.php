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
        $filter = $request->get('filter', 'bulanan');
        $startDate = $request->get('start', date('Y-m-01'));
        $endDate = $request->get('end', date('Y-m-d'));
        
        // Query transaksi berdasarkan filter
        $query = Transaksi::with(['pelanggan', 'detailTransaksis.barang']);
        
        if ($filter == 'harian') {
            $query->whereDate('created_at', $startDate);
        } elseif ($filter == 'mingguan') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query->whereYear('created_at', date('Y'))
                  ->whereMonth('created_at', date('m'));
        }
        
        $transaksis = $query->latest()->get();
        
        // Statistik
        $totalTransaksi = Transaksi::count();
        $totalPendapatan = Transaksi::sum('total_biaya');
        $totalDenda = Transaksi::sum('total_denda');
        $totalBarang = Barang::count();
        $totalPelanggan = Pelanggan::count();
        $transaksiAktif = Transaksi::where('status_transaksi', 'Diproses')->count();
        
        // Data untuk grafik (6 bulan terakhir)
        $bulanLabels = [];
        $dataPendapatan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("-$i months"));
            $bulanLabels[] = date('M', strtotime($bulan));
            $total = Transaksi::whereYear('created_at', date('Y', strtotime($bulan)))
                ->whereMonth('created_at', date('m', strtotime($bulan)))
                ->sum('total_biaya');
            $dataPendapatan[] = $total;
        }
        
        // Ringkasan bulan ini
        $totalTransaksiBulanIni = Transaksi::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();
        $pendapatanBulanIni = Transaksi::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total_biaya');
        
        $totalSelesai = Transaksi::where('status_transaksi', 'Selesai')->count();
        $totalTransaksiAll = Transaksi::count();
        $tepatWaktu = $totalTransaksiAll > 0 ? round(($totalSelesai / $totalTransaksiAll) * 100) : 100;
        
        return view('laporan.index', compact(
            'transaksis', 'totalTransaksi', 'totalPendapatan', 'totalDenda',
            'totalBarang', 'totalPelanggan', 'transaksiAktif',
            'bulanLabels', 'dataPendapatan',
            'totalTransaksiBulanIni', 'pendapatanBulanIni', 'tepatWaktu',
            'filter', 'startDate', 'endDate'
        ));
    }
    
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start', date('Y-m-01'));
        $endDate = $request->get('end', date('Y-m-d'));
        
        $transaksis = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $html = '<table border="1"><thead>';
        $html .= '<tr><th>No Transaksi</th><th>Pelanggan</th><th>Barang</th>';
        $html .= '<th>Tanggal Sewa</th><th>Tanggal Kembali</th><th>Total</th><th>Status</th></tr></thead><tbody>';
        
        foreach($transaksis as $item) {
            $html .= '<tr>';
            $html .= '<td>#TRX-'.$item->id_transaksi.'</td>';
            $html .= '<td>'.($item->pelanggan->nama_pelanggan ?? '-').'</td>';
            $html .= '<td>'.($item->detailTransaksis->first()->barang->nama_barang ?? '-').'</td>';
            $html .= '<td>'.$item->tgl_sewa.'</td>';
            $html .= '<td>'.$item->tgl_jatuh_tempo.'</td>';
            $html .= '<td>Rp '.number_format($item->total_biaya, 0, ',', '.').'</td>';
            $html .= '<td>'.$item->status_transaksi.'</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="laporan_transaksi_'.date('Y-m-d').'.xls"');
        echo $html;
        exit;
    }
}