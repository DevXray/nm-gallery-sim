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
<button class="btn-outline" onclick="showFilterModal()">🔽 Filter</button>            @if(session('user')['role'] == 'Owner')
            <button class="btn-gold" onclick="showTambahBarangModal()">+ Tambah Koleksi</button>
            @endif
        </div>
    </div>

    <div class="inv-table-card">
        <table class="inv-tbl">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Baju</th>
                    <th>Ukuran Tersedia</th>
                    <th>Total Stok</th>
                    <th>Harga/Hari</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="barangTableBody">
                @forelse($barang as $item)
                @php
                    $stokArray = is_string($item->stok) ? json_decode($item->stok, true) : [];
                    if (!is_array($stokArray)) $stokArray = [];
                    $totalStok = array_sum($stokArray);
                @endphp
                <tr>
                    <td class="foto-cell">
    @if($item->foto && file_exists(public_path($item->foto)))
        <img src="/{{ $item->foto }}" class="foto-thumbnail" 
     onclick="showFotoDetail('{{ $item->foto }}', '{{ addslashes($item->nama_barang) }}')"
     style="width: 40px; height: 48px; object-fit: cover; border-radius: 8px; cursor: pointer;">
    @else
        <div class="baju-photo" style="cursor: pointer;" onclick="showFotoDetail(null, '{{ addslashes($item->nama_barang) }}')">👘</div>
    @endif
</td>
                    <td>
                        <div class="baju-cell">
                            <div>
                                <div class="baju-name">{{ $item->nama_barang }}</div>
                                <div class="baju-code">#BB-{{ str_pad($item->id_barang, 3, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono" style="font-size:12px">{{ $item->ukuran ?? '-' }}</td>
                    <td class="font-mono" style="font-size:12px; font-weight:600;">
                        {{ $totalStok }}
                        <button class="btn-stok-detail" onclick="showStokDetail({{ $item->id_barang }}, '{{ addslashes($item->nama_barang) }}', {{ json_encode($stokArray) }})" title="Lihat detail stok">👁️</button>
                    </td>
                    <td class="font-mono td-gold" style="font-size:12px">Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}</td>
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
                            <!-- Tombol Sewa (SEMUA ROLE bisa) -->
                            <a href="{{ route('transaksi.create') }}?barang={{ $item->id_barang }}" class="row-btn" title="Sewa">📋</a>
                            
                            @if(session('user')['role'] == 'Owner')
                            <!-- Tombol Edit & Hapus (ONLY OWNER) -->
<button class="row-btn" onclick='showEditBarangModal({{ $item->id_barang }}, "{{ addslashes($item->nama_barang) }}", "{{ $item->ukuran }}", {{ $item->harga_sewa }}, {{ json_encode($stokArray) }}, "{{ $item->status_barang }}", "{{ $item->foto }}")' title="Edit">✏️</button>                                @csrf
                                @method('DELETE')
                                <button type="submit" class="row-btn" style="background:none; cursor:pointer;" title="Hapus">🗑️</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px;">Belum ada data barang</td>
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

