@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('breadcrumb', 'Laporan Keuangan')

@section('content')
<div class="page active" id="page-reports">

    <div class="pg-head">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="pg-title"><i class="bi bi-bar-chart-line"></i> Laporan Keuangan</div>
                <div class="pg-sub">Rekap pendapatan dan penyewaan Baju Bodo</div>
            </div>
        </div>
    </div>

    <!-- Two chart summary cards -->
    <div class="reports-grid">
        <div class="card gold-top">
            <div class="card-head">
                <div>
                    <div class="card-title">
                        <i class="bi bi-graph-up-arrow"></i> Pendapatan 
                        @if($filter == 'harian')
                            Per Jam
                        @elseif($filter == 'mingguan')
                            Per Hari
                        @else
                            Per Tanggal
                        @endif
                    </div>
                    <div class="card-sub" id="chartSubtitle">
                        @if($filter == 'harian')
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} · per jam
                        @elseif($filter == 'mingguan')
                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }} · per hari
                        @else
                            {{ \Carbon\Carbon::parse($startDate)->format('M Y') }} · per tanggal
                        @endif
                        · dalam jutaan Rp
                    </div>
                </div>
            </div>
            <div class="bar-chart-wrap ai-style-change-2">
    <canvas id="pendapatanChart" class="ai-style-change-1" style="width: 457px; height: 84.5px; max-height: 180px; display: block; box-sizing: border-box;"></canvas>
