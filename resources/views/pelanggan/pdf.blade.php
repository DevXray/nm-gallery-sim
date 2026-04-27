@extends('layouts.pdf_master')

@section('content')
 
<table class="table-data">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pelanggan</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Total Sewa</th>
            <th>Terakhir Sewa</th>
            <th>Total Bayar</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($pelanggan as $item)
        @php
            $statusAktif = ($item->transaksi_count > 0) ? true : false;
            $terakhirSewa = $item->terakhir_sewa ? \Carbon\Carbon::parse($item->terakhir_sewa)->format('d/m/Y') : '-';
        @endphp
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $item->nama_pelanggan }}</td>
            <td>{{ $item->alamat ?: '-' }}</td>
            <td class="text-center">{{ $item->no_telp }}</td>
            <td class="text-center">{{ $item->transaksi_count ?? 0 }}×</td>
            <td class="text-center">{{ $terakhirSewa }}</td>
            <td class="text-right">Rp {{ number_format($item->transaksi_sum_total_biaya ?? 0, 0, ',', '.') }}</td>
            <td class="text-center">
                @if($statusAktif)
                    <span class="badge-out">Aktif Sewa</span>
                @else
                    <span class="badge-success">Selesai</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="summary-box">
    <table style="width: auto; margin: 0;">
        <tr><td><strong>Total Pelanggan:</strong> {{ $totalPelanggan }} pelanggan</td></tr>
        <tr><td><strong>Sedang Menyewa:</strong> {{ $sedangMenyewa }} pelanggan</td></tr>
        <tr><td><strong>Pelanggan Setia:</strong> {{ $pelangganSetia }} pelanggan (≥3x sewa)</td></tr>
    </table>
</div>
<style>
.badge-out {
    background: #fff3cd;
    color: #856404;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 9px;
    display: inline-block;
}
.badge-success {
    background: #d4edda;
    color: #155724;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 9px;
    display: inline-block;
}
.text-right {
    text-align: right;
}
.text-center {
    text-align: center;
}
</style>
@endsection