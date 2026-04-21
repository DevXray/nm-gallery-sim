@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('breadcrumb', 'Laporan Keuangan')

@section('content')
<div class="page active" id="page-reports">

    <div class="pg-head">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="pg-title">Laporan Keuangan</div>
                <div class="pg-sub">Rekap pendapatan dan penyewaan Baju Bodo</div>
            </div>
        </div>
    </div>

    <!-- Two chart summary cards -->
    <div class="reports-grid">
        <div class="card gold-top">
            <div class="card-head">
                <div>
                    <div class="card-title">Pendapatan Bulanan</div>
                    <div class="card-sub">Jan – {{ \Carbon\Carbon::now()->format('M Y') }} · dalam jutaan Rp</div>
                </div>
            </div>
            <div class="bar-chart-wrap">
                <canvas id="pendapatanChart" width="100%" height="150" style="max-height:150px"></canvas>
            </div>
        </div>

        <div class="card gold-top">
            <div class="card-head">
                <div>
                    <div class="card-title">Ringkasan Bulan Ini</div>
                    <div class="card-sub">{{ \Carbon\Carbon::now()->format('F Y') }} (1–{{ \Carbon\Carbon::now()->format('d') }} {{ \Carbon\Carbon::now()->format('M') }})</div>
                </div>
            </div>
            <div style="padding:14px 18px;display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--gray-50);border-radius:var(--r2);border:1px solid var(--gray-200)">
                    <div>
                        <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.6px">Total Penyewaan</div>
                        <div style="font-size:24px;font-weight:800;color:var(--black);margin-top:2px;letter-spacing:-.5px">{{ $totalTransaksiBulanIni }}</div>
                    </div>
                    <div style="font-size:28px">👘</div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--gold-xs);border-radius:var(--r2);border:1px solid var(--gold-md)">
                    <div>
                        <div style="font-size:11px;color:var(--gold-dk);font-weight:600;text-transform:uppercase;letter-spacing:.6px">Total Pendapatan</div>
                        <div style="font-size:24px;font-weight:800;color:var(--gold-dk);margin-top:2px;letter-spacing:-.5px;font-family:var(--ff-mono)">{{ number_format($pendapatanBulanIni / 1000000, 1, ',', '') }}jt</div>
                    </div>
                    <div style="font-size:28px">💰</div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:rgba(45,166,110,.05);border-radius:var(--r2);border:1px solid rgba(45,166,110,.15)">
                    <div>
                        <div style="font-size:11px;color:#1a8050;font-weight:600;text-transform:uppercase;letter-spacing:.6px">Baju Kembali Tepat Waktu</div>
                        <div style="font-size:24px;font-weight:800;color:#1a8050;margin-top:2px;letter-spacing:-.5px">{{ $tepatWaktu }}%</div>
                    </div>
                    <div style="font-size:28px">✅</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card gold-top" style="margin-bottom:16px">
        <div class="card-head">
            <div>
                <div class="card-title">Rincian Transaksi</div>
                <div class="card-sub">Filter berdasarkan periode</div>
            </div>
        </div>

        <div class="report-filter-row">
            <div class="period-toggle">
                <div class="pt-btn {{ $filter == 'harian' ? 'active' : '' }}" onclick="setFilter('harian')">Harian</div>
                <div class="pt-btn {{ $filter == 'mingguan' ? 'active' : '' }}" onclick="setFilter('mingguan')">Mingguan</div>
                <div class="pt-btn {{ $filter == 'bulanan' ? 'active' : '' }}" onclick="setFilter('bulanan')">Bulanan</div>
            </div>
            <input type="date" class="date-input" id="start_date" value="{{ $startDate }}">
            <span style="font-size:12px;color:var(--gray-400)">s/d</span>
            <input type="date" class="date-input" id="end_date" value="{{ $endDate }}">
            <button class="btn-outline" style="margin-left:auto" onclick="applyFilter()">Terapkan</button>
        </div>

        <table class="report-tbl">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Baju</th>
                    <th>Tanggal</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $item)
                <tr>
                    <td class="td-mono" style="font-size:11px;color:var(--gold-dk)">#TRX-{{ str_pad($item->id_transaksi, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight:500">{{ $item->pelanggan->nama_pelanggan ?? '-' }}</td>
                    <td style="color:var(--gray-600)">{{ $item->detailTransaksis->first()->barang->nama_barang ?? '-' }} · {{ $item->detailTransaksis->first()->barang->ukuran ?? 'M' }}</td>
                    <td class="td-mono" style="font-size:11.5px;color:var(--gray-500)">{{ \Carbon\Carbon::parse($item->tgl_sewa)->format('d M Y') }}</td>
                    <td class="td-mono" style="font-size:11.5px">{{ \Carbon\Carbon::parse($item->tgl_sewa)->diffInDays($item->tgl_jatuh_tempo) }} hari</td>
                    <td>
                        @if($item->status_transaksi == 'Diproses')
                            <span class="badge badge-out">Aktif</span>
                        @elseif($item->status_transaksi == 'Selesai')
                            <span class="badge badge-ready" style="background:rgba(45,166,110,.08);color:#1a8050;border-color:rgba(45,166,110,.2)">Selesai</span>
                        @else
                            <span class="badge badge-damaged" style="background:rgba(220,80,60,.07);color:#c04030;border-color:rgba(220,80,60,.2)">Terlambat</span>
                        @endif
                    </td>
                    <td class="td-mono td-gold">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px;">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="report-footer">
            <div style="font-size:12px;color:var(--gray-500)">
                Total Ditampilkan: <strong style="color:var(--black)">{{ $transaksis->count() }} transaksi</strong> &nbsp;·&nbsp; Total Pendapatan: <strong style="color:var(--gold-dk);font-family:var(--ff-mono)">Rp {{ number_format($transaksis->sum('total_biaya'), 0, ',', '.') }}</strong>
            </div>
            <div class="pg-btns">
                <button class="pg-btn active">1</button>
                <button class="pg-btn">2</button>
                <button class="pg-btn">3</button>
                <button class="pg-btn">›</button>
            </div>
        </div>
    </div>

    <!-- Export Row -->
    <div class="export-row">
        <div class="export-text">
            <div class="export-title">Unduh Laporan</div>
            <div class="export-sub">Ekspor rekap keuangan untuk pencatatan Owner</div>
        </div>
        <div class="export-btns">
            <button class="btn-outline" onclick="exportExcel()">📊 Export Excel</button>
            <button class="btn-gold" onclick="exportPDF()">📄 Export PDF</button>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Pendapatan
    const ctx = document.getElementById('pendapatanChart').getContext('2d');
    
    // Data dari server
    const bulanLabels = {!! json_encode($bulanLabels) !!};
    const dataPendapatan = {!! json_encode($dataPendapatan) !!};
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: dataPendapatan,
                backgroundColor: 'rgba(201, 168, 76, 0.7)',
                borderColor: '#C9A84C',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(0) + 'M';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                },
                legend: { display: false }
            }
        }
    });

    function setFilter(filter) {
        document.querySelectorAll('.period-toggle .pt-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        applyFilter();
    }

    function applyFilter() {
        let filter = document.querySelector('.period-toggle .pt-btn.active').innerText.toLowerCase();
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;
        window.location.href = "{{ route('laporan') }}?filter=" + filter + "&start=" + startDate + "&end=" + endDate;
    }

    function exportExcel() {
        window.location.href = "{{ route('laporan.export.excel') }}?start=" + document.getElementById('start_date').value + "&end=" + document.getElementById('end_date').value;
    }

    function exportPDF() {
        alert('Fitur Export PDF sedang dalam pengembangan');
    }
</script>

<style>
.reports-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 18px;
}
.bar-chart-wrap {
    padding: 18px 20px 14px;
}
.report-filter-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 13px 18px;
    border-bottom: 1px solid var(--gray-100);
    flex-wrap: wrap;
}
.period-toggle {
    display: flex;
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 2px;
    gap: 2px;
}
.pt-btn {
    padding: 5px 12px;
    border-radius: 5px;
    font-size: 11.5px;
    font-weight: 500;
    cursor: pointer;
    color: var(--gray-500);
}
.pt-btn.active {
    background: white;
    color: var(--black);
    font-weight: 700;
    box-shadow: var(--sh-xs);
}
.date-input {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
}
.report-tbl {
    width: 100%;
    border-collapse: collapse;
}
.report-tbl thead tr {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}
.report-tbl th {
    padding: 10px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: .8px;
}
.report-tbl tbody tr {
    border-bottom: 1px solid var(--gray-100);
}
.report-tbl tbody tr:hover {
    background: var(--gray-50);
}
.report-tbl td {
    padding: 11px 16px;
    font-size: 12.5px;
    vertical-align: middle;
}
.td-mono {
    font-family: 'JetBrains Mono', monospace;
}
.td-gold {
    color: var(--gold-dk);
    font-weight: 700;
}
.report-footer {
    padding: 12px 16px;
    border-top: 1px solid var(--gray-100);
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pg-btns {
    display: flex;
    gap: 3px;
}
.pg-btn {
    width: 28px;
    height: 28px;
    border-radius: 5px;
    border: 1px solid var(--gray-200);
    background: white;
    font-size: 11.5px;
    cursor: pointer;
}
.pg-btn.active {
    background: var(--black);
    border-color: var(--black);
    color: var(--gold-lt);
}
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4.5px;
    padding: 3.5px 9px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
}
.badge-out {
    background: var(--gold-xs);
    color: var(--gold-dk);
    border: 1px solid var(--gold-md);
}
.export-row {
    margin-top: 16px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.export-title {
    font-size: 13.5px;
    font-weight: 700;
}
.export-sub {
    font-size: 11.5px;
    color: var(--gray-400);
    margin-top: 3px;
}
.export-btns {
    display: flex;
    gap: 8px;
}
</style>
@endsection