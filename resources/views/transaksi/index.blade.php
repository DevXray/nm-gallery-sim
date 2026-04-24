@extends('layouts.app')

@section('title', 'Transaksi Penyewaan')
@section('breadcrumb', 'Transaksi & E-Nota')

@section('content')
<div class="page active" id="page-transaction">

    <div class="pg-head">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div>
                <div class="pg-title">Transaksi Penyewaan</div>
                <div class="pg-sub">Isi form di bawah untuk membuat transaksi sewa baru</div>
            </div>
        </div>
    </div>

    <div class="trx-layout">

        <!-- LEFT: Form -->
        <div class="form-card">

            <!-- STEP 0: PILIH BARANG -->
            <div class="form-sect" style="background: var(--gold-xs); border-bottom: 2px solid var(--gold-md);">
                <div class="form-sect-lbl" style="color: var(--gold-dk);">🎯 0 · Pilih Barang</div>
                <div class="fgrid">
                    <div class="field f-full">
                        <label class="flbl">Pilih Barang yang Akan Disewa *</label>
                        <select id="pilih_barang" class="finput" style="font-size: 14px; padding: 10px;">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id_barang }}" 
                                        data-nama="{{ $barang->nama_barang }}"
                                        data-harga="{{ $barang->harga_sewa }}"
                                        data-stok="{{ $barang->stok }}"
                                        {{ ($selectedBarang && $selectedBarang->id_barang == $barang->id_barang) ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_sewa, 0, ',', '.') }}/hari
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- INFORMASI BARANG YANG DIPILIH -->
            <input type="hidden" id="selected_barang_id" value="{{ $selectedBarang->id_barang ?? '' }}">
            <input type="hidden" id="selected_barang_nama" value="{{ $selectedBarang->nama_barang ?? '' }}">
            <input type="hidden" id="selected_barang_harga" value="{{ $selectedBarang->harga_sewa ?? 0 }}">
            <input type="hidden" id="selected_barang_stok" value="{{ $selectedBarang->stok ?? '{}' }}">

            <!-- Info Barang yang Disewa -->
            <div class="form-sect" id="info_barang_section" style="{{ $selectedBarang ? '' : 'display:none;' }} background: var(--gold-xs); border-bottom: 1px solid var(--gold-md);">
                <div class="form-sect-lbl" style="color: var(--gold-dk);">📦 Barang yang Disewa</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 15px; font-weight: 700; color: var(--black);" id="display_barang_nama">
                            {{ $selectedBarang->nama_barang ?? '' }}
                        </div>
                        <div style="font-size: 12px; color: var(--gray-500); margin-top: 2px;" id="display_barang_harga">
                            @if($selectedBarang)
                                Rp {{ number_format($selectedBarang->harga_sewa, 0, ',', '.') }} / hari
                            @endif
                        </div>
                    </div>
                    <div style="font-size: 32px;">👘</div>
                </div>
            </div>

            <!-- Step 1: Data Pelanggan -->
            <div class="form-sect">
                <div class="form-sect-lbl">1 · Data Pelanggan</div>
                <div class="fgrid">
                    <div class="field">
                        <label class="flbl">Nama Pelanggan *</label>
                        <input type="text" id="nama_pelanggan" class="finput" placeholder="Nama lengkap pelanggan" required 
                               value="{{ $selectedPelanggan->nama_pelanggan ?? '' }}">
                    </div>
                    <div class="field">
                        <label class="flbl">No. Telepon *</label>
                        <input type="text" id="no_telp" class="finput" placeholder="0812-xxxx-xxxx" required
                               value="{{ $selectedPelanggan->no_telp ?? '' }}">
                    </div>
                    <div class="field f-full">
                        <label class="flbl">Alamat / Catatan</label>
                        <input type="text" id="alamat" class="finput" placeholder="Alamat lengkap pelanggan"
                               value="{{ $selectedPelanggan->alamat ?? '' }}">
                    </div>
                </div>
            </div>

            <!-- Step 2: Periode & Opsi -->
            <div class="form-sect">
                <div class="form-sect-lbl">2 · Periode &amp; Opsi</div>
                <div class="fgrid">
                    <div class="field">
                        <label class="flbl">Tanggal Mulai Sewa</label>
                        <input type="date" id="tgl_sewa" class="finput" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="field">
                        <label class="flbl">Tanggal Kembali *</label>
                        <input type="date" id="tgl_jatuh_tempo" class="finput">
                    </div>
                    
                    <!-- PILIH UKURAN & JUMLAH -->
                    <div class="field f-full">
                        <label class="flbl">Pilih Ukuran & Jumlah</label>
                        <div id="ukuran-container" style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="padding: 20px; text-align: center; color: var(--gray-500);" id="empty-ukuran-message">
                                ⚠️ Silakan pilih barang terlebih dahulu
                            </div>
                        </div>
                        <div id="stok-warning" style="display:none; margin-top: 8px; padding: 8px; background: #fee2e2; border-radius: 8px; color: #dc2626; font-size: 12px;">
                            ⚠️ Stok tidak mencukupi! Silakan kurangi jumlah.
                        </div>
                    </div>
                    
                    <div class="field">
                        <label class="flbl">Jenis Pembayaran</label>
                        <select id="metode_bayar" class="fselect">
                            <option value="Bayar Penuh">Bayar Penuh</option>
                            <option value="DP 50%">DP 50% Dulu</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;background:var(--gray-50)">
                <button type="button" class="btn-white" onclick="simpanDraft()">Simpan Draft</button>
                <button type="button" class="btn-gold" onclick="showModal()" id="btnGenerate" disabled>Generate E-Nota &amp; Proses →</button>
            </div>
        </div>

        <!-- RIGHT: Preview E-Nota -->
        <div class="nota-panel">
            <div class="nota-preview-hd">
                <div class="nota-preview-title">Preview E-Nota</div>
                <span style="font-size:10.5px;color:var(--gray-400)">Auto-generate</span>
            </div>

            <div class="nota-paper">
                <div class="nota-top">
                    <div class="nota-brand">NM Gallery</div>
                    <div class="nota-tagline">Baju Bodo Collection</div>
                    <div class="nota-trx-label">Nomor Transaksi</div>
                    <div class="nota-trx-num" id="previewNotaNum">#TRX-NEW</div>
                </div>
                <div class="nota-body">
                    <div class="nota-row"><span class="nota-key">Pelanggan</span><span class="nota-val" id="previewPelanggan">—</span></div>
                    <div class="nota-row"><span class="nota-key">Baju</span><span class="nota-val" id="previewBarang">—</span></div>
                    <div class="nota-row"><span class="nota-key">Periode</span><span class="nota-val" id="previewPeriode">—</span></div>
                    <div class="nota-row"><span class="nota-key">Durasi</span><span class="nota-val" id="previewDurasi" style="color:var(--gold-dk);font-weight:700">—</span></div>
                    <div class="nota-row"><span class="nota-key">Harga/hari</span><span class="nota-val" id="previewHarga">—</span></div>
                    <div id="previewItems"></div>
                    <div class="nota-total-box">
                        <span class="nota-total-lbl">Total</span>
                        <span class="nota-total-val" id="previewTotal">Rp 0</span>
                    </div>
                    <div class="nota-footer">
                        Terima kasih telah mempercayakan momen<br>Anda kepada <b>NM Gallery</b> ✦ Makassar
                    </div>
                </div>
            </div>

            <button class="nota-gen-btn" onclick="showModal()">🖨 Generate &amp; Cetak E-Nota</button>
        </div>
    </div>
