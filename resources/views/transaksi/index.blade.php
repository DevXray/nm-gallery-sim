@extends('layouts.app')

@section('title', 'Transaksi Penyewaan')
@section('breadcrumb', 'Transaksi & E-Nota')

@section('content')
<div class="page active" id="page-transaction">

    <div class="pg-head">
        <div class="pg-title">Transaksi Penyewaan</div>
        <div class="pg-sub">Isi form di bawah untuk membuat transaksi sewa baru</div>
    </div>

    @if(session('success'))
    <div style="background:rgba(45,166,110,.1);border:1px solid rgba(45,166,110,.3);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#1a8050;font-weight:600;font-size:13px">
        ✅ {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div style="background:rgba(220,80,60,.08);border:1px solid rgba(220,80,60,.2);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#c0392b;font-size:13px">
        ⚠️ {{ $errors->first() }}
    </div>
    @endif

    <div class="trx-layout">
        <!-- LEFT: Form -->
        <div class="form-card">
            <div class="form-sect" style="background: var(--gold-xs);">
                <div class="form-sect-lbl">🎯 Pilih Barang</div>
                <div class="fgrid">
                    <div class="field f-full">
                        <label class="flbl">Pilih Barang *</label>
                        <select id="pilih_barang" class="fselect">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                @php
                                    $stokArray = is_string($barang->stok) ? json_decode($barang->stok, true) : [];
                                    $stokStr = is_array($stokArray) ? json_encode($stokArray) : '{}';
                                @endphp
                                <option value="{{ $barang->id_barang }}"
                                        data-nama="{{ $barang->nama_barang }}"
                                        data-harga="{{ $barang->harga_sewa }}"
                                        data-stok="{{ $stokStr }}"
                                        {{ (isset($selectedBarang) && $selectedBarang->id_barang == $barang->id_barang) ? 'selected' : '' }}>
                                    {{ $barang->nama_barang }} — Rp {{ number_format($barang->harga_sewa, 0, ',', '.') }}/hari
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-sect">
                <div class="form-sect-lbl">📦 Data Pelanggan</div>
                <div class="fgrid">
                    <div class="field">
                        <label class="flbl">Nama Pelanggan *</label>
                        <input type="text" id="nama_pelanggan" class="finput" placeholder="Nama lengkap"
                            value="{{ $selectedPelanggan->nama_pelanggan ?? '' }}">
                    </div>
                    <div class="field">
                        <label class="flbl">No. Telepon *</label>
                        <input type="text" id="no_telp" class="finput" placeholder="0812-xxxx-xxxx"
                            value="{{ $selectedPelanggan->no_telp ?? '' }}">
                    </div>
                    <div class="field f-full">
                        <label class="flbl">Alamat</label>
                        <input type="text" id="alamat" class="finput" placeholder="Alamat pelanggan"
                            value="{{ $selectedPelanggan->alamat ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="form-sect">
                <div class="form-sect-lbl">📅 Periode & Pembayaran</div>
                <div class="fgrid">
                    <div class="field">
                        <label class="flbl">Tanggal Mulai</label>
                        <input type="datetime-local" id="tgl_sewa" class="finput" value="{{ date('Y-m-d\TH:i') }}">
                    </div>
                    <div class="field">
                        <label class="flbl">Tanggal Kembali</label>
                        <input type="datetime-local" id="tgl_jatuh_tempo" class="finput">
                    </div>
                    <div class="field f-full">
                        <label class="flbl">Ukuran & Jumlah</label>
                        <div id="ukuran-container" style="padding:15px;background:#f5f5f5;border-radius:8px;text-align:center;color:#aaa;font-size:12px">
                            Pilih barang terlebih dahulu
                        </div>
                        <div id="stok-warning" style="display:none;margin-top:8px;padding:8px;background:#fee2e2;border-radius:8px;color:red;font-size:12px"></div>
                    </div>
                    <div class="field">
                        <label class="flbl">Metode Pembayaran</label>
                        <select id="metode_bayar" class="fselect" onchange="toggleDpInput()">
                            <option value="Lunas">💰 Bayar Penuh (Lunas)</option>
                            <option value="DP">📋 Bayar DP (Sebagian)</option>
                        </select>
                    </div>
                    <div class="field" id="dp_input_wrapper" style="display:none">
                        <label class="flbl">Jumlah DP (Rp)</label>
                        <input type="number" id="jumlah_dp" class="finput" placeholder="Masukkan jumlah DP" oninput="updatePreview()">
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="form-sect" style="background:#fafafa;">
                <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap">
                    <button type="button" class="btn-white" onclick="showDraftModal()" title="Lihat draft tersimpan">
                        📂 Lihat Draft
                    </button>
                    <button type="button" class="btn-outline" id="btnSimpanDraft" onclick="simpanDraft()" disabled>
                        💾 Simpan Draft
                    </button>
                    <button type="button" class="btn-gold" id="btnCetakNota" disabled onclick="prosesTransaksi()">
                        ✅ Simpan & Cetak Nota
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT: Preview E-Nota -->
        <div class="nota-panel">
            <div class="nota-preview-hd">
                <div class="nota-preview-title">Preview E-Nota</div>
                <span style="font-size:10px;color:#aaa">Auto-update</span>
            </div>
            <div class="nota-paper">
                <div class="nota-top">
                    <div class="nota-brand">NM Gallery</div>
                    <div class="nota-tagline">Baju Bodo Collection</div>
                    <div class="nota-trx-label">BUKTI PENYEWAAN</div>
                    <div class="nota-trx-num">#TRX-PREVIEW</div>
                </div>
                <div class="nota-body">
                    <div class="nota-row"><span class="nota-key">Pelanggan</span><span class="nota-val" id="previewPelanggan">—</span></div>
                    <div class="nota-row"><span class="nota-key">Barang</span><span class="nota-val" id="previewBarang">—</span></div>
                    <div class="nota-row"><span class="nota-key">Periode</span><span class="nota-val" id="previewPeriode">—</span></div>
                    <div class="nota-row"><span class="nota-key">Durasi</span><span class="nota-val" id="previewDurasi">—</span></div>
                    <div id="previewItems"></div>
                    <div class="nota-row"><span class="nota-key">Total Sewa</span><span class="nota-val gold" id="previewTotal">Rp 0</span></div>
                    <div class="nota-row" id="previewDpRow" style="display:none">
                        <span class="nota-key">DP Dibayar</span>
                        <span class="nota-val" id="previewDpVal" style="color:#1a8050">Rp 0</span>
                    </div>
                    <div class="nota-row" id="previewSisaRow" style="display:none">
                        <span class="nota-key">Sisa saat kembali</span>
                        <span class="nota-val" id="previewSisaVal" style="color:#c0392b">Rp 0</span>
                    </div>
                    <div class="nota-total-box">
                        <span class="nota-total-lbl" id="previewTotalLabel">Dibayar Sekarang</span>
                        <span class="nota-total-val" id="previewTotalBayar">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form tersembunyi untuk submit transaksi -->
<form id="transaksiForm" action="{{ route('transaksi.store') }}" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="nama_pelanggan" id="form_nama">
    <input type="hidden" name="no_telp" id="form_telp">
    <input type="hidden" name="alamat" id="form_alamat">
    <input type="hidden" name="id_barang" id="form_barang">
    <input type="hidden" name="items" id="form_items">
    <input type="hidden" name="tgl_sewa" id="form_tgl_sewa">
    <input type="hidden" name="tgl_jatuh_tempo" id="form_tgl_jatuh">
    <input type="hidden" name="metode_bayar" id="form_metode">
    <input type="hidden" name="jumlah_dp" id="form_dp">
    <input type="hidden" name="draft_id" id="form_draft_id">
</form>

<!-- MODAL: LIHAT DRAFT -->
<div class="modal-overlay" id="draftModal">
    <div class="modal-popup" style="max-width:600px;max-height:80vh">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">📂 Draft Tersimpan</div>
                <div class="modal-popup-sub">Pelanggan yang masih ragu-ragu — klik untuk muat ke form</div>
            </div>
            <button class="modal-popup-close" onclick="closeDraftModal()">✕</button>
        </div>
        <div class="modal-popup-body" id="draftListContent">
            <div style="text-align:center;padding:30px;color:#aaa">
                <div style="font-size:24px;margin-bottom:8px">📋</div>
                Memuat draft...
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeDraftModal()">Tutup</button>
        </div>
    </div>
</div>

<style>
.nota-val.gold { color: var(--gold-dk); font-weight: 700; font-family: monospace; }
.trx-layout { display: grid; grid-template-columns: 1fr 320px; gap: 18px; }
.form-card { background: white; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; }
.form-sect { padding: 20px 24px; border-bottom: 1px solid #eee; }
.form-sect:last-child { border-bottom: none; }
.form-sect-lbl { font-size: 11px; font-weight: 700; color: #888; text-transform: uppercase; margin-bottom: 14px; }
.fgrid { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; }
.f-full { grid-column: 1 / -1; }
.field { display: flex; flex-direction: column; gap: 5px; }
.flbl { font-size: 11px; font-weight: 600; color: #666; }
.nota-panel { background: white; border: 1px solid #ddd; border-radius: 12px; position: sticky; top: 0; }
.nota-preview-hd { padding: 13px 16px; background: #fafafa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
.nota-preview-title { font-size: 12px; font-weight: 700; }
.nota-paper { margin: 14px; border: 1px solid #eee; border-radius: 8px; overflow: hidden; }
.nota-top { background: #0a0a0a; padding: 14px 16px; }
.nota-brand { font-size: 20px; font-style: italic; color: #e0c06e; }
.nota-tagline { font-size: 8px; color: rgba(255,255,255,0.3); }
.nota-trx-label { font-size: 8px; color: rgba(255,255,255,0.3); margin-top: 8px; }
.nota-trx-num { font-family: monospace; font-size: 12px; color: #e0c06e; }
.nota-body { padding: 13px 14px; }
.nota-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 11px; border-bottom: 1px solid #f0f0f0; }
.nota-key { color: #888; }
.nota-val { color: #333; text-align: right; }
.nota-total-box { background: #fafafa; border: 1px solid #e0c06e; border-radius: 8px; padding: 10px; margin-top: 10px; display: flex; justify-content: space-between; }
.nota-total-lbl { font-size: 12px; font-weight: 700; }
.nota-total-val { font-family: monospace; font-size: 14px; font-weight: 700; color: #C9A84C; }
.nota-gen-btn { margin: 0 14px 14px; width: calc(100% - 28px); padding: 11px; background: #0a0a0a; border: 1px solid rgba(201,168,76,.4); border-radius: 8px; color: #e0c06e; font-size: 13px; font-weight: 700; cursor: pointer; }

/* Modal styles */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.25s ease; }
.modal-overlay.show { opacity: 1; visibility: visible; }
.modal-popup { background: white; border-radius: 20px; width: 90%; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.3); max-height: 90vh; display: flex; flex-direction: column; }
.modal-popup-header { padding: 20px 24px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: flex-start; }
.modal-popup-title { font-size: 18px; font-weight: 700; color: var(--black); }
.modal-popup-sub { font-size: 11px; color: var(--gold-dk); margin-top: 4px; }
.modal-popup-close { width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--gray-200); background: white; cursor: pointer; font-size: 14px; }
.modal-popup-body { padding: 20px; overflow-y: auto; flex: 1; }
.modal-popup-footer { padding: 14px 20px; border-top: 1px solid var(--gray-200); background: var(--gray-50); display: flex; gap: 10px; justify-content: flex-end; }
.modal-input { padding: 10px 12px; border: 1.5px solid var(--gray-200); border-radius: 10px; font-size: 13px; font-family: inherit; }
</style>

<script>
let selectedBarangId  = '';
let selectedBarangNama = '';
let selectedBarangHarga = 0;
let currentDraftId = null;

// ─────────────────────────────────────────────
// Pilih Barang
// ─────────────────────────────────────────────
document.getElementById('pilih_barang').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (!this.value) {
        selectedBarangId = '';
        document.getElementById('ukuran-container').innerHTML = '<div style="padding:15px;text-align:center;color:#aaa;font-size:12px">Pilih barang terlebih dahulu</div>';
        updatePreview();
        return;
    }
    selectedBarangId    = this.value;
    selectedBarangNama  = opt.dataset.nama;
    selectedBarangHarga = parseInt(opt.dataset.harga);

    let stok = {};
    try { stok = JSON.parse(opt.dataset.stok); } catch(e) { stok = {}; }

    if (Object.keys(stok).length === 0) {
        document.getElementById('ukuran-container').innerHTML =
            '<div style="padding:15px;text-align:center;color:#c0392b;font-size:12px">⚠️ Tidak ada stok tersedia</div>';
    } else {
        let html = '';
        for (const [size, qty] of Object.entries(stok)) {
            html += `<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;padding:8px;background:#f9f9f9;border-radius:6px;">
                        <label style="min-width:70px;font-size:12px;font-weight:600">Size ${size}</label>
                        <input type="number" class="jumlah-ukuran" data-size="${size}" data-stok="${qty}"
                               min="0" max="${qty}" value="0"
                               style="width:80px;padding:6px;border-radius:6px;border:1px solid #ddd;font-size:12px;">
                        <span style="font-size:11px;color:#888">Stok: ${qty}</span>
                     </div>`;
        }
        document.getElementById('ukuran-container').innerHTML = html;
    }

    document.querySelectorAll('.jumlah-ukuran').forEach(el => el.addEventListener('input', updatePreview));
    updatePreview();
});

// ─────────────────────────────────────────────
// Toggle input DP
// ─────────────────────────────────────────────
function toggleDpInput() {
    const isDP = document.getElementById('metode_bayar').value === 'DP';
    document.getElementById('dp_input_wrapper').style.display = isDP ? '' : 'none';
    updatePreview();
}

// ─────────────────────────────────────────────
// Hitung items yang dipilih
// ─────────────────────────────────────────────
function getSelectedItems() {
    let items = [], hasError = false;
    document.querySelectorAll('.jumlah-ukuran').forEach(el => {
        const jumlah = parseInt(el.value) || 0;
        const stok   = parseInt(el.dataset.stok);
        if (jumlah > 0) {
            if (jumlah > stok) { hasError = true; el.style.borderColor = 'red'; }
            else { el.style.borderColor = '#ddd'; items.push({ size: el.dataset.size, jumlah, harga: selectedBarangHarga }); }
        }
    });
    return { items, hasError };
}

// ─────────────────────────────────────────────
// Update preview nota realtime
// ─────────────────────────────────────────────
function updatePreview() {
    const start = document.getElementById('tgl_sewa').value;
    const end   = document.getElementById('tgl_jatuh_tempo').value;
    const hari  = (start && end) ? Math.max(1, Math.ceil((new Date(end) - new Date(start)) / 86400000)) : 0;

    const { items, hasError } = getSelectedItems();
    const metode = document.getElementById('metode_bayar').value;
    let total = 0, html = '';

    items.forEach(it => {
        const sub = it.harga * it.jumlah * hari;
        total += sub;
        html += `<div class="nota-row"><span class="nota-key">Size ${it.size} ×${it.jumlah}</span><span class="nota-val">Rp ${sub.toLocaleString('id-ID')}</span></div>`;
    });

    document.getElementById('previewPelanggan').textContent = document.getElementById('nama_pelanggan').value || '—';
    document.getElementById('previewBarang').textContent    = selectedBarangNama || '—';
    document.getElementById('previewPeriode').textContent   = start && end ? `${start.substring(0,10)} s/d ${end.substring(0,10)}` : '—';
    document.getElementById('previewDurasi').textContent    = hari ? `${hari} hari` : '—';
    document.getElementById('previewItems').innerHTML        = html;
    document.getElementById('previewTotal').textContent     = `Rp ${total.toLocaleString('id-ID')}`;

    // DP / Lunas logic
    const dpInput     = parseInt(document.getElementById('jumlah_dp').value) || Math.round(total * 0.5);
    const sisa        = total - dpInput;

    if (metode === 'DP' && total > 0) {
        document.getElementById('previewDpRow').style.display   = '';
        document.getElementById('previewSisaRow').style.display  = '';
        document.getElementById('previewDpVal').textContent     = `Rp ${dpInput.toLocaleString('id-ID')}`;
        document.getElementById('previewSisaVal').textContent   = `Rp ${Math.max(0, sisa).toLocaleString('id-ID')}`;
        document.getElementById('previewTotalLabel').textContent = 'DP Dibayar Sekarang';
        document.getElementById('previewTotalBayar').textContent = `Rp ${dpInput.toLocaleString('id-ID')}`;
    } else {
        document.getElementById('previewDpRow').style.display   = 'none';
        document.getElementById('previewSisaRow').style.display  = 'none';
        document.getElementById('previewTotalLabel').textContent = 'Dibayar Sekarang';
        document.getElementById('previewTotalBayar').textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }

    // Enable/disable tombol
    const valid = !hasError && items.length > 0 && hari > 0 && selectedBarangId;
    document.getElementById('btnCetakNota').disabled  = !valid;
    document.getElementById('btnSimpanDraft').disabled = !selectedBarangId || !document.getElementById('nama_pelanggan').value;

    const warn = document.getElementById('stok-warning');
    if (hasError) { warn.style.display = ''; warn.textContent = '⚠️ Stok tidak mencukupi!'; }
    else if (!end) { warn.style.display = ''; warn.textContent = '⚠️ Pilih tanggal kembali!'; }
    else { warn.style.display = 'none'; }
}

// Pasang listener realtime
['tgl_sewa','tgl_jatuh_tempo','nama_pelanggan','no_telp','jumlah_dp'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', updatePreview);
    if (el) el.addEventListener('change', updatePreview);
});

// ─────────────────────────────────────────────
// Proses Transaksi (Simpan + Cetak)
// ─────────────────────────────────────────────
function prosesTransaksi() {
    const { items, hasError } = getSelectedItems();
    if (hasError || items.length === 0) { alert('Periksa kembali pilihan ukuran dan jumlah!'); return; }
    if (!document.getElementById('nama_pelanggan').value) { alert('Nama pelanggan harus diisi!'); return; }
    if (!document.getElementById('no_telp').value) { alert('No. telepon harus diisi!'); return; }

    const metode = document.getElementById('metode_bayar').value;
    const total  = items.reduce((s, it) => {
        const h = Math.max(1, Math.ceil((new Date(document.getElementById('tgl_jatuh_tempo').value) - new Date(document.getElementById('tgl_sewa').value)) / 86400000));
        return s + it.harga * it.jumlah * h;
    }, 0);
    const dp = metode === 'DP' ? (parseInt(document.getElementById('jumlah_dp').value) || Math.round(total * 0.5)) : total;

    document.getElementById('form_nama').value    = document.getElementById('nama_pelanggan').value;
    document.getElementById('form_telp').value    = document.getElementById('no_telp').value;
    document.getElementById('form_alamat').value  = document.getElementById('alamat').value;
    document.getElementById('form_barang').value  = selectedBarangId;
    document.getElementById('form_items').value   = JSON.stringify(items);
    document.getElementById('form_tgl_sewa').value = document.getElementById('tgl_sewa').value;
    document.getElementById('form_tgl_jatuh').value = document.getElementById('tgl_jatuh_tempo').value;
    document.getElementById('form_metode').value  = metode;
    document.getElementById('form_dp').value      = dp;
    document.getElementById('form_draft_id').value = currentDraftId ?? '';

    document.getElementById('transaksiForm').submit();
}

// ─────────────────────────────────────────────
// Simpan Draft
// ─────────────────────────────────────────────
function simpanDraft() {
    const { items } = getSelectedItems();
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('nama_pelanggan', document.getElementById('nama_pelanggan').value || 'Draft');
    formData.append('no_telp', document.getElementById('no_telp').value || '-');
    formData.append('alamat', document.getElementById('alamat').value);
    formData.append('id_barang', selectedBarangId);
    formData.append('items', JSON.stringify(items));
    formData.append('tgl_sewa', document.getElementById('tgl_sewa').value);
    formData.append('tgl_jatuh', document.getElementById('tgl_jatuh_tempo').value);
    formData.append('metode_bayar', document.getElementById('metode_bayar').value);
    formData.append('jumlah_dp', document.getElementById('jumlah_dp').value || 0);

    const btn = document.getElementById('btnSimpanDraft');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    fetch('{{ route("draft.save") }}', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                btn.textContent = '✅ Draft Tersimpan!';
                btn.style.borderColor = 'rgba(45,166,110,.4)';
                btn.style.color = '#1a8050';
                setTimeout(() => { btn.textContent = '💾 Simpan Draft'; btn.disabled = false; btn.style.borderColor = ''; btn.style.color = ''; }, 3000);
            } else { alert('Gagal menyimpan draft: ' + d.message); btn.textContent = '💾 Simpan Draft'; btn.disabled = false; }
        })
        .catch(() => { alert('Terjadi kesalahan.'); btn.textContent = '💾 Simpan Draft'; btn.disabled = false; });
}

// ─────────────────────────────────────────────
// Modal Lihat Draft
// ─────────────────────────────────────────────
function showDraftModal() {
    document.getElementById('draftModal').classList.add('show');
    document.getElementById('draftListContent').innerHTML = '<div style="text-align:center;padding:30px;color:#aaa"><div style="font-size:24px;margin-bottom:8px">⏳</div>Memuat draft...</div>';

    fetch('{{ route("draft.list") }}')
        .then(r => r.json())
        .then(d => {
            if (!d.success || d.drafts.length === 0) {
                document.getElementById('draftListContent').innerHTML = '<div style="text-align:center;padding:40px;color:#aaa"><div style="font-size:32px;margin-bottom:12px">📋</div>Belum ada draft tersimpan</div>';
                return;
            }
            let html = '<div style="display:flex;flex-direction:column;gap:10px;">';
            d.drafts.forEach(dr => {
                html += `<div style="border:1.5px solid #e0e0e0;border-radius:12px;padding:14px;cursor:pointer;transition:all .15s;background:white" 
                              onmouseover="this.style.borderColor='#C9A84C'" onmouseout="this.style.borderColor='#e0e0e0'">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px">
                        <div onclick="loadDraft(${JSON.stringify(dr).replace(/"/g,'&quot;')})" style="flex:1">
                            <div style="font-size:13px;font-weight:700;color:#0a0a0a">${dr.nama_pelanggan}</div>
                            <div style="font-size:11px;color:#888;margin-top:2px">${dr.no_telp} · ${dr.barang}</div>
                            <div style="font-size:11px;color:#C9A84C;margin-top:4px;font-weight:600">
                                Rp ${parseInt(dr.total_biaya).toLocaleString('id-ID')} · ${dr.metode_bayar}
                            </div>
                            <div style="font-size:10px;color:#aaa;margin-top:2px">
                                Sewa: ${dr.tgl_sewa} s/d ${dr.tgl_jatuh} · Dibuat: ${dr.created_at}
                            </div>
                        </div>
                        <div style="display:flex;gap:6px">
                            <button onclick="loadDraft(${JSON.stringify(dr).replace(/"/g,'&quot;')})" 
                                    style="padding:5px 12px;background:#C9A84C;color:white;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer">
                                Muat
                            </button>
                            <button onclick="hapusDraft(${dr.id_draft}, this)" 
                                    style="padding:5px 10px;background:white;color:#c0392b;border:1px solid rgba(220,80,60,.3);border-radius:6px;font-size:11px;cursor:pointer">
                                🗑
                            </button>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            document.getElementById('draftListContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('draftListContent').innerHTML = '<div style="text-align:center;padding:30px;color:#c0392b">⚠️ Gagal memuat draft</div>';
        });
}

function closeDraftModal() {
    document.getElementById('draftModal').classList.remove('show');
}

function loadDraft(dr) {
    currentDraftId = dr.id_draft;

    // Set data pelanggan
    document.getElementById('nama_pelanggan').value = dr.nama_pelanggan;
    document.getElementById('no_telp').value        = dr.no_telp;
    document.getElementById('alamat').value         = dr.alamat || '';
    document.getElementById('metode_bayar').value   = dr.metode_bayar;

    // Set tanggal
    if (dr.tgl_sewa && dr.tgl_sewa !== '-') {
        const [d,m,y] = dr.tgl_sewa.split('/');
        document.getElementById('tgl_sewa').value = `${y}-${m}-${d}T00:00`;
    }
    if (dr.tgl_jatuh && dr.tgl_jatuh !== '-') {
        const [d,m,y] = dr.tgl_jatuh.split('/');
        document.getElementById('tgl_jatuh_tempo').value = `${y}-${m}-${d}T00:00`;
    }

    // Trigger pilih barang
    const barangSelect = document.getElementById('pilih_barang');
    barangSelect.value = dr.id_barang;
    barangSelect.dispatchEvent(new Event('change'));

    toggleDpInput();
    closeDraftModal();

    // Notif
    const notif = document.createElement('div');
    notif.style.cssText = 'position:fixed;top:20px;right:20px;background:#1a8050;color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 4px 15px rgba(0,0,0,.2)';
    notif.textContent = '✅ Draft berhasil dimuat ke form!';
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 3000);
}

function hapusDraft(id, btn) {
    if (!confirm('Hapus draft ini?')) return;
    const row = btn.closest('[style]');

    fetch(`/draft/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
    });
}

// Default tanggal kembali +3 hari
const def = new Date();
def.setDate(def.getDate() + 3);
document.getElementById('tgl_jatuh_tempo').value = def.toISOString().slice(0, 16);

// Inisialisasi barang terpilih saat halaman pertama kali dibuka.
// Ini penting untuk kasus barang sudah ter-select dari query param (?barang=...)
// agar ukuran, preview, dan status tombol langsung sinkron.
const initialBarangSelect = document.getElementById('pilih_barang');
if (initialBarangSelect && initialBarangSelect.value) {
    initialBarangSelect.dispatchEvent(new Event('change'));
} else {
    updatePreview();
}
</script>
@endsection