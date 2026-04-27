@extends('layouts.pdf_master')

@section('content')
<table class="table-data">
    <thead>
        <tr>
            <th>No</th>
            <th>No. Transaksi</th>
            <th>Pelanggan</th>
            <th>No. Telepon</th>
            <th>Barang</th>
            <th>Ukuran</th>
            <th>Tgl Sewa</th>
            <th>Tgl Jatuh</th>
            <th>Tgl Kembali</th>
            <th>Durasi</th>
            <th>Total Biaya</th>
            <th>Denda</th>
            <th>Grand Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($transaksis as $item)
        @php
            $detail = $item->detailTransaksis->first();
            $tglSewa = \Carbon\Carbon::parse($item->tgl_sewa);
            $tglJatuh = \Carbon\Carbon::parse($item->tgl_jatuh_tempo);
            $durasi = $tglSewa->diffInDays($tglJatuh);
            $denda = $item->total_denda ?? 0;
            $grand = $item->total_biaya + $denda;
            $statusText = $item->status_transaksi == 'Diproses' ? 'Disewa' : ($item->status_transaksi == 'Selesai' ? 'Selesai' : 'Terlambat');
        @endphp
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td class="text-center">#TRX-{{ str_pad($item->id_transaksi, 4, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $item->pelanggan->nama_pelanggan ?? '-' }}</td>
            <td class="text-center">{{ $item->pelanggan->no_telp ?? '-' }}</td>
            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
            <td class="text-center">{{ $detail->ukuran ?? '-' }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($item->tgl_sewa)) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($item->tgl_jatuh_tempo)) }}</td>
            <td class="text-center">{{ $item->tgl_kembali ? date('d/m/Y', strtotime($item->tgl_kembali)) : '-' }}</td>
            <td class="text-center">{{ $durasi }} hari</td>
            <td class="text-right">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($denda, 0, ',', '.') }}</td>
            <td class="text-right"><strong>Rp {{ number_format($grand, 0, ',', '.') }}</strong></td>
            <td class="text-center">
                <span class="badge {{ $item->status_transaksi == 'Diproses' ? 'badge-out' : 'badge-success' }}">
                    {{ $statusText }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="12" class="text-right"><strong>TOTAL KESELURUHAN</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="summary-box">
    <table style="width: auto; margin: 0;">
        <tr><td><strong>Total Transaksi:</strong> {{ $transaksis->count() }} transaksi</td></tr>
        <tr><td><strong>Periode:</strong> {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</td></tr>
        <tr><td><strong>Filter:</strong> {{ ucfirst($filter) }}</td></tr>
    </table>
</div>
@endsection