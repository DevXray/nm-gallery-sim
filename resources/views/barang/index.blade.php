@extends('layouts.app')

@section('title', 'Inventaris Barang')
@section('breadcrumb', 'Inventaris & Stok')

@section('content')
<div class="pg-head">
    <div class="pg-title">Inventaris &amp; Stok</div>
    <div class="pg-sub">{{ $totalBarang }} koleksi Baju Bodo terdaftar · diperbarui baru saja</div>
</div>

<div class="inv-toolbar">
    <div class="inv-search">
        <span style="font-size:14px;color:var(--gray-400)">⌕</span>
        <input type="text" id="searchInput" placeholder="Cari nama baju...">
    </div>
    <div style="display:flex;gap:8px;margin-left:auto">
        <a href="{{ route('barang.create') }}" class="btn-gold">+ Tambah Koleksi</a>
    </div>
</div>

<div class="inv-table-card">
    <table class="inv-tbl">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama Baju</th>
                <th>Harga/Hari</th>
                <th>Stok</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="barangTableBody">
            @forelse($barang as $item)
            <tr>
                <td><div class="baju-photo">👘</div></td>
                <td>
                    <div class="baju-cell">
                        <div>
                            <div class="baju-name">{{ $item->nama_barang }}</div>
                            <div class="baju-code">#BB-{{ str_pad($item->id_barang, 3, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                </td>
                <td class="font-mono td-gold" style="font-size:12px">Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}</td>
                <td class="font-mono" style="font-size:12px;color:var(--gray-500)">1</td>
                <td>
                    @if($item->status_barang == 'Tersedia')
                        <span class="badge badge-ready">Siap Sewa</span>
                    @elseif($item->status_barang == 'Disewa')
                        <span class="badge badge-out">Sedang Disewa</span>
                    @elseif($item->status_barang == 'Laundry')
                        <span class="badge badge-laundry">Laundry</span>
                    @else
                        <span class="badge badge-damaged">Perbaikan</span>
                    @endif
                </td>
                <td>
                    <div class="row-acts">
                        <a href="{{ route('barang.edit', $item->id_barang) }}" class="row-btn">✏️</a>
                        <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="row-btn" style="background:none; cursor:pointer;">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:40px;">Belum ada data barang</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="tbl-footer">
        <div class="pg-info">Menampilkan {{ $barang->count() }} dari {{ $totalBarang }} koleksi</div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#barangTableBody tr');
    rows.forEach(row => {
        let name = row.querySelector('.baju-name')?.innerText.toLowerCase() || '';
        row.style.display = name.includes(filter) ? '' : 'none';
    });
});
</script>
@endsection
