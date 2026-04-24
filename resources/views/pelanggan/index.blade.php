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
            <button class="btn-gold" onclick="showTambahPelangganModal()">+ Tambah Pelanggan</button>
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
        <button class="btn-outline" style="margin-left:auto" onclick="exportPelangganPDF()">📄 Export PDF</button>
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
                            <button class="row-btn" onclick="showEditPelangganModal({{ $item->id_pelanggan }}, '{{ $item->nama_pelanggan }}', '{{ $item->no_telp }}', '{{ $item->alamat }}')" title="Edit">✏️</button>
                            <form action="{{ route('pelanggan.destroy', $item->id_pelanggan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn" style="background:none; cursor:pointer;" title="Hapus">🗑️</button>
                            </form>
                            @if(session('user')['role'] == 'Karyawan')
                                <a href="{{ route('transaksi.create') }}?pelanggan={{ $item->id_pelanggan }}" class="row-btn" title="Buat Sewa">📋</a>
                            @else
                                <button class="row-btn" onclick="showAccessDeniedSewa()" title="Buat Sewa" style="cursor:pointer;">📋</button>
                            @endif
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

<!-- MODAL TAMBAH PELANGGAN -->
<div class="modal-overlay" id="tambahPelangganModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Tambah Pelanggan Baru</div>
                <div class="modal-popup-sub">Masukkan data pelanggan</div>
            </div>
            <button class="modal-popup-close" onclick="closeTambahPelangganModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Nama Pelanggan *</label>
                    <input type="text" id="new_nama_pelanggan" class="modal-input" placeholder="Nama lengkap pelanggan">
                </div>
                <div class="user-info-field">
                    <label>No. Telepon *</label>
                    <input type="text" id="new_no_telp" class="modal-input" placeholder="0812-xxxx-xxxx">
                </div>
                <div class="user-info-field">
                    <label>Alamat</label>
                    <input type="text" id="new_alamat" class="modal-input" placeholder="Alamat lengkap pelanggan">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeTambahPelangganModal()">Batal</button>
            <button class="btn-gold" onclick="savePelangganBaru()">Simpan</button>
        </div>
    </div>
</div>

<!-- MODAL EDIT PELANGGAN -->
<div class="modal-overlay" id="editPelangganModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Edit Pelanggan</div>
                <div class="modal-popup-sub">Ubah data pelanggan</div>
            </div>
            <button class="modal-popup-close" onclick="closeEditPelangganModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Nama Pelanggan *</label>
                    <input type="text" id="edit_nama_pelanggan" class="modal-input">
                </div>
                <div class="user-info-field">
                    <label>No. Telepon *</label>
                    <input type="text" id="edit_no_telp" class="modal-input">
                </div>
                <div class="user-info-field">
                    <label>Alamat</label>
                    <input type="text" id="edit_alamat" class="modal-input">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeEditPelangganModal()">Batal</button>
            <button class="btn-gold" onclick="updatePelanggan()">Update</button>
        </div>
    </div>