<!-- MODAL TAMBAH BARANG (ONLY OWNER) -->
@if(session('user')['role'] == 'Owner')
<div class="modal-overlay" id="tambahBarangModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Tambah Koleksi Baru</div>
                <div class="modal-popup-sub">Masukkan data baju baru ke inventaris</div>
            </div>
            <button class="modal-popup-close" onclick="closeTambahBarangModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Nama Baju *</label>
                    <input type="text" id="new_nama_barang" class="modal-input" placeholder="Contoh: Baju Bodo Sutra Hijau">
                </div>
                <div class="user-info-field">
                    <label>Harga Sewa / Hari *</label>
                    <input type="number" id="new_harga_sewa" class="modal-input" placeholder="200000">
                </div>
                <div class="user-info-field">
                    <label>Ukuran & Stok</label>
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="ukuran-checkbox" value="S"> Size S</label>
                            <input type="number" class="stok-input" data-ukuran="S" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="ukuran-checkbox" value="M"> Size M</label>
                            <input type="number" class="stok-input" data-ukuran="M" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="ukuran-checkbox" value="L"> Size L</label>
                            <input type="number" class="stok-input" data-ukuran="L" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="ukuran-checkbox" value="XL"> Size XL</label>
                            <input type="number" class="stok-input" data-ukuran="XL" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                    </div>
                    <small style="color: var(--gray-400); font-size: 10px; display: block; margin-top: 8px;">Centang ukuran, lalu isi stok masing-masing</small>
                </div>
            </div>
        </div>
        <div class="user-info-field">
    <label>Foto Barang</label>
    <input type="file" name="foto" id="new_foto" class="modal-input" accept="image/jpeg,image/png,image/jpg">
    <small style="color: var(--gray-400); font-size: 10px;">Format: JPG, PNG. Max 2MB</small>
</div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeTambahBarangModal()">Batal</button>
            <button class="btn-gold" onclick="saveBarangBaru()">Simpan Koleksi</button>
        </div>
    </div>
</div>

<!-- MODAL EDIT BARANG (ONLY OWNER) -->
<div class="modal-overlay" id="editBarangModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Edit Koleksi</div>
                <div class="modal-popup-sub">Ubah data baju yang sudah ada</div>
            </div>
            <button class="modal-popup-close" onclick="closeEditBarangModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Nama Baju *</label>
                    <input type="text" id="edit_nama_barang" class="modal-input">
                </div>
                <div class="user-info-field">
                    <label>Harga Sewa / Hari *</label>
                    <input type="number" id="edit_harga_sewa" class="modal-input">
                </div>
                <div class="user-info-field">
                    <label>Ukuran & Stok</label>
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 8px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="edit-ukuran-checkbox" value="S"> Size S</label>
                            <input type="number" class="edit-stok-input" data-ukuran="S" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="edit-ukuran-checkbox" value="M"> Size M</label>
                            <input type="number" class="edit-stok-input" data-ukuran="M" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="edit-ukuran-checkbox" value="L"> Size L</label>
                            <input type="number" class="edit-stok-input" data-ukuran="L" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <label style="min-width: 60px;"><input type="checkbox" class="edit-ukuran-checkbox" value="XL"> Size XL</label>
                            <input type="number" class="edit-stok-input" data-ukuran="XL" placeholder="Stok" style="width: 80px; padding: 6px; border: 1px solid var(--gray-200); border-radius: 6px;" min="0" value="0" disabled>
                        </div>
                    </div>
                    <small style="color: var(--gray-400); font-size: 10px; display: block; margin-top: 8px;">Centang ukuran yang tersedia, isi stok masing-masing</small>
                </div>
            </div>
        </div>
        <div class="user-info-field">
    <label>Foto Barang</label>
    <div id="edit_foto_preview" style="margin-bottom: 8px;"></div>
    <input type="file" name="foto" id="edit_foto" class="modal-input" accept="image/jpeg,image/png,image/jpg">
    <div style="margin-top: 8px;">
        <label style="cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <input type="checkbox" name="hapus_foto" value="1" id="hapus_foto_checkbox">
            Hapus foto yang ada
        </label>
    </div>
    <small style="color: var(--gray-400); font-size: 10px;">Format: JPG, PNG. Max 2MB</small>
</div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeEditBarangModal()">Batal</button>
            <button class="btn-gold" onclick="updateBarang()">Update Koleksi</button>
        </div>
    </div>
</div>
@endif

<!-- MODAL DETAIL STOK -->
<div class="modal-overlay" id="stokDetailModal">
    <div class="modal-popup" style="max-width: 350px;">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title" id="stokDetailTitle">Detail Stok</div>
                <div class="modal-popup-sub">Stok per ukuran</div>
            </div>
            <button class="modal-popup-close" onclick="closeStokDetailModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div id="stokDetailContent" style="display: flex; flex-direction: column; gap: 12px;"></div>
        </div>
        <div class="modal-popup-footer" style="justify-content: center;">
            <button class="btn-gold" onclick="closeStokDetailModal()">OK</button>
        </div>
    </div>
