@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="pg-head">
    <div class="pg-title">Dashboard</div>
    <div class="pg-sub">{{ now()->translatedFormat('l, d F Y') }} · Selamat datang kembali, {{ session('user')['nama_lengkap'] ?? 'User' }}</div>
</div>

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-ico gold-ico">👘</div>
        <div class="stat-label">Baju Siap Sewa</div>
        <div class="stat-val">{{ $barangTersedia ?? 0 }}</div>
        <span class="stat-tag neutral">dari {{ $totalBarang ?? 0 }} koleksi</span>
    </div>
    <div class="stat-card">
        <div class="stat-ico gold-ico">📤</div>
        <div class="stat-label">Sedang Disewa</div>
        <div class="stat-val">{{ $transaksiAktif ?? 0 }}</div>
        <span class="stat-tag neutral">transaksi aktif</span>
    </div>
    <div class="stat-card">
        <div class="stat-ico gold-ico">💰</div>
        <div class="stat-label">Pendapatan Hari Ini</div>
        <div class="stat-val"><span class="curr">Rp</span>{{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</div>
        <span class="stat-tag neutral">total pendapatan</span>
    </div>
</div>

<div class="qa-section">
    <div class="qa-title">Aksi Cepat</div>
    <div class="qa-grid">
        <a href="{{ route('transaksi.create') }}" class="qa-card primary">
            <div class="qa-ico black">👘</div>
            <div>
                <div class="qa-label" style="color:#fff">Sewa Baru</div>
                <div class="qa-desc">Buat transaksi penyewaan</div>
            </div>
        </a>
        <a href="{{ route('transaksi.index') }}" class="qa-card">
            <div class="qa-ico soft">↩️</div>
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

<div class="card gold-top">
    <div class="card-head">
        <div>
            <div class="card-title">Transaksi Terbaru</div>
            <div class="card-sub">Aktivitas terakhir</div>
        </div>
        <a href="{{ route('transaksi.index') }}" style="font-size:11px;color:var(--gold-dk);text-decoration:none;font-weight:600">Lihat Semua →</a>
    </div>
    <div class="recent-list">
        @forelse($transaksiTerbaru ?? [] as $trx)
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
@endsection