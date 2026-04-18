@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('breadcrumb', 'Data Pelanggan')

@section('content')
<div class="pg-head">
    <div class="pg-title">Data Pelanggan</div>
    <div class="pg-sub">Daftar semua pelanggan</div>
</div>

<div style="margin-bottom: 16px;">
    <a href="{{ route('pelanggan.create') }}" class="btn-gold">+ Tambah Pelanggan</a>
</div>

<div class="inv-table-card">
    <table class="inv-tbl">
        <thead>
            <tr>
                <th>Nama</th>
                <th>No. Telepon</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelanggan as $item)
            <tr>
                <td>{{ $item->nama_pelanggan }}</td>
                <td>{{ $item->no_telp }}</td>
                <td>{{ $item->alamat ?? '-' }}</td>
                <td>
                    <a href="{{ route('pelanggan.edit', $item->id_pelanggan) }}" class="btn-outline" style="padding: 4px 10px;">Edit</a>
                    <form action="{{ route('pelanggan.destroy', $item->id_pelanggan) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-outline" style="padding: 4px 10px; background:#fee;" onclick="return confirm('Hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;">Belum ada data pelanggan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