</div>

<!-- MODAL DETAIL FOTO -->
<div class="modal-overlay" id="fotoDetailModal">
    <div class="modal-popup" style="max-width: 600px; text-align: center;">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title" id="fotoDetailTitle">Foto Barang</div>
                <div class="modal-popup-sub">Klik gambar untuk memperbesar</div>
            </div>
            <button class="modal-popup-close" onclick="closeFotoDetailModal()">✕</button>
        </div>
        <div class="modal-popup-body" style="text-align: center; padding: 20px;">
            <img id="fotoDetailImage" src="" alt="Foto Barang" 
                 style="max-width: 100%; max-height: 55vh; border-radius: 12px; cursor: pointer; transition: transform 0.2s;"
                 onclick="zoomFoto()">
        </div>
        <div class="modal-popup-footer" style="justify-content: center; gap: 10px;">
            <button class="btn-white" onclick="closeFotoDetailModal()">Tutup</button>
            <button class="btn-gold" onclick="zoomFoto()">🔍 Perbesar</button>
        </div>
    </div>
</div>

<!-- MODAL ZOOM FOTO (fullscreen) -->
<div class="modal-overlay" id="zoomFotoModal" onclick="closeZoomModal()">
    <div class="zoom-container" style="position: relative; width: 90%; max-width: 90vw; max-height: 90vh; text-align: center;">
        <button class="zoom-close" onclick="closeZoomModal()" style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 28px; cursor: pointer;">✕</button>
        <img id="zoomImage" src="" alt="Zoom" style="max-width: 100%; max-height: 85vh; border-radius: 8px;">
    </div>
</div>
<!-- MODAL FILTER LANJUTAN -->
<div class="modal-overlay" id="filterModal">
    <div class="modal-popup" style="max-width: 400px;">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Filter Lanjutan</div>
                <div class="modal-popup-sub">Saring data berdasarkan kriteria</div>
            </div>
            <button class="modal-popup-close" onclick="closeFilterModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Rentang Harga</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="number" id="filter_harga_min" class="modal-input" placeholder="Min" style="width: 50%;">
                        <input type="number" id="filter_harga_max" class="modal-input" placeholder="Max" style="width: 50%;">
                    </div>
                </div>
                <div class="user-info-field">
                    <label>Ukuran</label>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <label><input type="checkbox" class="filter-ukuran" value="S"> Size S</label>
                        <label><input type="checkbox" class="filter-ukuran" value="M"> Size M</label>
                        <label><input type="checkbox" class="filter-ukuran" value="L"> Size L</label>
                        <label><input type="checkbox" class="filter-ukuran" value="XL"> Size XL</label>
                    </div>
                </div>
                <div class="user-info-field">
                    <label>Minimal Stok</label>
                    <input type="number" id="filter_stok_min" class="modal-input" placeholder="Minimal stok" min="0" value="0">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="resetFilter()">Reset</button>
            <button class="btn-white" onclick="closeFilterModal()">Batal</button>
            <button class="btn-gold" onclick="applyFilter()">Terapkan Filter</button>
        </div>
    </div>