</div>

<!-- Modal E-Nota (sama seperti sebelumnya, tidak diubah) -->
<div class="overlay" id="notaOverlay" onclick="closeModal(event)">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="modal-hd">
            <div class="modal-hd-left">
                <div class="modal-ico">N</div>
                <div>
                    <div class="modal-title">E-Nota Digital</div>
                    <div class="modal-sub">NM Gallery · Baju Bodo Collection</div>
                </div>
            </div>
            <button class="modal-x" onclick="closeModal()">✕</button>
        </div>

        <div class="modal-nota">
            <div class="modal-nota-top">
                <div>
                    <div class="modal-nota-brand">NM Gallery</div>
                    <div class="modal-nota-tagline">Baju Bodo Authentic Collection</div>
                    <div class="modal-nota-addr">Jl. Somba Opu No. 12, Makassar<br>+62 411-xxx-xxxx · @nmgallery.id</div>
                </div>
                <div class="modal-nota-trxid">
                    <div class="label">No. Transaksi</div>
                    <div class="num" id="modalNotaNum">#TRX-NEW</div>
                    <div class="date" id="modalTanggal">{{ date('d M Y · H:i') }} WIB</div>
                </div>
            </div>

            <div class="modal-nota-body">
                <div class="nota-cust-grid">
                    <div><div class="nota-field-k">Nama Pelanggan</div><div class="nota-field-v" id="modalNama">—</div></div>
                    <div><div class="nota-field-k">No. Telepon</div><div class="nota-field-v" id="modalTelp">—</div></div>
                    <div><div class="nota-field-k">Periode Sewa</div><div class="nota-field-v" id="modalPeriode">—</div></div>
                </div>

                <table class="nota-items-tbl">
                    <thead>
                        <tr><th>Item</th><th>Harga/hr</th><th>Hari</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody id="modalItems"></tbody>
                </table>

                <div class="nota-totals">
                    <div class="nota-tot-row"><span class="nota-tot-lbl">Subtotal Sewa</span><span class="nota-tot-val" id="modalSubtotal">Rp 0</span></div>
                    <div class="nota-tot-row"><span class="nota-tot-lbl">Jaminan</span><span class="nota-tot-val">Rp 200.000</span></div>
                    <div class="nota-tot-row"><span class="nota-tot-lbl" id="modalDpLabel">DP Dibayar (50%)</span><span class="nota-tot-val" id="modalDp" style="color:#1a8050">−Rp 0</span></div>
                    <div class="nota-tot-row grand"><span class="nota-tot-lbl">TOTAL DIBAYAR</span><span class="nota-tot-val" id="modalTotal">Rp 0</span></div>
                </div>

                <div class="nota-foot">
                    Terima kasih telah mempercayakan momen spesial Anda kepada <b>NM Gallery</b>.<br>
                    Nota ini sah sebagai bukti transaksi. ✦ Makassar, Sulawesi Selatan
                </div>
            </div>
        </div>

        <div class="modal-acts">
            <button class="btn-print" onclick="printNota()">🖨 Cetak Nota</button>
            <button class="btn-share" onclick="simpanTransaksi()">✅ Simpan Transaksi</button>
        </div>
    </div>