</div>
        </div>

        <div class="card gold-top">
            <div class="card-head">
                <div>
                    <div class="card-title"><i class="bi bi-clipboard2-data"></i> Ringkasan Periode</div>
                    <div class="card-sub">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
                </div>
            </div>
            <div style="padding:14px 18px;display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8f9fa;border-radius:10px;border:1px solid #e8e8e8">
                    <div>
                        <div style="font-size:10px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.6px">Total Penyewaan</div>
                        <div style="font-size:24px;font-weight:800;color:#1a1a1a;margin-top:2px;">{{ $totalTransaksiPeriode ?? 0 }}</div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center"><i class="bi bi-bag-heart" style="font-size:18px;color:var(--gold-dk)"></i></div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:rgba(201,168,76,0.08);border-radius:10px;border:1px solid rgba(201,168,76,0.25)">
                    <div>
                        <div style="font-size:10px;color:#C9A84C;font-weight:600;text-transform:uppercase;letter-spacing:.6px">Total Pendapatan</div>
                        <div style="font-size:22px;font-weight:800;color:#C9A84C;margin-top:2px;font-family:monospace">Rp {{ number_format(($totalPendapatanPeriode ?? 0), 0, ',', '.') }}</div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.2);display:flex;align-items:center;justify-content:center"><i class="bi bi-cash-stack" style="font-size:18px;color:var(--gold-dk)"></i></div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:rgba(45,166,110,.05);border-radius:10px;border:1px solid rgba(45,166,110,.15)">
                    <div>
                        <div style="font-size:10px;color:#1a8050;font-weight:600;text-transform:uppercase;letter-spacing:.6px">Rata-rata per Hari</div>
                        <div style="font-size:20px;font-weight:800;color:#1a8050;margin-top:2px;font-family:monospace">
                            @php
                                $hariCount = max(1, \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1);
                                $rataRata = ($totalPendapatanPeriode ?? 0) / $hariCount;
                            @endphp
                            Rp {{ number_format($rataRata, 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(45,166,110,.08);border:1px solid rgba(45,166,110,.2);display:flex;align-items:center;justify-content:center"><i class="bi bi-activity" style="font-size:18px;color:#1a8050"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card gold-top" style="margin-bottom:20px">
        <div class="card-head">
            <div>
                <div class="card-title"><i class="bi bi-table"></i> Rincian Transaksi</div>
                <div class="card-sub">Filter berdasarkan periode dan tanggal</div>
            </div>
        </div>

        <div class="report-filter-row">
            <div class="period-toggle" id="periodToggle">
                <div class="pt-btn {{ $filter == 'harian' ? 'active' : '' }}" data-filter="harian"><i class="bi bi-clock"></i> Harian</div>
                <div class="pt-btn {{ $filter == 'mingguan' ? 'active' : '' }}" data-filter="mingguan"><i class="bi bi-calendar3-week"></i> Mingguan</div>
                <div class="pt-btn {{ $filter == 'bulanan' ? 'active' : '' }}" data-filter="bulanan"><i class="bi bi-calendar3"></i> Bulanan</div>
            </div>
            <input type="date" class="date-input" id="start_date" value="{{ $startDate ?? date('Y-m-01') }}">
            <span style="font-size:12px;color:#aaa">→</span>
            <input type="date" class="date-input" id="end_date" value="{{ $endDate ?? date('Y-m-d') }}">
            <button class="btn-gold" id="btnTerapkan"><i class="bi bi-funnel-fill"></i> Terapkan</button>
        </div>

        <div class="table-responsive">
            <table class="report-tbl">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Baju</th>
                        <th>Tanggal Sewa</th>
                        <th>Tanggal Kembali</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis ?? [] as $item)
                    <tr style="transition: all 0.2s ease;">
                        <td class="td-mono" style="font-size:11px;color:#C9A84C;font-weight:600;">#TRX-{{ str_pad($item->id_transaksi, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-weight:500">{{ $item->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td style="color:#666">
                            @php
                                $firstDetail = $item->detailTransaksis->first();
                                $namaBarang = '-';
                                if($firstDetail && $firstDetail->barang) {
                                    $namaBarang = $firstDetail->barang->nama_barang;
                                    if($firstDetail->ukuran) {
                                        $namaBarang .= ' <span style="font-size:9px;color:#C9A84C;">(' . $firstDetail->ukuran . ')</span>';
                                    }
                                }
                            @endphp
                            {!! $namaBarang !!}
                        </td>
                        <td class="td-mono" style="font-size:11.5px;color:#888">{{ \Carbon\Carbon::parse($item->tgl_sewa)->format('d/m/Y') }}</td>
                        <td class="td-mono" style="font-size:11.5px;color:#888">
                            @if($item->tgl_kembali)
                                {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') }}
                            @else
                                <span class="badge badge-out" style="font-size:9px;"><i class="bi bi-hourglass-split"></i> Belum Kembali</span>
                            @endif
                        </td>
                        <td class="td-mono" style="font-size:11.5px">
                            @php
                                $tglSewa = \Carbon\Carbon::parse($item->tgl_sewa);
                                $tglJatuh = \Carbon\Carbon::parse($item->tgl_jatuh_tempo);
                                $durasi = $tglSewa->diffInDays($tglJatuh);
                            @endphp
                            {{ $durasi }} hari
                        </td>
                        <td>
                            @if($item->status_transaksi == 'Diproses')
                                <span class="badge badge-out"><i class="bi bi-circle-fill" style="color:#d4900a;font-size:8px;vertical-align:2px"></i> Aktif</span>
                            @elseif($item->status_transaksi == 'Selesai')
                                <span class="badge badge-ready" style="background:rgba(45,166,110,.1);color:#1a8050;border:1px solid rgba(45,166,110,.25);border-radius:20px;"><i class="bi bi-check-circle-fill" style="color:#1a8050;font-size:10px"></i> Selesai</span>
                            @else
                                <span class="badge badge-damaged" style="background:rgba(220,80,60,.08);color:#c04030;border:1px solid rgba(220,80,60,.2);border-radius:20px;"><i class="bi bi-exclamation-triangle-fill" style="font-size:10px"></i> Terlambat</span>
                            @endif
                        </td>
                        <td class="td-mono td-gold" style="font-weight:700;">Rp {{ number_format(($item->total_biaya + ($item->total_denda ?? 0)), 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:50px;">
                            <div style="font-size:16px; color:#aaa;"><i class="bi bi-inbox"></i> Belum ada transaksi</div>
                            <div style="font-size:12px; color:#ccc; margin-top:8px;">Silakan buat transaksi terlebih dahulu</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-footer">
            <div>
                @php
                    $totalDisplay = 0;
                    foreach($transaksis ?? [] as $item) {
                        $totalDisplay += $item->total_biaya + ($item->total_denda ?? 0);
                    }
                @endphp
                <i class="bi bi-table"></i> Total Ditampilkan: <strong style="color:#1a1a1a">{{ count($transaksis ?? []) }} transaksi</strong> &nbsp;|&nbsp; 
                <i class="bi bi-cash-stack"></i> Total Pendapatan: <strong style="color:#C9A84C;font-family:monospace">Rp {{ number_format($totalDisplay, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Export Row -->
    <div class="export-row">
        <div class="export-text">
            <div class="export-title"><i class="bi bi-download"></i> Unduh Laporan</div>
            <div class="export-sub">Ekspor rekap keuangan untuk pencatatan Owner</div>
        </div>
        <div class="export-btns">
            <button class="btn-outline" id="btnExportExcel"><i class="bi bi-file-earmark-spreadsheet"></i> Export Excel</button>
            <button class="btn-gold" id="btnExportPDF"><i class="bi bi-file-earmark-pdf"></i> Export PDF</button>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ========== CHART - SESUAI UKURAN CANVAS ==========
const ctx = document.getElementById('pendapatanChart');

if (ctx) {
    const chartContext = ctx.getContext('2d');
    
    let chartLabels = @json($chartLabels);
    let chartData = @json($chartData);
    
    // Ambil ukuran canvas yang sudah ada dari HTML
    const canvasWidth = ctx.clientWidth;
    const canvasHeight = ctx.clientHeight;
    
    new Chart(chartContext, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pendapatan',
                data: chartData,
                backgroundColor: 'rgba(201, 168, 76, 0.7)',
                borderColor: '#C9A84C',
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: '#eee', drawBorder: false },
                    title: { display: false },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'rb';
                            return value;
                        },
                        font: { size: 9 }
                    }
                },
                x: {
                    grid: { display: false },
                    title: { display: false },
                    ticks: {
                        font: { size: 8 },
                        maxRotation: 45,
                        minRotation: 45,
                        autoSkip: true,
                        maxTicksLimit: 10
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}
   // ========== FORMAT TANGGAL LOKAL INDONESIA (WITA) ==========
function formatDateLocal(date) {
    let year = date.getFullYear();
    let month = String(date.getMonth() + 1).padStart(2, '0');
    let day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ========== UPDATE RANGE TANGGAL OTOMATIS ==========
function updateDateRangeByFilter(filter) {
    let today = new Date();
    let startDate = document.getElementById('start_date');
    let endDate = document.getElementById('end_date');
    
    if (filter === 'harian') {
        let dateStr = formatDateLocal(today);
        startDate.value = dateStr;
        endDate.value = dateStr;
    } 
    else if (filter === 'mingguan') {
        let day = today.getDay();
        let diffToMonday = (day === 0 ? 6 : day - 1);
        let monday = new Date(today);
        monday.setDate(today.getDate() - diffToMonday);
        let sunday = new Date(monday);
        sunday.setDate(monday.getDate() + 6);
        startDate.value = formatDateLocal(monday);
        endDate.value = formatDateLocal(sunday);
    } 
    else if (filter === 'bulanan') {
        let year = today.getFullYear();
        let month = today.getMonth();
        
        let start = new Date(year, month, 1);
        let end = new Date(year, month + 1, 0);
        
        startDate.value = formatDateLocal(start);
        endDate.value = formatDateLocal(end);
        
        console.log('Bulanan WITA - start:', startDate.value, 'end:', endDate.value);
    }
}
// ========== FUNGSI FILTER ==========
function applyFilter() {
    let activeFilter = document.querySelector('.period-toggle .pt-btn.active');
    let filter = activeFilter ? activeFilter.getAttribute('data-filter') : 'bulanan';
    
    // Ambil nilai dari input (sudah diupdate)
    let startDate = document.getElementById('start_date').value;
    let endDate = document.getElementById('end_date').value;
    
    console.log('Apply filter - filter:', filter, 'start:', startDate, 'end:', endDate);
    
    if (!startDate || !endDate) {
        alert('Silakan pilih tanggal terlebih dahulu!');
        return;
    }
    if (new Date(startDate) > new Date(endDate)) {
        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
        return;
    }
    
    // Redirect dengan parameter
    let url = '{{ route("laporan") }}?filter=' + filter + '&start=' + startDate + '&end=' + endDate;
    window.location.href = url;
}

    function exportExcel() {
        let activeFilter = document.querySelector('.period-toggle .pt-btn.active');
        let filter = activeFilter ? activeFilter.getAttribute('data-filter') : 'bulanan';
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;
        let url = '{{ route("laporan.export.excel") }}?filter=' + filter + '&start=' + startDate + '&end=' + endDate;
        window.location.href = url;
    }

    function exportPDF() {
        let activeFilter = document.querySelector('.period-toggle .pt-btn.active');
        let filter = activeFilter ? activeFilter.getAttribute('data-filter') : 'bulanan';
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;
        let url = '{{ route("laporan.export.pdf") }}?filter=' + filter + '&start=' + startDate + '&end=' + endDate;
        window.open(url, '_blank');
    }

    // ========== SET DEFAULT TANGGAL ==========
    function setDefaultDateRange() {
        let startInput = document.getElementById('start_date');
        let endInput = document.getElementById('end_date');
        
        if (!startInput.value || !endInput.value) {
            let activeFilter = document.querySelector('.period-toggle .pt-btn.active');
            let filter = activeFilter ? activeFilter.getAttribute('data-filter') : 'bulanan';
            updateDateRangeByFilter(filter);
        }
    }

    // ========== EVENT LISTENERS ==========
    document.addEventListener('DOMContentLoaded', function() {
        setDefaultDateRange();
        
        // Period toggle buttons
let periodBtns = document.querySelectorAll('.period-toggle .pt-btn');
periodBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Hapus active dari semua
        periodBtns.forEach(b => b.classList.remove('active'));
        // Tambah active ke yang diklik
        this.classList.add('active');
        
        // Ambil filter value
        let filterValue = this.getAttribute('data-filter');
        
        // Update range tanggal
        updateDateRangeByFilter(filterValue);
        
        // Apply filter (redirect)
        applyFilter();
    });
});
        
        let btnTerapkan = document.getElementById('btnTerapkan');
        if (btnTerapkan) {
            btnTerapkan.addEventListener('click', applyFilter);
        }
        
        let btnExportExcel = document.getElementById('btnExportExcel');
        if (btnExportExcel) {
            btnExportExcel.addEventListener('click', exportExcel);
        }
        
        let btnExportPDF = document.getElementById('btnExportPDF');
        if (btnExportPDF) {
            btnExportPDF.addEventListener('click', exportPDF);
        }
        
        let startDateInput = document.getElementById('start_date');
        let endDateInput = document.getElementById('end_date');
        if (startDateInput) {
            startDateInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applyFilter();
            });
        }
        if (endDateInput) {
            endDateInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applyFilter();
            });
        }
    });
</script>

<style>
.reports-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.bar-chart-wrap.ai-style-change-2 {
    padding: 30px 20px 20px 20px;  /* Tambah padding atas jadi 30px */
    background: linear-gradient(135deg, #fefefe 0%, #fafafa 100%);
    border-radius: 12px;
    margin: 0 16px 20px 16px;
}
/* ── Laporan responsive ── */
@media (max-width: 768px) {
  .report-filter-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .report-filter-row .period-toggle { width: 100%; }
  .report-filter-row .date-input { width: 100%; }
  .report-filter-row .btn-gold { width: 100%; justify-content: center; }
  .export-row { flex-direction: column; align-items: stretch; }
  .export-btns { display: flex; gap: 8px; }
  .export-btns button { flex: 1; }
}


#pendapatanChart.ai-style-change-1 {
    width: 100%;
    height: auto;
    max-height: 180px;
    margin-top: 15px;  /* Tambah margin top */
    filter: drop-shadow(0 2px 8px rgba(0,0,0,0.05));
}

.card.gold-top {
    border-top: 3px solid #C9A84C;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    background: white;
    overflow: hidden;
}

.card.gold-top:hover {
    box-shadow: 0 8px 30px rgba(201, 168, 76, 0.12);
    transform: translateY(-3px);
}

.card-head {
    padding: 18px 20px 0 20px;
}

.card-title {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
}

.card-sub {
    font-size: 10.5px;
    color: #aaa;
    margin-top: 4px;
}

.report-filter-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    flex-wrap: wrap;
    background: #fefefe;
}

