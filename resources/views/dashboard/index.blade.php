@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="page active" id="page-dashboard">

    <div class="pg-head">
        <div class="pg-title">Dashboard</div>
        <div class="pg-sub">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} · Selamat datang kembali, {{ session('user')['nama_lengkap'] ?? 'User' }}</div>
    </div>

    <!-- Stat Cards -->
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-ico gold-ico">👘</div>
            <div class="stat-label">Baju Siap Sewa</div>
            <div class="stat-val">{{ $barangTersedia }}</div>
            <span class="stat-tag neutral">dari {{ $totalBarang }} koleksi</span>
        </div>
        <div class="stat-card">
            <div class="stat-ico green-ico">📤</div>
            <div class="stat-label">Sedang Disewa</div>
            <div class="stat-val">{{ $transaksiAktif }}</div>
            <span class="stat-tag up">↑ aktif hari ini</span>
        </div>
        <div class="stat-card">
            <div class="stat-ico orange-ico">💰</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-val"><span class="curr">Rp</span>{{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
            <span class="stat-tag up">↑ dari kemarin</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="qa-section">
        <div class="qa-title">Aksi Cepat</div>
        <div class="qa-grid">
            @if(session('user')['role'] == 'Karyawan')
            <a href="{{ route('transaksi.create') }}" class="qa-card primary">
                <div class="qa-ico black">👘</div>
                <div>
                    <div class="qa-label" style="color:#fff">Sewa Baru</div>
                    <div class="qa-desc">Buat transaksi penyewaan</div>
                </div>
            </a>
            @else
            <a href="{{ route('barang.create') }}" class="qa-card primary">
                <div class="qa-ico black">➕</div>
                <div>
                    <div class="qa-label" style="color:#fff">Tambah Barang</div>
                    <div class="qa-desc">Tambah koleksi baru</div>
                </div>
            </a>
            @endif

            <a href="{{ route('transaksi.index') }}" class="qa-card">
                <div class="qa-ico gold">↩️</div>
                <div>
                    <div class="qa-label">Kembalikan Baju</div>
                    <div class="qa-desc">Proses pengembalian</div>
                </div>
            </a>

            <a href="{{ route('barang.index') }}" class="qa-card">
                <div class="qa-ico soft">📋</div>
                <div>
                    <div class="qa-label">Cek Stok</div>
                    <div class="qa-desc">Lihat status koleksi</div>
                </div>
            </a>

            <a href="{{ route('laporan') }}" class="qa-card">
                <div class="qa-ico soft">📊</div>
                <div>
                    <div class="qa-label">Laporan</div>
                    <div class="qa-desc">Rekap keuangan & sewa</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Bottom: Chart + Recent -->
    <div class="dash-bottom">

        <!-- Mini bar chart -->
        <div class="card gold-top">
            <div class="card-head">
                <div>
                    <div class="card-title">Penyewaan Bulanan</div>
                    <div class="card-sub">Periode {{ \Carbon\Carbon::now()->subMonths(6)->format('F') }} – {{ \Carbon\Carbon::now()->format('F Y') }}</div>
                </div>
                <a href="{{ route('laporan') }}" style="font-size:11px;color:var(--gold-dk);text-decoration:none;font-weight:600;">Lihat Semua →</a>
            </div>
            <div class="mini-chart">
                <canvas id="sewaChart" width="100%" height="170" style="max-height:170px"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card gold-top">
            <div class="card-head">
                <div>
                    <div class="card-title">Transaksi Terbaru</div>
                    <div class="card-sub">Aktivitas terbaru</div>
                </div>
                <a href="{{ route('transaksi.index') }}" style="font-size:11px;color:var(--gold-dk);text-decoration:none;font-weight:600;">Semua →</a>
            </div>
            <div class="recent-list">
                @forelse($transaksiTerbaru as $trx)
                <div class="recent-item">
                    <div class="ri-ava">{{ substr($trx->pelanggan->nama_pelanggan ?? '?', 0, 2) }}</div>
                    <div>
                        <div class="ri-name">{{ $trx->pelanggan->nama_pelanggan ?? '-' }}</div>
                        <div class="ri-detail">
                            @if($trx->detailTransaksis->first())
                                {{ $trx->detailTransaksis->first()->barang->nama_barang ?? '-' }}
                            @else
                                -
                            @endif
                            · {{ \Carbon\Carbon::parse($trx->tgl_sewa)->diffInDays($trx->tgl_jatuh_tempo) }} hari
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div class="ri-amount">Rp {{ number_format($trx->total_biaya ?? 0, 0, ',', '.') }}</div>
                        <span class="ri-badge {{ ($trx->status_transaksi ?? '') == 'Diproses' ? 'out' : 'done' }}">
                            {{ ($trx->status_transaksi ?? '') == 'Diproses' ? 'Disewa' : 'Selesai' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="recent-item">Belum ada transaksi</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk chart (contoh data dari 6 bulan terakhir)
    const ctx = document.getElementById('sewaChart').getContext('2d');
    
    // Data dummy untuk 6 bulan terakhir
    const bulanLabels = [];
    const dataSewa = [];
    
    // Generate 6 bulan terakhir
    for (let i = 5; i >= 0; i--) {
        let date = new Date();
        date.setMonth(date.getMonth() - i);
        bulanLabels.push(date.toLocaleString('id-ID', { month: 'short' }));
        dataSewa.push(Math.floor(Math.random() * 40) + 10);
    }
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Jumlah Penyewaan',
                data: dataSewa,
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
                    grid: { color: '#f0f0f0' },
                    ticks: { stepSize: 10 }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' sewa';
                        }
                    }
                },
                legend: { display: false }
            }
        }
    });