</div>

<form id="transaksiForm" action="{{ route('transaksi.store') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="nama_pelanggan" id="form_nama_pelanggan">
    <input type="hidden" name="no_telp" id="form_no_telp">
    <input type="hidden" name="alamat" id="form_alamat">
    <input type="hidden" name="id_barang" id="form_id_barang">
    <input type="hidden" name="items" id="form_items">
    <input type="hidden" name="tgl_sewa" id="form_tgl_sewa">
    <input type="hidden" name="tgl_jatuh_tempo" id="form_tgl_jatuh_tempo">
    <input type="hidden" name="metode_bayar" id="form_metode_bayar">
</form>

<script>
    let selectedBarangId = document.getElementById('selected_barang_id').value;
    let selectedBarangNama = document.getElementById('selected_barang_nama').value;
    let selectedBarangHarga = parseInt(document.getElementById('selected_barang_harga').value) || 0;
    let selectedBarangStok = {};

    // Fungsi untuk parse stok dari JSON
    function parseStok(stokString) {
        try {
            if (!stokString) return {};
            if (typeof stokString === 'object') return stokString;
            return JSON.parse(stokString);
        } catch(e) {
            console.error('Error parsing stok:', e);
            return {};
        }
    }

    // Fungsi untuk load ukuran berdasarkan barang yang dipilih
    function loadUkuranBarang(barangId, barangNama, barangHarga, barangStok) {
        selectedBarangId = barangId;
        selectedBarangNama = barangNama;
        selectedBarangHarga = barangHarga;
        selectedBarangStok = parseStok(barangStok);
        
        // Update display
        document.getElementById('selected_barang_id').value = barangId;
        document.getElementById('selected_barang_nama').value = barangNama;
        document.getElementById('selected_barang_harga').value = barangHarga;
        document.getElementById('selected_barang_stok').value = typeof barangStok === 'string' ? barangStok : JSON.stringify(barangStok);
        
        // Tampilkan section info barang
        const infoSection = document.getElementById('info_barang_section');
        if (barangId) {
            infoSection.style.display = 'block';
            document.getElementById('display_barang_nama').innerText = barangNama;
            document.getElementById('display_barang_harga').innerHTML = 'Rp ' + barangHarga.toLocaleString('id-ID') + ' / hari';
        } else {
            infoSection.style.display = 'none';
        }
        
        // Generate input ukuran
        const container = document.getElementById('ukuran-container');
        const stokArray = selectedBarangStok;
        
        if (Object.keys(stokArray).length > 0) {
            let html = '';
            for (const [size, stokVal] of Object.entries(stokArray)) {
                html += `
                    <div class="ukuran-item" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap; padding: 8px; background: var(--gray-50); border-radius: 8px;">
                        <label style="min-width: 80px; font-weight: 700; color: var(--gold-dk);">Size ${size}</label>
                        <input type="number" class="jumlah-ukuran" 
                               data-size="${size}" data-stok="${stokVal}"
                               style="width: 100px; padding: 10px; border: 2px solid var(--gray-200); border-radius: 8px; font-size: 14px; text-align: center;" 
                               min="0" max="${stokVal}" value="0" step="1">
                        <span style="font-size: 13px; color: var(--gray-600);">Stok tersedia: <strong style="color: var(--gold-dk);">${stokVal}</strong> pcs</span>
                    </div>
                `;
            }
            container.innerHTML = html;
            
            // Add event listeners
            document.querySelectorAll('.jumlah-ukuran').forEach(input => {
                input.addEventListener('change', updatePreview);
                input.addEventListener('input', updatePreview);
            });
        } else {
            container.innerHTML = '<div style="padding: 20px; text-align: center; background: #fee2e2; border-radius: 8px; color: #dc2626;">⚠️ Belum ada data ukuran untuk barang ini. Silakan update data barang di halaman Inventaris.</div>';
        }
        
        updatePreview();
    }

    // Event listener untuk pilih barang
    document.getElementById('pilih_barang').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            const barangId = this.value;
            const barangNama = selectedOption.getAttribute('data-nama');
            const barangHarga = parseInt(selectedOption.getAttribute('data-harga'));
            const barangStok = selectedOption.getAttribute('data-stok');
            
            loadUkuranBarang(barangId, barangNama, barangHarga, barangStok);
        } else {
            // Reset jika tidak ada barang dipilih
            selectedBarangId = '';
            selectedBarangNama = '';
            selectedBarangHarga = 0;
            selectedBarangStok = {};
            document.getElementById('info_barang_section').style.display = 'none';
            document.getElementById('ukuran-container').innerHTML = '<div style="padding: 20px; text-align: center; color: var(--gray-500);">⚠️ Silakan pilih barang terlebih dahulu</div>';
            updatePreview();
        }
    });

    function getDataPelanggan() {
        return {
            nama: document.getElementById('nama_pelanggan').value,
            telp: document.getElementById('no_telp').value,
            alamat: document.getElementById('alamat').value
        };
    }

    function hitungHari() {
        let tglSewa = document.getElementById('tgl_sewa').value;
        let tglKembali = document.getElementById('tgl_jatuh_tempo').value;
        if (tglSewa && tglKembali) {
            let start = new Date(tglSewa);
            let end = new Date(tglKembali);
            let diffTime = Math.abs(end - start);
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays === 0 ? 1 : diffDays;
        }
        return 0;
    }

    function getSelectedItems() {
        const items = [];
        let hasError = false;
        
        document.querySelectorAll('.jumlah-ukuran').forEach(input => {
            const jumlah = parseInt(input.value) || 0;
            const size = input.getAttribute('data-size');
            const stok = parseInt(input.getAttribute('data-stok'));
            
            if (jumlah > 0) {
                if (jumlah > stok) {
                    hasError = true;
                    input.style.borderColor = '#dc2626';
                    input.style.backgroundColor = '#fee2e2';
                } else {
                    input.style.borderColor = '#e4e4e7';
                    input.style.backgroundColor = 'white';
                    items.push({ size, jumlah, harga: selectedBarangHarga });
                }
            } else {
                input.style.borderColor = '#e4e4e7';
                input.style.backgroundColor = 'white';
            }
        });
        
        return { items, hasError };
    }

    function updatePreview() {
        let tglSewa = document.getElementById('tgl_sewa').value;
        let tglKembali = document.getElementById('tgl_jatuh_tempo').value;
        let jumlahHari = hitungHari();
        let pelanggan = getDataPelanggan();
        let { items, hasError } = getSelectedItems();
        
        let totalBiaya = 0;
        let itemsPreviewHtml = '';
        
        items.forEach(item => {
            let subtotal = item.harga * item.jumlah * jumlahHari;
            totalBiaya += subtotal;
            itemsPreviewHtml += `<div class="nota-row"><span class="nota-key">Size ${item.size} x ${item.jumlah}</span><span class="nota-val">Rp ${subtotal.toLocaleString('id-ID')}</span></div>`;
        });
        
        document.getElementById('previewPelanggan').innerText = pelanggan.nama || '—';
        document.getElementById('previewBarang').innerText = selectedBarangNama || '—';
        document.getElementById('previewPeriode').innerText = (tglSewa && tglKembali) ? tglSewa + ' s/d ' + tglKembali : '—';
        document.getElementById('previewDurasi').innerText = jumlahHari ? jumlahHari + ' hari' : '—';
        document.getElementById('previewHarga').innerText = selectedBarangHarga ? 'Rp ' + selectedBarangHarga.toLocaleString('id-ID') + '/hari' : '—';
        
        const previewTotalElement = document.getElementById('previewTotal');
        if (previewTotalElement) {
            previewTotalElement.innerHTML = totalBiaya ? 'Rp ' + totalBiaya.toLocaleString('id-ID') : 'Rp 0';
        }
        
        const previewContainer = document.getElementById('previewItems');
        if (previewContainer) previewContainer.innerHTML = itemsPreviewHtml;
        
        const btnGenerate = document.getElementById('btnGenerate');
        const warning = document.getElementById('stok-warning');
        
        // Validasi button generate
        if (!selectedBarangId) {
            btnGenerate.disabled = true;
            if (warning) warning.style.display = 'none';
        } else if (hasError) {
            btnGenerate.disabled = true;
            if (warning) {
                warning.style.display = 'block';
                warning.innerHTML = '⚠️ Stok tidak mencukupi! Silakan kurangi jumlah.';
            }
        } else if (items.length === 0) {
            btnGenerate.disabled = true;
            if (warning) {
                warning.style.display = 'block';
                warning.innerHTML = '⚠️ Minimal pilih 1 item dengan jumlah lebih dari 0!';
            }
        } else {
            btnGenerate.disabled = false;
            if (warning) warning.style.display = 'none';
        }
    }

    // Set default tanggal kembali (3 hari dari sekarang)
    function setDefaultTanggalKembali() {
        let today = new Date();
        let threeDaysLater = new Date(today);
        threeDaysLater.setDate(today.getDate() + 3);
        let yyyy = threeDaysLater.getFullYear();
        let mm = String(threeDaysLater.getMonth() + 1).padStart(2, '0');
        let dd = String(threeDaysLater.getDate()).padStart(2, '0');
        document.getElementById('tgl_jatuh_tempo').value = yyyy + '-' + mm + '-' + dd;
        updatePreview();
    }

    // Event listeners
    document.getElementById('tgl_sewa').addEventListener('change', updatePreview);
    document.getElementById('tgl_jatuh_tempo').addEventListener('change', updatePreview);
    document.getElementById('nama_pelanggan').addEventListener('input', updatePreview);
    document.getElementById('no_telp').addEventListener('input', updatePreview);
    document.getElementById('alamat').addEventListener('input', updatePreview);

    // Inisialisasi
    setDefaultTanggalKembali();
    
    // Jika sudah ada selectedBarang dari parameter
    if (selectedBarangId) {
        // Trigger change event untuk load ukuran
        const selectElement = document.getElementById('pilih_barang');
        if (selectElement) {
            selectElement.value = selectedBarangId;
            // Trigger change event manually
            const event = new Event('change');
            selectElement.dispatchEvent(event);
        }
    }

    function showModal() {
        let pelanggan = getDataPelanggan();
        if (!pelanggan.nama) { alert('Isi nama pelanggan dulu!'); return; }
        if (!pelanggan.telp) { alert('Isi no telepon pelanggan dulu!'); return; }
        
        let { items, hasError } = getSelectedItems();
        if (hasError) { alert('Jumlah melebihi stok yang tersedia!'); return; }
        if (items.length === 0) { alert('Pilih minimal satu ukuran dengan jumlah lebih dari 0!'); return; }
        
        let tglSewa = document.getElementById('tgl_sewa').value;
        let tglKembali = document.getElementById('tgl_jatuh_tempo').value;
        let jumlahHari = hitungHari();
        let metode = document.getElementById('metode_bayar').value;
        let kodeNota = '#TRX-' + Math.floor(Math.random() * 10000);
        
        let totalBiaya = 0;
        let itemsModalHtml = '';
        
        items.forEach(item => {
            let subtotal = item.harga * item.jumlah * jumlahHari;
            totalBiaya += subtotal;
            itemsModalHtml += `<tr><td>${selectedBarangNama} (${item.size}) x${item.jumlah}</td>
                                   <td style="color:var(--gray-400);font-family:monospace">${item.harga.toLocaleString('id-ID')}</td>
                                   <td style="text-align:center">×${jumlahHari}</td>
                                   <td style="color:var(--gold-dk)">Rp ${subtotal.toLocaleString('id-ID')}</td>
                               </tr>`;
        });
        
        let formatTglSewa = tglSewa ? new Date(tglSewa).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'}) : '';
        let formatTglKembali = tglKembali ? new Date(tglKembali).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'}) : '';
        
        document.getElementById('previewNotaNum').innerText = kodeNota;
        document.getElementById('modalNotaNum').innerText = kodeNota;
        document.getElementById('modalNama').innerText = pelanggan.nama;
        document.getElementById('modalTelp').innerText = pelanggan.telp;
        document.getElementById('modalPeriode').innerText = (formatTglSewa && formatTglKembali) ? formatTglSewa + ' – ' + formatTglKembali : '—';
        document.getElementById('modalItems').innerHTML = itemsModalHtml;
        document.getElementById('modalSubtotal').innerHTML = `Rp ${totalBiaya.toLocaleString('id-ID')}`;
        
        let dp = metode === 'DP 50%' ? totalBiaya / 2 : totalBiaya;
        let dpDisplay = metode === 'DP 50%' ? `−Rp ${(totalBiaya/2).toLocaleString('id-ID')}` : `Rp ${totalBiaya.toLocaleString('id-ID')}`;
        document.getElementById('modalDp').innerHTML = dpDisplay;
        
        let finalTotal = (metode === 'DP 50%') ? (totalBiaya / 2) + 200000 : totalBiaya + 200000;
        document.getElementById('modalTotal').innerHTML = `Rp ${finalTotal.toLocaleString('id-ID')}`;
        
        document.getElementById('form_nama_pelanggan').value = pelanggan.nama;
        document.getElementById('form_no_telp').value = pelanggan.telp;
        document.getElementById('form_alamat').value = pelanggan.alamat;
        document.getElementById('form_id_barang').value = selectedBarangId;
        document.getElementById('form_items').value = JSON.stringify(items);
        document.getElementById('form_tgl_sewa').value = tglSewa;
        document.getElementById('form_tgl_jatuh_tempo').value = tglKembali;
        document.getElementById('form_metode_bayar').value = metode;
        
        document.getElementById('notaOverlay').classList.add('show');
    }

    function simpanTransaksi() {
        document.getElementById('transaksiForm').submit();
    }

    function simpanDraft() {
        alert('Draft disimpan! (Fitur akan segera hadir)');
    }

    function printNota() {
        let nomorNota = document.getElementById('modalNotaNum').innerText;
        let tanggal = document.getElementById('modalTanggal').innerText;
        let namaPelanggan = document.getElementById('modalNama').innerText;
        let telpPelanggan = document.getElementById('modalTelp').innerText;
        let periodeSewa = document.getElementById('modalPeriode').innerText;
        let totalBayar = document.getElementById('modalTotal').innerText;
        let itemsHTML = document.getElementById('modalItems').innerHTML;
        
        let printWindow = window.open('', '_blank', 'width=500,height=600');
        printWindow.document.write(`<!DOCTYPE html><html><head><title>E-Nota NM Gallery - ${nomorNota}</title><meta charset="UTF-8"><style>body{font-family:'Courier New',monospace;background:#fff;padding:20px}.nota{max-width:350px;margin:0 auto;border:1px solid #ddd;background:white}.nota-header{background:#0a0a0a;color:#e0c06e;padding:20px;text-align:center;border-bottom:2px solid #C9A84C}.nota-title{font-size:22px;font-weight:bold}.nota-subtitle{font-size:9px;color:rgba(255,255,255,0.4)}.nota-address{font-size:9px;color:rgba(255,255,255,0.3)}.nota-body{padding:15px}.nota-info{border-bottom:1px dashed #ddd;padding-bottom:10px;margin-bottom:10px}.info-row{display:flex;justify-content:space-between;font-size:10px;padding:4px 0}.nota-items{width:100%;border-collapse:collapse;margin:10px 0;font-size:10px}.nota-items th{text-align:left;padding:6px 0;border-bottom:1px solid #ddd}.nota-items td{padding:6px 0;border-bottom:1px solid #f0f0f0}.nota-total{border-top:1px solid #ddd;margin-top:10px;padding-top:10px}.total-row{display:flex;justify-content:space-between;padding:5px 0}.total-grand{font-size:14px;font-weight:bold;border-top:1px solid #ddd;margin-top:5px;padding-top:8px}.total-grand .info-value{font-size:16px;color:#C9A84C}.nota-footer{text-align:center;font-size:9px;color:#999;border-top:1px dashed #ddd;padding:12px;margin-top:10px}.signature{margin-top:20px;text-align:center;font-size:9px}.signature-line{margin-top:30px;border-top:1px solid #ddd;width:150px;margin:30px auto 5px}@media print{body{padding:0}.nota{box-shadow:none;border:none}}</style></head><body><div class="nota"><div class="nota-header"><div class="nota-title">NM GALLERY</div><div class="nota-subtitle">Baju Bodo Collection</div><div class="nota-address">Jl. Somba Opu No. 12, Makassar</div></div><div class="nota-body"><div class="nota-info"><div class="info-row"><span class="info-label">No. Transaksi</span><span>${nomorNota}</span></div><div class="info-row"><span class="info-label">Tanggal</span><span>${tanggal}</span></div></div><div class="nota-info"><div class="info-row"><span class="info-label">Pelanggan</span><span>${namaPelanggan}</span></div><div class="info-row"><span class="info-label">No. Telepon</span><span>${telpPelanggan}</span></div><div class="info-row"><span class="info-label">Periode Sewa</span><span>${periodeSewa}</span></div></div><table class="nota-items"><thead><tr><th>Item</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead><tbody>${itemsHTML}</tbody></table><div class="nota-total"><div class="total-row total-grand"><span class="info-label">TOTAL</span><span class="info-value">${totalBayar}</span></div></div><div class="nota-footer">Terima kasih telah mempercayakan momen spesial Anda kepada NM Gallery<br>✦ Nota ini sah sebagai bukti transaksi ✦</div><div class="signature"><div class="signature-line"></div>Hormat Kami,<br>NM Gallery</div></div></div><script>window.onload=function(){window.print();setTimeout(function(){window.close();},500)};<\/script></body></html>`);
        printWindow.document.close();
    }

    function closeModal(e) {
        if (!e || e.target === document.getElementById('notaOverlay')) {
            document.getElementById('notaOverlay').classList.remove('show');
        }
    }