</div>
<script>
    let currentEditId = null;

    function setChip(el) {
    // Update active class
    el.closest('.filter-chips').querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    
    // Ambil filter dari teks chip (hanya kata pertama sebelum angka)
    let filterText = el.innerText.toLowerCase().replace(/[\(\)0-9]/g, '').trim();
    
    let rows = document.querySelectorAll('#barangTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let statusElement = row.querySelector('.badge');
        let status = statusElement ? statusElement.innerText.toLowerCase().trim() : '';
        
        // Mapping status
        let statusMatch = false;
        if (filterText === 'semua') {
            statusMatch = true;
        } else if (filterText === 'siap' && (status === 'siap sewa' || status === 'siap')) {
            statusMatch = true;
        } else if (filterText === 'disewa' && (status === 'sedang disewa' || status === 'disewa')) {
            statusMatch = true;
        } else if (filterText === 'laundry' && status === 'laundry') {
            statusMatch = true;
        } else if (filterText === 'rusak' && (status === 'perbaikan' || status === 'rusak')) {
            statusMatch = true;
        }
        
        if (statusMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update info count
    let totalRows = document.querySelectorAll('#barangTableBody tr').length;
    let infoText = document.querySelector('.pg-info');
    if (infoText) {
        infoText.innerHTML = `Menampilkan ${visibleCount} dari ${totalRows} koleksi`;
    }
}
    
    // SEARCH FUNCTION
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase().trim();
    let rows = document.querySelectorAll('#barangTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let name = row.querySelector('.baju-name')?.innerText.toLowerCase() || '';
        let size = row.querySelector('td:nth-child(3)')?.innerText.toLowerCase() || '';
        
        if (name.includes(filter) || size.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update info jika tidak ada hasil
    let infoText = document.querySelector('.pg-info');
    if (infoText) {
        if (visibleCount === 0 && filter !== '') {
            infoText.innerHTML = `Tidak ada hasil untuk "<strong>${filter}</strong>"`;
        } else {
            infoText.innerHTML = `Menampilkan ${visibleCount} dari ${rows.length} koleksi`;
        }
    }
});

    // Enable/disable stok input saat checkbox dicentang
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('ukuran-checkbox')) {
            let row = e.target.closest('div');
            let stokInput = row.querySelector('.stok-input');
            if (stokInput) {
                stokInput.disabled = !e.target.checked;
                if (!e.target.checked) stokInput.value = '0';
            }
        }
        if (e.target.classList.contains('edit-ukuran-checkbox')) {
            let row = e.target.closest('div');
            let stokInput = row.querySelector('.edit-stok-input');
            if (stokInput) {
                stokInput.disabled = !e.target.checked;
                if (!e.target.checked) stokInput.value = '0';
            }
        }
    });

    // DETAIL STOK MODAL
    function showStokDetail(id, nama, stokArray) {
        document.getElementById('stokDetailTitle').innerText = 'Detail Stok - ' + nama;
        let content = '';
        let totalStok = 0;
        for (let size in stokArray) {
            content += `<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--gray-100);">
                            <span style="font-weight: 600;">Size ${size}</span>
                            <span>${stokArray[size]} pcs</span>
                        </div>`;
            totalStok += stokArray[size];
        }
        content += `<div style="display: flex; justify-content: space-between; padding: 12px 0; margin-top: 8px; border-top: 2px solid var(--gold); font-weight: 700;">
                        <span>TOTAL STOK</span>
                        <span style="color: var(--gold-dk);">${totalStok} pcs</span>
                    </div>`;
        document.getElementById('stokDetailContent').innerHTML = content;
        document.getElementById('stokDetailModal').classList.add('show');
    }

    function closeStokDetailModal() {
        document.getElementById('stokDetailModal').classList.remove('show');
    }

    @if(session('user')['role'] == 'Owner')
    // TAMBAH BARANG
    function showTambahBarangModal() {
        document.getElementById('new_nama_barang').value = '';
        document.getElementById('new_harga_sewa').value = '';
        
        document.querySelectorAll('#tambahBarangModal .ukuran-checkbox').forEach(cb => {
            cb.checked = false;
            let row = cb.closest('div');
            let stokInput = row.querySelector('.stok-input');
            if (stokInput) {
                stokInput.disabled = true;
                stokInput.value = '0';
            }
        });
        
        document.getElementById('tambahBarangModal').classList.add('show');
    }

    function closeTambahBarangModal() {
        document.getElementById('tambahBarangModal').classList.remove('show');
    }

    function saveBarangBaru() {
    const nama = document.getElementById('new_nama_barang').value;
    const harga = document.getElementById('new_harga_sewa').value;
    
    const ukuranList = [];
    const stokData = {};
    
    document.querySelectorAll('#tambahBarangModal .ukuran-checkbox:checked').forEach(cb => {
        const ukuran = cb.value;
        const row = cb.closest('div');
        const stokInput = row.querySelector('.stok-input');
        const stok = parseInt(stokInput?.value) || 0;
        
        if (stok > 0) {
            ukuranList.push(ukuran);
            stokData[ukuran] = stok;
        }
    });
    
    const ukuran = ukuranList.join(', ');
    const stokJson = JSON.stringify(stokData);
    
    if (!nama) { alert('Nama baju harus diisi!'); return; }
    if (!harga) { alert('Harga sewa harus diisi!'); return; }
    if (ukuranList.length === 0) { alert('Pilih minimal satu ukuran dengan stok lebih dari 0!'); return; }
    
    const formData = new FormData();
    formData.append('nama_barang', nama);
    formData.append('ukuran', ukuran);
    formData.append('harga_sewa', harga);
    formData.append('stok', stokJson);
    formData.append('status_barang', 'Tersedia');
    formData.append('_token', '{{ csrf_token() }}');
    
    // Upload foto
    let fotoFile = document.getElementById('new_foto').files[0];
    if (fotoFile) {
        formData.append('foto', fotoFile);
    }
    
    fetch('{{ route("barang.store") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
        // Jangan set Content-Type, biar browser yang atur untuk FormData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Barang berhasil ditambahkan');
            location.reload();
        } else {
            alert(data.message || 'Gagal menambah barang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error);
    });
}

    // EDIT BARANG
    function showEditBarangModal(id, nama, ukuranStr, harga, stokJson, status, foto) {
    currentEditId = id;
    document.getElementById('edit_nama_barang').value = nama;
    document.getElementById('edit_harga_sewa').value = harga;
    
    // Tampilkan preview foto jika ada
    let previewDiv = document.getElementById('edit_foto_preview');
    if (foto && foto !== 'null' && foto !== '') {
        previewDiv.innerHTML = `<img src="/${foto}" style="max-width: 100px; max-height: 100px; border-radius: 8px; border: 1px solid var(--gray-200);">`;
    } else {
        previewDiv.innerHTML = '<span style="font-size: 12px; color: var(--gray-400);">Belum ada foto</span>';
    }
    
    // Reset checkbox hapus
    let chkHapus = document.getElementById('hapus_foto_checkbox');
    if (chkHapus) chkHapus.checked = false;
    
    // Parse stok data
    let stokData = {};
    if (typeof stokJson === 'string') {
        try { stokData = JSON.parse(stokJson); } catch(e) { stokData = {}; }
    } else if (typeof stokJson === 'object') {
        stokData = stokJson;
    }
    
    // Reset checkbox dan stok input
    document.querySelectorAll('#editBarangModal .edit-ukuran-checkbox').forEach(cb => {
        cb.checked = false;
        let row = cb.closest('div');
        let stokInput = row.querySelector('.edit-stok-input');
        if (stokInput) {
            stokInput.disabled = true;
            stokInput.value = '0';
        }
    });
    
    // Centang ukuran yang ada
    for (let size in stokData) {
        let checkbox = document.querySelector(`#editBarangModal .edit-ukuran-checkbox[value="${size}"]`);
        if (checkbox) {
            checkbox.checked = true;
            let row = checkbox.closest('div');
            let stokInput = row.querySelector('.edit-stok-input');
            if (stokInput) {
                stokInput.disabled = false;
                stokInput.value = stokData[size];
            }
        }
    }
    
    document.getElementById('editBarangModal').classList.add('show');
}

    function closeEditBarangModal() {
        document.getElementById('editBarangModal').classList.remove('show');
    }

    function updateBarang() {
    const nama = document.getElementById('edit_nama_barang').value;
    const harga = document.getElementById('edit_harga_sewa').value;
    
    const ukuranList = [];
    const stokData = {};
    
    document.querySelectorAll('#editBarangModal .edit-ukuran-checkbox:checked').forEach(cb => {
        const ukuran = cb.value;
        const row = cb.closest('div');
        const stokInput = row.querySelector('.edit-stok-input');
        const stok = parseInt(stokInput?.value) || 0;
        
        if (stok > 0) {
            ukuranList.push(ukuran);
            stokData[ukuran] = stok;
        }
    });
    
    const ukuran = ukuranList.join(', ');
    const stokJson = JSON.stringify(stokData);
    
    if (!nama) { alert('Nama baju harus diisi!'); return; }
    if (!harga) { alert('Harga sewa harus diisi!'); return; }
    if (ukuranList.length === 0) { alert('Pilih minimal satu ukuran dengan stok lebih dari 0!'); return; }
    
    const formData = new FormData();
    formData.append('nama_barang', nama);
    formData.append('ukuran', ukuran);
    formData.append('harga_sewa', harga);
    formData.append('stok', stokJson);
    formData.append('_method', 'PUT');
    formData.append('_token', '{{ csrf_token() }}');
    
    // Upload foto baru jika ada
    let fotoFile = document.getElementById('edit_foto').files[0];
    if (fotoFile) {
        formData.append('foto', fotoFile);
    }
    
    // Hapus foto jika checkbox centang
    let hapusFoto = document.getElementById('hapus_foto_checkbox').checked;
    if (hapusFoto) {
        formData.append('hapus_foto', '1');
    }
    
    fetch('/barang/' + currentEditId, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Barang berhasil diupdate');
            location.reload();
        } else {
            alert(data.message || 'Gagal update barang');
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}
    @endif

   function showFotoDetail(fotoUrl, namaBarang) {
    console.log('Foto URL:', fotoUrl); // Debug: lihat di console browser
    
    document.getElementById('fotoDetailTitle').innerHTML = 'Foto - ' + namaBarang;
    const imgElement = document.getElementById('fotoDetailImage');
    
    if (fotoUrl && fotoUrl !== 'null' && fotoUrl !== '') {
        // Pastikan path dimulai dengan /
        let imgPath = fotoUrl.startsWith('/') ? fotoUrl : '/' + fotoUrl;
        console.log('Image path:', imgPath); // Debug
        imgElement.src = imgPath;
        imgElement.alt = namaBarang;
    } else {
        imgElement.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23C9A84C" stroke-width="1"%3E%3Crect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"%3E%3C/rect%3E%3Cpath d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"%3E%3C/path%3E%3C/svg%3E';
        imgElement.alt = 'Tidak ada foto';
    }
    
    document.getElementById('fotoDetailModal').classList.add('show');
}

function closeFotoDetailModal() {
    document.getElementById('fotoDetailModal').classList.remove('show');
}

function zoomFoto() {
    const imgSrc = document.getElementById('fotoDetailImage').src;
    const imgAlt = document.getElementById('fotoDetailImage').alt;
    
    if (imgSrc && !imgSrc.includes('svg')) {
        document.getElementById('zoomImage').src = imgSrc;
        document.getElementById('zoomImage').alt = imgAlt;
        document.getElementById('zoomFotoModal').classList.add('show');
    }
}

function closeZoomModal() {
    document.getElementById('zoomFotoModal').classList.remove('show');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFotoDetailModal();
        closeStokDetailModal();
    }
});

