@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('breadcrumb', 'Detail Transaksi')

@section('content')
<div class="pg-head">
    <div style="display:flex;align-items:flex-start;justify-content:space-between">
        <div>
            <div class="pg-title">Detail Transaksi</div>
            <div class="pg-sub">Informasi lengkap dan E-Nota penyewaan</div>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('transaksi.index') }}" class="btn-white">← Buat Transaksi Baru</a>
            <button class="btn-gold" onclick="window.print()">🖨 Cetak E-Nota</button>
        </div>
    </div>
</div>

<div class="trx-layout" style="grid-template-columns: 1fr 1fr; gap: 24px;">

    <!-- LEFT: Informasi Transaksi -->
    <div class="form-card">
        <div class="form-sect">
            <div class="form-sect-lbl">Informasi Transaksi</div>
            <div class="fgrid">
                <div class="field f-full">
                    <label class="flbl">No. Transaksi</label>
                    <input type="text" class="finput" readonly value="#TRX-{{ str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) }}">
                </div>
                <div class="field f-full">
                    <label class="flbl">Status</label>
                    <div>
                        @if($transaksi->status_transaksi == 'Diproses')
                            <span class="badge badge-out">🟡 Sedang Disewa</span>
                        @else
                            <span class="badge badge-ready">✅ Selesai</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-sect">
            <div class="form-sect-lbl">Data Pelanggan</div>
            <div class="fgrid">
                <div class="field f-full">
                    <label class="flbl">Nama Pelanggan</label>
                    <input type="text" class="finput" readonly value="{{ $transaksi->pelanggan->nama_pelanggan ?? '-' }}">
                </div>
                <div class="field">
                    <label class="flbl">No. Telepon</label>
                    <input type="text" class="finput" readonly value="{{ $transaksi->pelanggan->no_telp ?? '-' }}">
                </div>
                <div class="field">
                    <label class="flbl">Alamat</label>
                    <input type="text" class="finput" readonly value="{{ $transaksi->pelanggan->alamat ?? '-' }}">
                </div>
            </div>
        </div>

        <div class="form-sect">
            <div class="form-sect-lbl">Detail Sewa</div>
            <div class="fgrid">
                <div class="field">
                    <label class="flbl">Baju yang Disewa</label>
                    <input type="text" class="finput" readonly value="{{ $transaksi->detailTransaksis->first()->barang->nama_barang ?? '-' }}">
                </div>
                <div class="field">
                    <label class="flbl">Tanggal Sewa</label>
                    <input type="text" class="finput" readonly value="{{ \Carbon\Carbon::parse($transaksi->tgl_sewa)->format('d/m/Y') }}">
                </div>
                <div class="field">
                    <label class="flbl">Tanggal Jatuh Tempo</label>
                    <input type="text" class="finput" readonly value="{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d/m/Y') }}">
                </div>
                <div class="field">
                    <label class="flbl">Tanggal Kembali</label>
                    <input type="text" class="finput" readonly value="{{ $transaksi->tgl_kembali ? \Carbon\Carbon::parse($transaksi->tgl_kembali)->format('d/m/Y') : '-' }}">
                </div>
                <div class="field">
                    <label class="flbl">Durasi Sewa</label>
                    <input type="text" class="finput" readonly value="{{ \Carbon\Carbon::parse($transaksi->tgl_sewa)->diffInDays($transaksi->tgl_jatuh_tempo) }} hari">
                </div>
                <div class="field">
                    <label class="flbl">Total Biaya</label>
                    <input type="text" class="finput" readonly value="Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}">
                </div>
                @if($transaksi->total_denda > 0)
                <div class="field">
                    <label class="flbl">Denda Keterlambatan</label>
                    <input type="text" class="finput" readonly value="Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}" style="color:#dc2626;">
                </div>
                @endif
            </div>
        </div>

        @if($transaksi->status_transaksi == 'Diproses')
        <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;background:var(--gray-50)">
            <form action="{{ route('transaksi.update', $transaksi->id_transaksi) }}" method="POST" onsubmit="return confirm('Kembalikan barang?')">
                @csrf
                @method('PUT')
                <button type="submit" class="btn-gold">↩️ Proses Pengembalian</button>
            </form>
        </div>
        @endif
    </div>

    <!-- RIGHT: E-Nota -->
    <div class="nota-panel">
        <div class="nota-preview-hd">
            <div class="nota-preview-title">E-Nota Digital</div>
            <span style="font-size:10.5px;color:var(--gray-400)">Bukti Transaksi Resmi</span>
        </div>

        <div class="nota-paper" id="notaPrint">
            <div class="nota-top">
                <div class="nota-brand">NM Gallery</div>
                <div class="nota-tagline">Baju Bodo Collection</div>
                <div class="nota-trx-label">Nomor Transaksi</div>
                <div class="nota-trx-num">#TRX-{{ str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="nota-body">
                <div class="nota-row">
                    <span class="nota-key">Tanggal Transaksi</span>
                    <span class="nota-val">{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="nota-row">
                    <span class="nota-key">Pelanggan</span>
                    <span class="nota-val">{{ $transaksi->pelanggan->nama_pelanggan ?? '-' }}</span>
                </div>
                <div class="nota-row">
                    <span class="nota-key">No. Telepon</span>
                    <span class="nota-val">{{ $transaksi->pelanggan->no_telp ?? '-' }}</span>
                </div>
                <div class="nota-row">
                    <span class="nota-key">Baju</span>
                    <span class="nota-val">{{ $transaksi->detailTransaksis->first()->barang->nama_barang ?? '-' }}</span>
                </div>
                <div class="nota-row">
                    <span class="nota-key">Periode Sewa</span>
                    <span class="nota-val">{{ \Carbon\Carbon::parse($transaksi->tgl_sewa)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d/m/Y') }}</span>
                </div>
                <div class="nota-row">
                    <span class="nota-key">Durasi</span>
                    <span class="nota-val" style="color:var(--gold-dk);font-weight:700">{{ \Carbon\Carbon::parse($transaksi->tgl_sewa)->diffInDays($transaksi->tgl_jatuh_tempo) }} hari</span>
                </div>
                <div class="nota-row">
                    <span class="nota-key">Harga/hari</span>
                    <span class="nota-val">Rp {{ number_format($transaksi->detailTransaksis->first()->barang->harga_sewa ?? 0, 0, ',', '.') }}</span>
                </div>

                @if($transaksi->tgl_kembali)
                <div class="nota-row">
                    <span class="nota-key">Tanggal Kembali</span>
                    <span class="nota-val">{{ \Carbon\Carbon::parse($transaksi->tgl_kembali)->format('d/m/Y') }}</span>
                </div>
                @endif

                @if($transaksi->total_denda > 0)
                <div class="nota-row">
                    <span class="nota-key">Denda Keterlambatan</span>
                    <span class="nota-val" style="color:#dc2626;">Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="nota-total-box">
                    <span class="nota-total-lbl">TOTAL</span>
                    <span class="nota-total-val">Rp {{ number_format($transaksi->total_biaya + $transaksi->total_denda, 0, ',', '.') }}</span>
                </div>

                <div class="nota-footer">
                    Terima kasih telah mempercayakan momen<br>Anda kepada <b>NM Gallery</b> ✦ Makassar
                </div>
            </div>
        </div>

        <button class="nota-gen-btn" onclick="window.print()">🖨 Cetak E-Nota</button>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .modal-acts, .nota-gen-btn, .btn-gold, .btn-white, .form-card, .pg-head {
        display: none !important;
    }
    .trx-layout {
        display: block !important;
    }
    .nota-panel {
        box-shadow: none !important;
        border: none !important;
    }
    .nota-paper {
        margin: 0 !important;
    }
    body {
        background: white !important;
    }
}
</style>
@endsection