</script>

<style>
.trx-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 18px;
}
.form-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
}
.form-sect {
    padding: 20px 24px;
    border-bottom: 1px solid var(--gray-100);
}
.form-sect-lbl {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-sect-lbl::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--gray-100);
}
.fgrid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 13px;
}
.f-full {
    grid-column: 1 / -1;
}
.field {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.flbl {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--gray-600);
}
.finput, .fselect {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 8.5px 12px;
    font-size: 13px;
    font-family: inherit;
}
.finput:focus, .fselect:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px var(--gold-xs);
}
.nota-panel {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
    position: sticky;
    top: 0;
}
.nota-preview-hd {
    padding: 13px 16px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
}
.nota-preview-title {
    font-size: 12.5px;
    font-weight: 700;
}
.nota-paper {
    margin: 14px;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    overflow: hidden;
}
.nota-top {
    background: var(--black);
    padding: 14px 16px;
}
.nota-brand {
    font-family: 'Instrument Serif', serif;
    font-style: italic;
    font-size: 20px;
    color: var(--gold-lt);
}
.nota-tagline {
    font-size: 8.5px;
    color: rgba(255,255,255,0.25);
    text-transform: uppercase;
}
.nota-trx-label {
    font-size: 8.5px;
    color: rgba(255,255,255,0.3);
    margin-top: 8px;
    text-transform: uppercase;
}
.nota-trx-num {
    font-family: monospace;
    font-size: 12px;
    color: var(--gold-lt);
}
.nota-body {
    padding: 13px 14px;
}
.nota-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 11.5px;
    border-bottom: 1px solid var(--gray-100);
}
.nota-key {
    color: var(--gray-500);
}
.nota-val {
    color: var(--black);
    font-weight: 500;
    text-align: right;
}
.nota-total-box {
    background: var(--gray-50);
    border: 1px solid var(--gold-md);
    border-radius: 8px;
    padding: 10px 12px;
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
}
.nota-total-lbl {
    font-size: 12px;
    font-weight: 700;
}
.nota-total-val {
    font-family: monospace;
    font-size: 15px;
    font-weight: 700;
    color: var(--gold-dk);
}
.nota-footer {
    font-size: 10px;
    color: var(--gray-400);
    text-align: center;
    padding-top: 8px;
    border-top: 1px dashed var(--gray-200);
}
.nota-gen-btn {
    margin: 0 14px 14px;
    width: calc(100% - 28px);
    padding: 11px;
    background: var(--black);
    border: 1px solid var(--gold-rim);
    border-radius: 8px;
    color: var(--gold-lt);
    font-weight: 700;
    cursor: pointer;
}
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(10,10,10,0.55);
    backdrop-filter: blur(6px);
    z-index: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
}
.overlay.show {
    opacity: 1;
    pointer-events: all;
}
.modal {
    background: white;
    border-radius: 14px;
    width: 450px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
}
.modal-hd {
    padding: 18px 20px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    justify-content: space-between;
    background: var(--gray-50);
}
.modal-ico {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--black);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Instrument Serif', serif;
    font-size: 17px;
    color: var(--gold-lt);
}
.modal-nota {
    margin: 16px;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    overflow: hidden;
}
.modal-nota-top {
    background: var(--black);
    padding: 16px 18px;
    display: flex;
    justify-content: space-between;
}
.modal-nota-brand {
    font-family: 'Instrument Serif', serif;
    font-style: italic;
    font-size: 22px;
    color: var(--gold-lt);
}
.nota-cust-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px dashed var(--gray-200);
}
.nota-field-k {
    font-size: 8.5px;
    text-transform: uppercase;
    color: var(--gray-400);
    font-weight: 700;
}
.nota-field-v {
    font-size: 12.5px;
    color: var(--black);
    font-weight: 500;
}
.nota-items-tbl {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
}
.nota-items-tbl th {
    font-size: 9px;
    text-transform: uppercase;
    color: var(--gray-400);
    padding: 5px 0;
    text-align: left;
}
.nota-items-tbl td {
    padding: 7px 0;
    font-size: 11.5px;
    border-bottom: 1px solid var(--gray-100);
}
.nota-totals {
    background: var(--gray-50);
    border: 1px solid var(--gold-md);
    border-radius: 8px;
    padding: 10px 12px;
}
.nota-tot-row {
    display: flex;
    justify-content: space-between;
    font-size: 11.5px;
    padding: 3px 0;
}
.nota-tot-row.grand {
    border-top: 1px solid var(--gray-200);
    padding-top: 8px;
    margin-top: 4px;
}
.modal-acts {
    padding: 0 16px 16px;
    display: flex;
    gap: 8px;
}
.btn-print, .btn-share {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}
.btn-print {
    background: var(--black);
    border: 1px solid var(--gold-rim);
    color: var(--gold-lt);
}
.btn-share {
    background: white;
    border: 1px solid var(--gray-200);
    color: var(--gray-600);
}
.btn-gold {
    background: var(--gold);
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}
.btn-white {
    background: white;
    border: 1px solid var(--gray-200);
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}
@media print {
    .sidebar, .topbar, .modal-acts, .nota-gen-btn, .btn-gold, .btn-white {
        display: none;
    }
    .overlay {
        position: relative;
        opacity: 1;
        background: white;
        backdrop-filter: none;
    }
    .modal {
        box-shadow: none;
        width: 100%;
    }
}
</style>
@endsection