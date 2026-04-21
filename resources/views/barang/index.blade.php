@extends('layouts.app')

@section('title', 'Inventaris Barang')
@section('breadcrumb', 'Inventaris & Stok')

@section('content')
<div class="page active" id="page-inventory">

    <div class="pg-head">
        <div class="pg-title">Inventaris &amp; Stok</div>
        <div class="pg-sub">{{ $totalBarang }} koleksi Baju Bodo terdaftar · diperbarui baru saja</div>
    </div>

    <div class="inv-toolbar">
        <div class="inv-search">
            <span style="font-size:14px;color:var(--gray-400)">⌕</span>
            <input type="text" id="searchInput" placeholder="Cari nama baju, warna, ukuran…">
        </div>

        <div class="filter-chips">
            <div class="chip active" onclick="setChip(this)">Semua ({{ $totalBarang }})</div>
            <div class="chip" onclick="setChip(this)">Siap ({{ $barangTersedia }})</div>
            <div class="chip" onclick="setChip(this)">Disewa ({{ $barangDisewa }})</div>
            <div class="chip" onclick="setChip(this)">Laundry ({{ $barangLaundry }})</div>
            <div class="chip" onclick="setChip(this)">Rusak ({{ $barangRusak }})</div>
        </div>

        <div style="display:flex;gap:8px;margin-left:auto">
            <button class="btn-outline" onclick="alert('Filter lanjutan dalam pengembangan')">🔽 Filter</button>
            <a href="{{ route('barang.create') }}" class="btn-gold">+ Tambah Koleksi</a>
        </div>
    </div>

    <div class="inv-table-card">
        <table class="inv-tbl">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Baju</th>
                    <th>Warna</th>
                    <th>Ukuran</th>
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
                    <td>
                        <div class="color-dot-cell">
                            <div class="color-dot" style="background:
                                @if($loop->index % 5 == 0) #2d6e3e
                                @elseif($loop->index % 5 == 1) #8b1c4a
                                @elseif($loop->index % 5 == 2) #c9a227
                                @elseif($loop->index % 5 == 3) #1a5a8a
                                @else #c8607a
                                @endif"></div>
                            @if($loop->index % 5 == 0) Hijau Emerald
                            @elseif($loop->index % 5 == 1) Merah Marun
                            @elseif($loop->index % 5 == 2) Emas & Krem
                            @elseif($loop->index % 5 == 3) Biru Langit
                            @else Merah Muda
                            @endif
                        </div>
                    </td>
                    <td class="font-mono" style="font-size:12px">S, M, L</td>
                    <td class="font-mono td-gold" style="font-size:12px">Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}</td>
                    <td class="font-mono" style="font-size:12px;color:var(--gray-500)">{{ rand(1,5) }}</td>
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
                            <a href="{{ route('transaksi.create') }}?barang={{ $item->id_barang }}" class="row-btn" title="Sewa">📋</a>
                            <a href="{{ route('barang.edit', $item->id_barang) }}" class="row-btn" title="Edit">✏️</a>
                            <form action="{{ route('barang.destroy', $item->id_barang) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus barang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn" style="background:none; cursor:pointer;" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px;">Belum ada data barang</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="tbl-footer">
            <div class="pg-info">Menampilkan {{ $barang->count() }} dari {{ $totalBarang }} koleksi</div>
            <div class="pg-btns">
                <button class="pg-btn">‹</button>
                <button class="pg-btn active">1</button>
                <button class="pg-btn">2</button>
                <button class="pg-btn">3</button>
                <button class="pg-btn">…</button>
                <button class="pg-btn">13</button>
                <button class="pg-btn">›</button>
            </div>
        </div>
    </div>

</div>

<script>
    function setChip(el) {
        el.closest('.filter-chips').querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        
        // Filter berdasarkan status
        let filter = el.innerText.toLowerCase();
        let rows = document.querySelectorAll('#barangTableBody tr');
        rows.forEach(row => {
            let status = row.querySelector('.badge')?.innerText.toLowerCase() || '';
            if (filter === 'semua' || status.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#barangTableBody tr');
        rows.forEach(row => {
            let name = row.querySelector('.baju-name')?.innerText.toLowerCase() || '';
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    });
</script>

<style>
.inv-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.inv-search {
    display: flex;
    align-items: center;
    gap: 7px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 0 12px;
    flex: 1;
    max-width: 340px;
}
.inv-search input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    padding: 8.5px 0;
    font-size: 12.5px;
}
.filter-chips {
    display: flex;
    gap: 6px;
    align-items: center;
}
.chip {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 500;
    border: 1px solid var(--gray-200);
    background: white;
    color: var(--gray-500);
    cursor: pointer;
}
.chip:hover {
    border-color: var(--gold-rim);
    color: var(--gold-dk);
}
.chip.active {
    background: var(--black);
    border-color: var(--black);
    color: var(--gold-lt);
}
.inv-table-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
}
.inv-tbl {
    width: 100%;
    border-collapse: collapse;
}
.inv-tbl thead tr {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}
.inv-tbl th {
    padding: 12px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
.inv-tbl td {
    padding: 13px 16px;
    font-size: 12.5px;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}
.inv-tbl tbody tr:hover {
    background: var(--gray-50);
}
.baju-photo {
    width: 40px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    border: 1px solid var(--gray-200);
    background: var(--gray-50);
}
.baju-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}
.baju-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--black);
}
.baju-code {
    font-size: 10.5px;
    color: var(--gray-400);
    font-family: monospace;
    margin-top: 1px;
}
.color-dot-cell {
    display: flex;
    align-items: center;
    gap: 7px;
}
.color-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.08);
}
.font-mono {
    font-family: 'JetBrains Mono', monospace;
}
.td-gold {
    color: var(--gold-dk);
    font-weight: 700;
}
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4.5px;
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
}
.badge::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
}
.badge-ready {
    background: var(--gold-xs);
    color: var(--gold-dk);
    border: 1px solid var(--gold-md);
}
.badge-ready::before { background: var(--gold); }
.badge-out {
    background: var(--gray-100);
    color: var(--gray-600);
    border: 1px solid var(--gray-200);
}
.badge-out::before { background: var(--gray-400); }
.badge-laundry {
    background: rgba(59,130,246,0.07);
    color: #2563eb;
    border: 1px solid rgba(59,130,246,0.2);
}
.badge-laundry::before { background: #60a5fa; }
.badge-damaged {
    background: rgba(220,80,60,0.07);
    color: #c0392b;
    border: 1px solid rgba(220,80,60,0.2);
}
.badge-damaged::before { background: #e87060; }
.row-acts {
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.12s;
}
.inv-tbl tbody tr:hover .row-acts {
    opacity: 1;
}
.row-btn {
    width: 27px;
    height: 27px;
    border-radius: 5px;
    border: 1px solid var(--gray-200);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    color: var(--gray-500);
    text-decoration: none;
}
.row-btn:hover {
    border-color: var(--gold-rim);
    color: var(--gold-dk);
    background: var(--gold-xs);
}
.tbl-footer {
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
</style>
@endsection