</script>

<style>
/* Dashboard additional styles */
.stat-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.stat-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 20px 22px;
    position: relative;
    transition: box-shadow 0.2s;
}
.stat-card:hover {
    box-shadow: var(--sh-sm);
    border-color: var(--gold-rim);
}
.stat-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
}
.stat-card:nth-child(1)::after { background: linear-gradient(90deg, #a07830, #C9A84C, #e0c06e); }
.stat-card:nth-child(2)::after { background: linear-gradient(90deg, #1a6b46, #2da66e, #52c896); }
.stat-card:nth-child(3)::after { background: linear-gradient(90deg, #c05020, #e07040, #f0a070); }
.stat-ico {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-bottom: 14px;
}
.stat-ico.gold-ico { background: var(--gold-xs); border: 1px solid var(--gold-md); }
.stat-ico.green-ico { background: rgba(45,166,110,.08); border: 1px solid rgba(45,166,110,.2); }
.stat-ico.orange-ico { background: rgba(224,112,64,.08); border: 1px solid rgba(224,112,64,.2); }
.stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
}
.stat-val {
    font-size: 32px;
    font-weight: 800;
    color: var(--black);
    margin: 5px 0 10px;
}
.stat-val .curr {
    font-size: 16px;
    color: var(--gray-400);
    font-weight: 500;
}
.stat-tag {
    display: inline-flex;
    background: var(--gray-100);
    color: var(--gray-500);
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 11px;
}
.stat-tag.up {
    background: rgba(45,166,110,.08);
    color: #1a8050;
}
.qa-section {
    margin-bottom: 20px;
}
.qa-title {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    margin-bottom: 12px;
}
.qa-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}
.qa-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    transition: all 0.18s;
}
.qa-card:hover {
    border-color: var(--gold-rim);
    box-shadow: var(--sh-sm);
    transform: translateY(-1px);
}
.qa-card.primary {
    background: var(--black);
    border-color: var(--black);
}
.qa-ico {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.qa-ico.black { background: rgba(255,255,255,.08); }
.qa-ico.gold { background: var(--gold-xs); border: 1px solid var(--gold-md); }
.qa-ico.soft { background: var(--gray-100); border: 1px solid var(--gray-200); }
.qa-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--black);
}
.qa-card.primary .qa-label { color: #fff; }
.qa-desc {
    font-size: 10.5px;
    color: var(--gray-400);
    margin-top: 1px;
}
.qa-card.primary .qa-desc { color: rgba(255,255,255,0.4); }
.dash-bottom {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 16px;
}
.mini-chart {
    padding: 16px 18px 14px;
}
.recent-list {
    padding: 2px 0;
}
.recent-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 18px;
    border-bottom: 1px solid var(--gray-100);
}
.recent-item:last-child { border-bottom: none; }
.recent-item:hover { background: var(--gray-50); }
.ri-ava {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--black);
    border: 1.5px solid var(--gold-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--gold-lt);
}
.ri-name {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--black);
}
.ri-detail {
    font-size: 11px;
    color: var(--gray-400);
}
.ri-amount {
    margin-left: auto;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
}
.ri-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    margin-top: 2px;
    display: block;
    text-align: right;
}
.ri-badge.out {
    background: var(--gold-xs);
    color: var(--gold-dk);
    border: 1px solid var(--gold-md);
}
.ri-badge.done {
    background: rgba(45,166,110,.08);
    color: #1a8050;
}
</style>
@endsection