</div>

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
/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.25s ease;
}
.modal-overlay.show {
    opacity: 1;
    visibility: visible;
}
.modal-popup {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 450px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    transform: scale(0.95);
    transition: transform 0.25s ease;
}
.modal-overlay.show .modal-popup {
    transform: scale(1);
}
.modal-popup-header {
    padding: 20px 24px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.modal-popup-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--black);
}
.modal-popup-sub {
    font-size: 11px;
    color: var(--gold-dk);
    margin-top: 4px;
}
.modal-popup-close {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    background: white;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.15s;
}
.modal-popup-close:hover {
    border-color: var(--gold-rim);
    color: var(--gold-dk);
}
.modal-popup-body {
    padding: 24px;
    max-height: 60vh;
    overflow-y: auto;
}
.modal-popup-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}
.user-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.user-info-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.user-info-field label {
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.modal-input {
    padding: 12px 14px;
    border: 1.5px solid var(--gray-200);
    border-radius: 10px;
    font-size: 13px;
    font-family: inherit;
    transition: all 0.2s;
}
.modal-input:focus {
    outline: none;
    border-color: #C9A84C;
    box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
}
</style>

<script>
    let currentEditId = null;

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

    // EXPORT PDF SEMUA PELANGGAN
    function exportPelangganPDF() {
        let rows = document.querySelectorAll('#pelangganTableBody tr');
        let dataPelanggan = [];
        
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                let nama = row.querySelector('.ri-name')?.innerText || '';
                let telp = row.querySelector('.font-mono')?.innerText || '';
                let totalSewa = row.querySelector('td:nth-child(3) span')?.innerText || '0×';
                let terakhirSewa = row.querySelector('td:nth-child(4)')?.innerText || '-';
                let totalBayar = row.querySelector('.td-gold')?.innerText || 'Rp 0';
                let status = row.querySelector('.badge')?.innerText || '-';
                
                dataPelanggan.push({ nama, telp, totalSewa, terakhirSewa, totalBayar, status });
            }
        });
        
        let printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Data Pelanggan - NM Gallery</title>
                <meta charset="UTF-8">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Courier New', monospace; padding: 30px; background: white; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #C9A84C; padding-bottom: 20px; }
                    .header h1 { font-size: 24px; margin-bottom: 5px; }
                    .header p { color: #666; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 11px; }
                    th { background: #0a0a0a; color: #e0c06e; text-transform: uppercase; }
                    .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 20px; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>NM Gallery</h1>
                    <p>Laporan Data Pelanggan</p>
                    <p>Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
                    <p>Total Pelanggan: ${dataPelanggan.length}</p>
                </div>
                <table>
                    <thead><tr><th>No</th><th>Nama Pelanggan</th><th>No. Telepon</th><th>Total Sewa</th><th>Terakhir Sewa</th><th>Total Bayar</th><th>Status</th></tr></thead>
                    <tbody>
                        ${dataPelanggan.map((p, i) => `
                            <tr>
                                <td>${i + 1}</td>
                                <td>${p.nama}</td>
                                <td>${p.telp}</td>
                                <td style="text-align:center">${p.totalSewa}</td>
                                <td>${p.terakhirSewa}</td>
                                <td>${p.totalBayar}</td>
                                <td>${p.status}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="footer">
                    Dicetak pada: ${new Date().toLocaleString('id-ID')}<br>
                    NM Gallery - Baju Bodo Collection ✦ Makassar
                </div>
                <script>
                    window.onload = function() { window.print(); setTimeout(function() { window.close(); }, 500); };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    // ACCESS DENIED UNTUK BUAT SEWA (OWNER)
    function showAccessDeniedSewa() {
        alert('Akses Ditolak! Fitur "Buat Sewa" hanya untuk Karyawan.');
    }

    // TAMBAH PELANGGAN
    function showTambahPelangganModal() {
        document.getElementById('new_nama_pelanggan').value = '';
        document.getElementById('new_no_telp').value = '';
        document.getElementById('new_alamat').value = '';
        document.getElementById('tambahPelangganModal').classList.add('show');
    }

    function closeTambahPelangganModal() {
        document.getElementById('tambahPelangganModal').classList.remove('show');
    }

    function savePelangganBaru() {
        const nama = document.getElementById('new_nama_pelanggan').value;
        const telp = document.getElementById('new_no_telp').value;
        const alamat = document.getElementById('new_alamat').value;

        if (!nama) { alert('Nama pelanggan harus diisi!'); return; }
        if (!telp) { alert('No. telepon harus diisi!'); return; }

        const formData = new FormData();
        formData.append('nama_pelanggan', nama);
        formData.append('no_telp', telp);
        formData.append('alamat', alamat);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("pelanggan.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pelanggan berhasil ditambahkan');
                location.reload();
            } else {
                alert(data.message || 'Gagal menambah pelanggan');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }

    // EDIT PELANGGAN
    function showEditPelangganModal(id, nama, telp, alamat) {
        currentEditId = id;
        document.getElementById('edit_nama_pelanggan').value = nama;
        document.getElementById('edit_no_telp').value = telp;
        document.getElementById('edit_alamat').value = alamat || '';
        document.getElementById('editPelangganModal').classList.add('show');
    }

    function closeEditPelangganModal() {
        document.getElementById('editPelangganModal').classList.remove('show');
    }

    function updatePelanggan() {
        const nama = document.getElementById('edit_nama_pelanggan').value;
        const telp = document.getElementById('edit_no_telp').value;
        const alamat = document.getElementById('edit_alamat').value;

        if (!nama) { alert('Nama pelanggan harus diisi!'); return; }
        if (!telp) { alert('No. telepon harus diisi!'); return; }

        const formData = new FormData();
        formData.append('nama_pelanggan', nama);
        formData.append('no_telp', telp);
        formData.append('alamat', alamat);
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');

        fetch('/pelanggan/' + currentEditId, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pelanggan berhasil diupdate');
                location.reload();
            } else {
                alert(data.message || 'Gagal update pelanggan');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }
</script>
@endsection