// FILTER MODAL
function showFilterModal() {
    // Reset nilai filter ke default sebelum tampil
    document.getElementById('filter_harga_min').value = '';
    document.getElementById('filter_harga_max').value = '';
    document.querySelectorAll('.filter-ukuran').forEach(cb => cb.checked = false);
    document.getElementById('filter_stok_min').value = '0';
    
    document.getElementById('filterModal').classList.add('show');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.remove('show');
}

function resetFilter() {
    document.getElementById('filter_harga_min').value = '';
    document.getElementById('filter_harga_max').value = '';
    document.querySelectorAll('.filter-ukuran').forEach(cb => cb.checked = false);
    document.getElementById('filter_stok_min').value = '0';
    applyFilter();
}

function applyFilter() {
    const hargaMin = parseInt(document.getElementById('filter_harga_min').value) || 0;
    const hargaMax = parseInt(document.getElementById('filter_harga_max').value) || 999999999;
    
    // Ambil ukuran yang dipilih
    const selectedSizes = [];
    document.querySelectorAll('.filter-ukuran:checked').forEach(cb => {
        selectedSizes.push(cb.value);
    });
    
    const stokMin = parseInt(document.getElementById('filter_stok_min').value) || 0;
    
    let rows = document.querySelectorAll('#barangTableBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let harga = parseInt(row.querySelector('.td-gold')?.innerText.replace(/[^0-9]/g, '')) || 0;
        let ukuranCell = row.querySelector('td:nth-child(3)')?.innerText || '';
        let stokTotal = parseInt(row.querySelector('td:nth-child(4)')?.innerText.split(' ')[0]) || 0;
        
        // Cek harga
        let hargaMatch = (harga >= hargaMin && harga <= hargaMax);
        
        // Cek ukuran (jika ada filter ukuran)
        let ukuranMatch = true;
        if (selectedSizes.length > 0) {
            ukuranMatch = false;
            for (let size of selectedSizes) {
                if (ukuranCell.includes(size)) {
                    ukuranMatch = true;
                    break;
                }
            }
        }
        
        // Cek stok
        let stokMatch = (stokTotal >= stokMin);
        
        if (hargaMatch && ukuranMatch && stokMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update info
    let totalRows = document.querySelectorAll('#barangTableBody tr').length;
    let infoText = document.querySelector('.pg-info');
    if (infoText) {
        infoText.innerHTML = `Menampilkan ${visibleCount} dari ${totalRows} koleksi (filter aktif)`;
    }
    
    closeFilterModal();
    
    // Tampilkan notifikasi filter aktif
    if (hargaMin > 0 || hargaMax < 999999999 || selectedSizes.length > 0 || stokMin > 0) {
        let filterBadge = document.createElement('div');
        filterBadge.id = 'filterActiveBadge';
        filterBadge.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: var(--gold); color: var(--black); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; z-index: 999; cursor: pointer;';
        filterBadge.innerHTML = '🔍 Filter Aktif ✕';
        filterBadge.onclick = function() { resetFilter(); this.remove(); };
        
        // Hapus badge lama jika ada
        let oldBadge = document.getElementById('filterActiveBadge');
        if (oldBadge) oldBadge.remove();
        
        document.body.appendChild(filterBadge);
        
        // Auto remove setelah 5 detik
        setTimeout(() => {
            let badge = document.getElementById('filterActiveBadge');
            if (badge) badge.remove();
        }, 5000);
    }
}
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
.btn-stok-detail {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    margin-left: 8px;
    color: var(--gold-dk);
    padding: 2px 5px;
    border-radius: 4px;
}
.btn-stok-detail:hover {
    background: var(--gold-xs);
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
    max-width: 500px;
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
.foto-thumbnail {
    width: 40px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    transition: transform 0.2s;
}
.foto-thumbnail:hover {
    transform: scale(1.05);
    border-color: var(--gold);
}

#filterModal .modal-popup {
    max-width: 400px;
}
#filterModal .user-info-field {
    margin-bottom: 16px;
}

/* ZOOM MODAL STYLES */
#zoomFotoModal {
    background: rgba(0,0,0,0.9);
    backdrop-filter: blur(8px);
}
#zoomFotoModal .zoom-container {
    display: flex;
    align-items: center;
    justify-content: center;
}
.zoom-close {
    transition: all 0.2s;
}
.zoom-close:hover {
    transform: scale(1.1);
    color: var(--gold);
}
</style>
@endsection