.period-toggle {
    display: flex;
    background: #f5f5f5;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 4px;
    gap: 4px;
}

.pt-btn {
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: #888;
    transition: all 0.2s ease;
}

.pt-btn:hover {
    background: rgba(201, 168, 76, 0.15);
    color: #C9A84C;
}

.pt-btn.active {
    background: #C9A84C;
    color: white;
    box-shadow: 0 2px 8px rgba(201,168,76,0.3);
}

.date-input {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 12px;
    font-family: monospace;
    transition: all 0.2s ease;
}

.date-input:focus {
    outline: none;
    border-color: #C9A84C;
    box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
}

.btn-outline {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 8px 18px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-outline:hover {
    background: #f5f5f5;
    border-color: #C9A84C;
    color: #C9A84C;
}

.btn-gold {
    background: #C9A84C;
    border: none;
    border-radius: 10px;
    padding: 8px 18px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    color: white;
}

.btn-gold:hover {
    background: #b8963a;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(201,168,76,0.3);
}

.report-tbl {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.report-tbl thead tr {
    background: #fafafa;
    border-bottom: 1px solid #eee;
}

.report-tbl th {
    padding: 14px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.report-tbl tbody tr {
    border-bottom: 1px solid #f5f5f5;
    transition: all 0.2s ease;
}

.report-tbl tbody tr:hover {
    background: #fef9e8;
}

.report-tbl td {
    padding: 13px 16px;
    font-size: 12.5px;
    vertical-align: middle;
}

.td-mono {
    font-family: 'Consolas', monospace;
}

.td-gold {
    color: #C9A84C;
    font-weight: 700;
}

.report-footer {
    padding: 14px 20px;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    border-radius: 0 0 16px 16px;
    font-size: 12px;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 600;
}

.badge-out {
    background: rgba(201,168,76,0.12);
    color: #C9A84C;
    border: 1px solid rgba(201,168,76,0.25);
}

.export-row {
    margin-top: 20px;
    background: white;
    border: 1px solid #eee;
    border-radius: 16px;
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

.export-title {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
}

.export-sub {
    font-size: 11px;
    color: #aaa;
    margin-top: 4px;
}

.export-btns {
    display: flex;
    gap: 12px;
}

@media (max-width: 768px) {
    .reports-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .report-filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    .period-toggle {
        justify-content: center;
    }
    .export-row {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endsection