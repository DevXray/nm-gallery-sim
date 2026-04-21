@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('breadcrumb', 'Data Pelanggan')

@section('content')
<div class="page active" id="page-pelanggan">

    <div class="pg-head">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="pg-title">Data Pelanggan</div>
                <div class="pg-sub">{{ $totalPelanggan }} pelanggan terdaftar · diperbarui baru saja</div>
            </div>
            <a href="{{ route('pelanggan.create') }}" class="btn-gold">+ Tambah Pelanggan</a>
        </div>
    </div>

    <!-- Stat strip -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
        <div style="background:var(--white);border:1px solid var(--gray-200);border-radius:var(--r3);padding:16px 18px;border-top:2px solid var(--gold);box-shadow:var(--sh-xs)">
            <div style="font-size:10.5px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">Total Pelanggan</div>
            <div style="font-size:28px;font-weight:800;color:var(--black);letter-spacing:-.5px">{{ $totalPelanggan }}</div>
            <div style="font-size:11px;color:var(--gray-400);margin-top:4px">+{{ rand(1,10) }} bulan ini</div>
        </div>
        <div style="background:var(--white);border:1px solid var(--gray-200);border-radius:var(--r3);padding:16px 18px;border-top:2px solid #2da66e;box-shadow:var(--sh-xs)">
            <div style="font-size:10.5px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">Sedang Menyewa</div>
            <div style="font-size:28px;font-weight:800;color:var(--black);letter-spacing:-.5px">{{ rand(5,20) }}</div>
            <div style="font-size:11px;color:var(--gray-400);margin-top:4px">Aktif hari ini</div>
        </div>
        <div style="background:var(--white);border:1px solid var(--gray-200);border-radius:var(--r3);padding:16px 18px;border-top:2px solid #e07040;box-shadow:var(--sh-xs)">
            <div style="font-size:10.5px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">Pelanggan Setia</div>
            <div style="font-size:28px;font-weight:800;color:var(--black);letter-spacing:-.5px">{{ rand(1,10) }}</div>
            <div style="font-size:11px;color:var(--gray-400);margin-top:4px">Sewa ≥ 3 kali</div>
        </div>
    </div>

    <!-- Search & filter -->
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap">
        <div class="inv-search" style="max-width:300px">
            <span style="font-size:14px;color:var(--gray-400)">⌕</span>
            <input type="text" id="searchInput" placeholder="Cari nama atau nomor telepon…">
        </div>
        <div class="filter-chips">
            <div class="chip active" onclick="setChip(this)">Semua ({{ $totalPelanggan }})</div>
            <div class="chip" onclick="setChip(this)">Aktif ({{ rand(5,20) }})</div>
            <div class="chip" onclick="setChip(this)">Riwayat Saja ({{ rand(1,10) }})</div>
        </div>
        <button class="btn-outline" style="margin-left:auto" onclick="alert('Fitur Export dalam pengembangan')">📤 Export</button>
    </div>

    <!-- Pelanggan table -->
    <div class="inv-table-card">
        <table class="inv-tbl">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>No. Telepon</th>
                    <th>Total Sewa</th>
                    <th>Terakhir Sewa</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="pelangganTableBody">
                @forelse($pelanggan as $item)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--black);border:1.5px solid var(--gold-md);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--gold-lt);flex-shrink:0">{{ substr($item->nama_pelanggan, 0, 2) }}</div>
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--black)">{{ $item->nama_pelanggan }}</div>
                                <div style="font-size:10.5px;color:var(--gray-400)">Makassar, Sul-Sel</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono" style="font-size:12px;color:var(--gray-600)">{{ $item->no_telp }}</td>
                    <td style="text-align:center"><span style="background:var(--gold-xs);color:var(--gold-dk);border:1px solid var(--gold-md);padding:3px 10px;border-radius:12px;font-size:11.5px;font-weight:700">{{ rand(1,10) }}×</span></td>
                    <td style="font-size:12px;color:var(--gray-500)">{{ $item->created_at->format('d M Y') }}</td>
                    <td class="td-mono td-gold" style="font-size:12px">Rp {{ number_format(rand(500000, 5000000), 0, ',', '.') }}</td>
                    <td>
                        @if(rand(0,1) == 1)
                            <span class="badge badge-out">Aktif Sewa</span>
                        @else
                            <span class="badge badge-ready" style="background:rgba(45,166,110,.08);color:#1a8050;border-color:rgba(45,166,110,.2)">Selesai</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-acts">
                            <a href="{{ route('pelanggan.edit', $item->id_pelanggan) }}" class="row-btn" title="Edit">✏️</a>
                            <form action="{{ route('pelanggan.destroy', $item->id_pelanggan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn" style="background:none; cursor:pointer;" title="Hapus">🗑️</button>
                            </form>
                            <a href="{{ route('transaksi.create') }}?pelanggan={{ $item->id_pelanggan }}" class="row-btn" title="Buat Sewa">📋</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px;">Belum ada data pelanggan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="tbl-footer">
            <div class="pg-info">Menampilkan {{ $pelanggan->count() }} dari {{ $totalPelanggan }} pelanggan</div>
            <div class="pg-btns">
                <button class="pg-btn">‹</button>
                <button class="pg-btn active">1</button>
                <button class="pg-btn">2</button>
                <button class="pg-btn">3</button>
                <button class="pg-btn">…</button>
                <button class="pg-btn">9</button>
                <button class="pg-btn">›</button>
            </div>
        </div>
    </div>

</div>

<script>
    function setChip(el) {
        el.closest('.filter-chips').querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
    }
    
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#pelangganTableBody tr');
        rows.forEach(row => {
            let name = row.querySelector('.ri-name')?.innerText.toLowerCase() || '';
            let tel = row.querySelector('.font-mono')?.innerText.toLowerCase() || '';
            row.style.display = (name.includes(filter) || tel.includes(filter)) ? '' : 'none';
        });
    });
</script>

<style>
.font-mono {
    font-family: 'JetBrains Mono', monospace;
}
.td-mono {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
}
.td-gold {
    color: var(--gold-dk);
    font-weight: 700;
}
.inv-search {
    display: flex;
    align-items: center;
    gap: 7px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 0 12px;
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
}
.inv-tbl td {
    padding: 13px 16px;
    font-size: 12.5px;
    border-bottom: 1px solid var(--gray-100);
}
.inv-tbl tbody tr:hover {
    background: var(--gray-50);
}
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
    gap: 4px;
}
.pg-btn {
    width: 28px;
    height: 28px;
    border-radius: 5px;
    border: 1px solid var(--gray-200);
    background: white;
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
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
}
.badge-out {
    background: var(--gold-xs);
    color: var(--gold-dk);
    border: 1px solid var(--gold-md);
}
</style>
@endsection