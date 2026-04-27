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
        
        // Query transaksi berdasarkan range tanggal
        $query = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])
            ->whereBetween('tgl_sewa', [$startDate, $endDate]);
        
        $transaksis = $query->orderBy('tgl_sewa', 'desc')->get();
        
        // ========== DATA GRAFIK BERDASARKAN FILTER ==========
        $chartLabels = [];
        $chartData = [];
        
        if ($filter == 'harian') {
            // GRAFIK HARIAN: per jam (00-23)
             for ($hour = 0; $hour <= 23; $hour++) {
        $chartLabels[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        
        $total = Transaksi::whereDate('tgl_sewa', $startDate)
            ->whereRaw('HOUR(tgl_sewa) = ?', [$hour])
            ->sum('total_biaya');
        
        // DEBUG: tampilkan di log
        if ($total > 0) {
            \Log::info("Jam " . $hour . ": Rp " . number_format($total));
        }
        
        $chartData[] = (int) $total;
    }
}
        elseif ($filter == 'mingguan') {
            // GRAFIK MINGGUAN: per hari (Senin - Minggu)
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));
            
            // Inisialisasi data per hari
            $dailyData = [];
            foreach ($days as $day) {
                $dailyData[$day] = 0;
            }
            
            // Hitung pendapatan per hari
            foreach ($period as $date) {
                $dayName = $this->getDayNameIndonesian($date->format('N'));
                $total = Transaksi::whereDate('tgl_sewa', $date->format('Y-m-d'))
                    ->whereBetween('tgl_sewa', [$startDate, $endDate])
                    ->sum('total_biaya');
                $dailyData[$dayName] += (int) $total;
            }
            
            $chartLabels = $days;
            $chartData = array_values($dailyData);
        } 
       else {
    // GRAFIK BULANAN: per tanggal (1 - 31)
    $start = new \DateTime($startDate);
    $bulan = $start->format('m');
    $tahun = $start->format('Y');
    
    // Jumlah hari dalam bulan
    $daysInMonth = date('t', strtotime($startDate));
    
    $chartLabels = [];
    $chartData = [];
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $chartLabels[] = (string)$day;
        
        $total = Transaksi::whereYear('tgl_sewa', $tahun)
            ->whereMonth('tgl_sewa', $bulan)
            ->whereDay('tgl_sewa', $day)
            ->sum('total_biaya');
        
        $chartData[] = (int) $total;
    }
}
        
        // ========== STATISTIK UNTUK CARD ==========
        $totalTransaksi = Transaksi::count();
        $totalPendapatan = Transaksi::sum('total_biaya') + Transaksi::sum('total_denda');
        $totalDenda = Transaksi::sum('total_denda');
        $totalBarang = Barang::count();
        $totalPelanggan = Pelanggan::count();
        $transaksiAktif = Transaksi::where('status_transaksi', 'Diproses')->count();
        
        // Ringkasan periode yang dipilih
        $totalTransaksiPeriode = $transaksis->count();
        $totalPendapatanPeriode = $transaksis->sum('total_biaya');
        $totalDendaPeriode = $transaksis->sum('total_denda');
        
        // Ringkasan bulan ini (keseluruhan)
        $totalTransaksiBulanIni = Transaksi::whereMonth('tgl_sewa', date('m'))
            ->whereYear('tgl_sewa', date('Y'))
            ->count();
        $pendapatanBulanIni = Transaksi::whereMonth('tgl_sewa', date('m'))
            ->whereYear('tgl_sewa', date('Y'))
            ->sum('total_biaya');
        
        $totalSelesai = Transaksi::where('status_transaksi', 'Selesai')->count();
        $totalTransaksiAll = Transaksi::count();
        $tepatWaktu = $totalTransaksiAll > 0 ? round(($totalSelesai / $totalTransaksiAll) * 100) : 100;
        
        return view('laporan.index', compact(
            'transaksis', 'totalTransaksi', 'totalPendapatan', 'totalDenda',
            'totalBarang', 'totalPelanggan', 'transaksiAktif',
            'chartLabels', 'chartData',
            'totalTransaksiPeriode', 'totalPendapatanPeriode', 'totalDendaPeriode',
            'totalTransaksiBulanIni', 'pendapatanBulanIni', 'tepatWaktu',
            'filter', 'startDate', 'endDate'
        ));
    }
    
    // Helper function untuk konversi hari ke Bahasa Indonesia
    private function getDayNameIndonesian($dayNumber)
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        return $days[$dayNumber] ?? 'Senin';
    }
    
    public function exportExcel(Request $request)
    {
        $filter = $request->get('filter', 'bulanan');
        $startDate = $request->get('start', date('Y-m-01'));
        $endDate = $request->get('end', date('Y-m-d'));
        
        $query = Transaksi::with(['pelanggan', 'detailTransaksis.barang']);
        
        if ($filter == 'harian') {
            $query->whereDate('tgl_sewa', $startDate);
        } elseif ($filter == 'mingguan') {
            $query->whereBetween('tgl_sewa', [$startDate, $endDate]);
        } else {
            $bulan = date('m', strtotime($startDate));
            $tahun = date('Y', strtotime($startDate));
            $query->whereMonth('tgl_sewa', $bulan)
                  ->whereYear('tgl_sewa', $tahun);
        }
        
        $transaksis = $query->orderBy('tgl_sewa', 'desc')->get();
        
        $filename = 'laporan_transaksi_' . date('Y-m-d') . '.xls';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        echo '<html>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<body>';
        
        echo '<h2>LAPORAN TRANSAKSI NM GALLERY</h2>';
        echo '<p>Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . '</p>';
        echo '<p>Filter: ' . ucfirst($filter) . '</p>';
        echo '<p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>';
        
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<thead>';
        echo '<tr style="background-color: #C9A84C; color: white;">';
        echo '<th>No</th>';
        echo '<th>No. Transaksi</th>';
        echo '<th>Pelanggan</th>';
        echo '<th>No. Telepon</th>';
        echo '<th>Barang</th>';
        echo '<th>Ukuran</th>';
        echo '<th>Tanggal Sewa</th>';
        echo '<th>Tanggal Jatuh Tempo</th>';
        echo '<th>Tanggal Kembali</th>';
        echo '<th>Durasi (hari)</th>';
        echo '<th>Total Biaya</th>';
        echo '<th>Denda</th>';
        echo '<th>Grand Total</th>';
        echo '<th>Status</th>';
        echo '</tr>';
        echo '</thead><tbody>';
        
        $no = 1;
        $grandTotal = 0;
        
        foreach($transaksis as $item) {
            $detail = $item->detailTransaksis->first();
            $tglSewa = \Carbon\Carbon::parse($item->tgl_sewa);
            $tglJatuh = \Carbon\Carbon::parse($item->tgl_jatuh_tempo);
            $durasi = $tglSewa->diffInDays($tglJatuh);
            $totalDenda = $item->total_denda ?? 0;
            $grandTotalItem = $item->total_biaya + $totalDenda;
            $grandTotal += $grandTotalItem;
            
            $statusText = '';
            if ($item->status_transaksi == 'Diproses') {
                $statusText = 'Aktif';
            } elseif ($item->status_transaksi == 'Selesai') {
                $statusText = 'Selesai';
            } else {
                $statusText = 'Terlambat';
            }
            
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>#TRX-' . str_pad($item->id_transaksi, 4, '0', STR_PAD_LEFT) . '</td>';
            echo '<td>' . ($item->pelanggan->nama_pelanggan ?? '-') . '</td>';
            echo '<td>' . ($item->pelanggan->no_telp ?? '-') . '</td>';
            echo '<td>' . ($detail->barang->nama_barang ?? '-') . '</td>';
            echo '<td>' . ($detail->ukuran ?? '-') . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($item->tgl_sewa)) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($item->tgl_jatuh_tempo)) . '</td>';
            echo '<td>' . ($item->tgl_kembali ? date('d/m/Y', strtotime($item->tgl_kembali)) : '-') . '</td>';
            echo '<td style="text-align:center">' . $durasi . '</td>';
            echo '<td style="text-align:right">Rp ' . number_format($item->total_biaya, 0, ',', '.') . '</td>';
            echo '<td style="text-align:right">Rp ' . number_format($totalDenda, 0, ',', '.') . '</td>';
            echo '<td style="text-align:right">Rp ' . number_format($grandTotalItem, 0, ',', '.') . '</td>';
            echo '<td>' . $statusText . '</td>';
            echo '</tr>';
        }
        
        echo '<tr style="background-color: #f5f5f5; font-weight: bold;">';
        echo '<td colspan="12" style="text-align:right"><strong>TOTAL KESELURUHAN</strong></td>';
        echo '<td style="text-align:right"><strong>Rp ' . number_format($grandTotal, 0, ',', '.') . '</strong></td>';
        echo '<td></td>';
        echo '</tr>';
        
        echo '</tbody></table>';
        echo '<p style="margin-top: 20px;"><em>Dicetak dari NM Gallery System - ' . date('d/m/Y H:i:s') . '</em></p>';
        echo '</body></html>';
        exit;
    }
    
    public function exportPDF(Request $request)
    {
        $filter = $request->get('filter', 'bulanan');
        $startDate = $request->get('start', date('Y-m-01'));
        $endDate = $request->get('end', date('Y-m-d'));
        
        $query = Transaksi::with(['pelanggan', 'detailTransaksis.barang']);
        
        if ($filter == 'harian') {
            $query->whereDate('tgl_sewa', $startDate);
        } elseif ($filter == 'mingguan') {
            $query->whereBetween('tgl_sewa', [$startDate, $endDate]);
        } else {
            $bulan = date('m', strtotime($startDate));
            $tahun = date('Y', strtotime($startDate));
            $query->whereMonth('tgl_sewa', $bulan)
                  ->whereYear('tgl_sewa', $tahun);
        }
        
        $transaksis = $query->orderBy('tgl_sewa', 'desc')->get();
        
        $grandTotal = 0;
        foreach($transaksis as $item) {
            $grandTotal += $item->total_biaya + ($item->total_denda ?? 0);
        }
        
        $data = [
            'transaksis' => $transaksis,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filter' => $filter,
            'grandTotal' => $grandTotal,
            'title' => 'Laporan Transaksi NM Gallery',
            'date' => date('d/m/Y H:i:s')
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan_transaksi_' . date('Y-m-d') . '.pdf');
    }
}