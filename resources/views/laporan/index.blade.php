@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('breadcrumb', 'Laporan Keuangan')

@section('content')
<div class="pg-head">
    <div class="pg-title">Laporan Keuangan</div>
    <div class="pg-sub">Rekap pendapatan dan penyewaan NM Gallery</div>
</div>

<!-- Filter Periode -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-head">
        <div class="card-title">Filter Laporan</div>
    </div>
    <div style="padding: 16px 20px;">
        <form method="GET" action="" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="field">
                <label class="flbl">Periode</label>
                <select name="periode" class="fselect" style="width: 150px;">
                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div class="field">
                <label class="flbl">Tahun</label>
                <select name="tahun" class="fselect" style="width: 120px;">
                    @foreach($tahunList as $thn)
                    <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-gold">Terapkan Filter</button>
        </form>
    </div>
</div>

<!-- Statistik Cards -->
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-ico gold-ico">💰</div>
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-val">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        <span class="stat-tag neutral">Dari {{ $totalTransaksi }} transaksi</span>
    </div>
    <div class="stat-card">
        <div class="stat-ico gold-ico">⚠️</div>
        <div class="stat-label">Total Denda</div>
        <div class="stat-val">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
        <span class="stat-tag neutral">Pendapatan tambahan</span>
    </div>
    <div class="stat-card">
        <div class="stat-ico gold-ico">📊</div>
        <div class="stat-label">Transaksi Aktif</div>
        <div class="stat-val">{{ $transaksiAktif }}</div>
        <span class="stat-tag neutral">Sedang disewa</span>
    </div>
</div>

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-ico gold-ico">👘</div>
        <div class="stat-label">Total Koleksi</div>
        <div class="stat-val">{{ $totalBarang }}</div>
        <span class="stat-tag neutral">Baju Bodo</span>
    </div>
    <div class="stat-card">
        <div class="stat-ico gold-ico">👥</div>
        <div class="stat-label">Total Pelanggan</div>
        <div class="stat-val">{{ $totalPelanggan }}</div>
        <span class="stat-tag neutral">Terdaftar</span>
    </div>
</div>

<!-- Grafik Pendapatan -->
<div class="card gold-top" style="margin-bottom: 20px;">
    <div class="card-head">
        <div>
            <div class="card-title">Grafik Pendapatan {{ $tahun }}</div>
            <div class="card-sub">Periode Januari - Desember {{ $tahun }}</div>
        </div>
    </div>
    <div style="padding: 20px;">
        <canvas id="pendapatanChart" style="width: 100%; height: 300px;"></canvas>
    </div>
</div>

<!-- Tabel Transaksi Terbaru -->
<div class="card gold-top">
    <div class="card-head">
        <div>
            <div class="card-title">Transaksi Terbaru</div>
            <div class="card-sub">10 transaksi terakhir</div>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="inv-tbl" style="width: 100%;">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Baju</th>
                    <th>Tgl Sewa</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiTerbaru as $item)
                <tr>
                    <td class="td-mono">#TRX-{{ str_pad($item->id_transaksi, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->pelanggan->nama_pelanggan ?? '-' }}</td>
                    <td>{{ $item->detailTransaksis->first()->barang->nama_barang ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_sewa)->format('d/m/Y') }}</td>
                    <td class="td-gold">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                    <td>
                        @if($item->status_transaksi == 'Diproses')
                            <span class="badge badge-out">Disewa</span>
                        @else
                            <span class="badge badge-ready">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px;">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="tbl-footer">
        <div class="pg-info">Total {{ $transaksiTerbaru->count() }} transaksi ditampilkan</div>
    </div>
</div>

<!-- Export Section -->
<div class="export-row">
    <div class="export-text">
        <div class="export-title">Unduh Laporan</div>
        <div class="export-sub">Ekspor rekap keuangan untuk pencatatan Owner</div>
    </div>
    <div class="export-btns">
        <a href="{{ route('laporan.export.excel') }}" class="btn-outline">📊 Export Excel</a>
        <a href="{{ route('laporan.export.pdf') }}" class="btn-gold">📄 Export PDF</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('pendapatanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {{ json_encode($pendapatanPerBulan) }},
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
                            return 'Rp ' + value.toLocaleString('id-ID');
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
                }
            }
        }
    });
</script>

<style>
.td-mono {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11.5px;
}
.td-gold {
    color: var(--gold-dk);
    font-weight: 700;
}
.export-row {
    margin-top: 20px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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