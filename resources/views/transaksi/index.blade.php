@extends('layouts.app')

@section('title', 'Transaksi Penyewaan')
@section('breadcrumb', 'Transaksi & E-Nota')

@section('content')
<div class="pg-head">
    <div class="pg-title">Transaksi Penyewaan</div>
    <div class="pg-sub">Isi form di bawah untuk membuat transaksi sewa baru</div>
</div>

<div class="trx-layout">

    <!-- LEFT: Form -->
    <div class="form-card">

        <!-- Step 1: Pilih Baju Bodo -->
        <div class="form-sect">
            <div class="form-sect-lbl">1 · Pilih Baju Bodo</div>
            <div class="baju-selector" id="bajuSelector">
                @foreach($barangs as $item)
                <div class="bs-card" onclick="pilihBaju(this, {{ $item->id_barang }}, '{{ $item->nama_barang }}', {{ $item->harga_sewa }})">
                    <div class="bs-check"></div>
                    <div class="baju-photo" style="width:36px;height:42px;font-size:18px">👘</div>
                    <div>
                        <div class="bs-name">{{ $item->nama_barang }}</div>
                        <div class="bs-color-line">
                            <div class="color-dot" style="background:#2d6e3e;width:9px;height:9px"></div>
                            Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}/hari
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Step 2: Data Pelanggan -->
        <div class="form-sect">
            <div class="form-sect-lbl">2 · Data Pelanggan</div>
            <div class="fgrid">
                <div class="field f-full">
                    <label class="flbl">Nama Pelanggan *</label>
                    <select id="id_pelanggan" class="fselect" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($pelanggans as $item)
                        <option value="{{ $item->id_pelanggan }}" data-telp="{{ $item->no_telp }}" data-alamat="{{ $item->alamat }}">{{ $item->nama_pelanggan }} - {{ $item->no_telp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="flbl">No. Telepon</label>
                    <input type="text" id="no_telp" class="finput" readonly placeholder="Akan terisi otomatis">
                </div>
                <div class="field f-full">
                    <label class="flbl">Alamat / Catatan</label>
                    <input type="text" id="alamat" class="finput" readonly placeholder="Akan terisi otomatis">
                </div>
            </div>
        </div>

        <!-- Step 3: Periode & Opsi -->
        <div class="form-sect">
            <div class="form-sect-lbl">3 · Periode &amp; Opsi</div>
            <div class="fgrid">
                <div class="field">
                    <label class="flbl">Tanggal Mulai Sewa</label>
                    <input type="date" id="tgl_sewa" class="finput" value="{{ date('Y-m-d') }}">
                </div>
                <div class="field">
                    <label class="flbl">Tanggal Kembali *</label>
                    <input type="date" id="tgl_jatuh_tempo" class="finput">
                </div>
                <div class="field">
                    <label class="flbl">Ukuran</label>
                    <select id="ukuran" class="fselect">
                        <option>Pilih ukuran…</option>
                        <option>Size S</option>
                        <option>Size M</option>
                        <option>Size L</option>
                        <option>Size XL</option>
                    </select>
                </div>
                <div class="field">
                    <label class="flbl">Jenis Pembayaran</label>
                    <select id="metode_bayar" class="fselect">
                        <option value="DP 50%">DP 50% Dulu</option>
                        <option value="Bayar Penuh">Bayar Penuh</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;background:var(--gray-50)">
            <button type="button" class="btn-white" onclick="simpanDraft()">Simpan Draft</button>
            <button type="button" class="btn-gold" onclick="showModal()">Generate E-Nota &amp; Proses →</button>
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

<!-- Modal E-Nota -->
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
                    <div><div class="nota-field-k">Ukuran</div><div class="nota-field-v" id="modalUkuran">—</div></div>
                </div>

                <table class="nota-items-tbl">
                    <thead>
                        <tr><th>Item</th><th>Harga/hr</th><th>Hari</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody id="modalItems">
                        <tr><td colspan="4">—</td></tr>
                    </tbody>
                </table>

                <div class="nota-totals">
                    <div class="nota-tot-row grand">
                        <span class="nota-tot-lbl">TOTAL DIBAYAR</span>
                        <span class="nota-tot-val" id="modalTotal">Rp 0</span>
                    </div>
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
    <input type="hidden" name="id_pelanggan" id="form_id_pelanggan">
    <input type="hidden" name="id_barang" id="form_id_barang">
    <input type="hidden" name="tgl_sewa" id="form_tgl_sewa">
    <input type="hidden" name="tgl_jatuh_tempo" id="form_tgl_jatuh_tempo">
    <input type="hidden" name="ukuran" id="form_ukuran">
    <input type="hidden" name="metode_bayar" id="form_metode_bayar">
</form>

<script>
    let selectedBarangId = null, selectedBarangNama = null, selectedBarangHarga = 0;
    let selectedPelangganId = null, selectedPelangganNama = '', selectedPelangganTelp = '', selectedPelangganAlamat = '';

    const pelangganData = @json($pelanggans);

    function pilihBaju(element, id, nama, harga) {
        document.querySelectorAll('.bs-card').forEach(card => {
            card.classList.remove('sel');
            card.querySelector('.bs-check').textContent = '';
        });
        element.classList.add('sel');
        element.querySelector('.bs-check').textContent = '✓';
        
        selectedBarangId = id;
        selectedBarangNama = nama;
        selectedBarangHarga = harga;
        updatePreview();
    }

    document.getElementById('id_pelanggan').addEventListener('change', function() {
        let selected = pelangganData.find(p => p.id_pelanggan == this.value);
        if (selected) {
            selectedPelangganId = selected.id_pelanggan;
            selectedPelangganNama = selected.nama_pelanggan;
            selectedPelangganTelp = selected.no_telp;
            selectedPelangganAlamat = selected.alamat || '';
            document.getElementById('no_telp').value = selectedPelangganTelp;
            document.getElementById('alamat').value = selectedPelangganAlamat;
        } else {
            selectedPelangganNama = '';
            selectedPelangganTelp = '';
            selectedPelangganAlamat = '';
            document.getElementById('no_telp').value = '';
            document.getElementById('alamat').value = '';
        }
        updatePreview();
    });

    function hitungHari() {
        let tglSewa = document.getElementById('tgl_sewa').value;
        let tglKembali = document.getElementById('tgl_jatuh_tempo').value;
        if (tglSewa && tglKembali) {
            let start = new Date(tglSewa);
            let end = new Date(tglKembali);
            let diffTime = Math.abs(end - start);
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
        }
        return 0;
    }

    function updatePreview() {
        let tglSewa = document.getElementById('tgl_sewa').value;
        let tglKembali = document.getElementById('tgl_jatuh_tempo').value;
        let jumlahHari = hitungHari();
        let total = selectedBarangHarga * jumlahHari;

        document.getElementById('previewPelanggan').innerText = selectedPelangganNama || '—';
        document.getElementById('previewBarang').innerText = selectedBarangNama || '—';
        document.getElementById('previewPeriode').innerText = (tglSewa && tglKembali) ? tglSewa + ' s/d ' + tglKembali : '—';
        document.getElementById('previewDurasi').innerText = jumlahHari ? jumlahHari + ' hari' : '—';
        document.getElementById('previewHarga').innerText = selectedBarangHarga ? 'Rp ' + selectedBarangHarga.toLocaleString('id-ID') : '—';
        document.getElementById('previewTotal').innerText = total ? 'Rp ' + total.toLocaleString('id-ID') : 'Rp 0';
    }

    document.getElementById('tgl_sewa').addEventListener('change', updatePreview);
    document.getElementById('tgl_jatuh_tempo').addEventListener('change', updatePreview);

    function showModal() {
        if (!selectedBarangId) { alert('Pilih baju dulu!'); return; }
        if (!selectedPelangganId) { alert('Pilih pelanggan dulu!'); return; }

        let tglSewa = document.getElementById('tgl_sewa').value;
        let tglKembali = document.getElementById('tgl_jatuh_tempo').value;
        let jumlahHari = hitungHari();
        let total = selectedBarangHarga * jumlahHari;
        let metode = document.getElementById('metode_bayar').value;
        let ukuran = document.getElementById('ukuran').value;
        let kodeNota = '#TRX-' + Math.floor(Math.random() * 10000);

        document.getElementById('previewNotaNum').innerText = kodeNota;
        document.getElementById('modalNotaNum').innerText = kodeNota;
        document.getElementById('modalNama').innerText = selectedPelangganNama;
        document.getElementById('modalTelp').innerText = selectedPelangganTelp;
        document.getElementById('modalPeriode').innerText = (tglSewa && tglKembali) ? tglSewa + ' – ' + tglKembali : '—';
        document.getElementById('modalUkuran').innerText = ukuran;

        document.getElementById('modalItems').innerHTML = `
            <tr>
                <td>${selectedBarangNama}</td>
                <td style="color:var(--gray-400);font-family:monospace">${selectedBarangHarga.toLocaleString('id-ID')}</td>
                <td style="text-align:center;color:var(--gray-400)">×${jumlahHari}</td>
                <td style="color:var(--gold-dk)">Rp ${total.toLocaleString('id-ID')}</td>
            </tr>
            <tr>
                <td>Jaminan Kerusakan</td>
                <td style="color:var(--gray-400)">—</td>
                <td style="text-align:center">—</td>
                <td>Rp 200.000</td>
            </tr>
        `;

        let finalTotal = (metode === 'DP 50%') ? total / 2 : total;
        document.getElementById('modalTotal').innerHTML = (metode === 'DP 50%') 
            ? `Rp ${(total/2).toLocaleString('id-ID')} <span style="font-size:10px; color:gray;">(DP 50%)</span>`
            : `Rp ${total.toLocaleString('id-ID')}`;

        document.getElementById('form_id_pelanggan').value = selectedPelangganId;
        document.getElementById('form_id_barang').value = selectedBarangId;
        document.getElementById('form_tgl_sewa').value = tglSewa;
        document.getElementById('form_tgl_jatuh_tempo').value = tglKembali;
        document.getElementById('form_ukuran').value = ukuran;
        document.getElementById('form_metode_bayar').value = metode;

        document.getElementById('notaOverlay').classList.add('show');
    }

    function simpanTransaksi() {
        document.getElementById('transaksiForm').submit();
    }

    function simpanDraft() {
        alert('Draft disimpan! Lanjutkan ke halaman draft.');
        window.location.href = "{{ route('transaksi.show', ['transaksi' => 0]) }}";
    }

    function printNota() {
        window.print();
    }

    function closeModal(e) {
        if (!e || e.target === document.getElementById('notaOverlay')) {
            document.getElementById('notaOverlay').classList.remove('show');
        }
    }

    updatePreview();
</script>

<style>
.trx-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 18px;
}
.baju-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 9px;
}
.bs-card {
    border: 1.5px solid var(--gray-200);
    border-radius: 8px;
    padding: 11px 13px;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: 10px;
}
.bs-card:hover { border-color: var(--gold-rim); background: var(--gold-xs); }
.bs-card.sel { border-color: var(--gold); background: var(--gold-xs); }
.bs-check {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 1.5px solid var(--gray-300);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
}
.bs-card.sel .bs-check { background: var(--gold); border-color: var(--gold); color: var(--black); }
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
.nota-preview-title { font-size: 12.5px; font-weight: 700; }
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
.nota-tagline { font-size: 8.5px; color: rgba(255,255,255,0.25); text-transform: uppercase; }
.nota-trx-label { font-size: 8.5px; color: rgba(255,255,255,0.3); margin-top: 8px; text-transform: uppercase; }
.nota-trx-num { font-family: monospace; font-size: 12px; color: var(--gold-lt); }
.nota-body { padding: 13px 14px; }
.nota-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 11.5px;
    border-bottom: 1px solid var(--gray-100);
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
.nota-total-lbl { font-size: 12px; font-weight: 700; }
.nota-total-val { font-family: monospace; font-size: 15px; font-weight: 700; color: var(--gold-dk); }
.nota-footer { font-size: 10px; color: var(--gray-400); text-align: center; padding-top: 8px; border-top: 1px dashed var(--gray-200); }
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
    transition: opacity .2s;
}
.overlay.show { opacity: 1; pointer-events: all; }
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
.nota-field-k { font-size: 8.5px; text-transform: uppercase; color: var(--gray-400); font-weight: 700; }
.nota-field-v { font-size: 12.5px; color: var(--black); font-weight: 500; }
.nota-items-tbl { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
.nota-items-tbl th { font-size: 9px; text-transform: uppercase; color: var(--gray-400); padding: 5px 0; text-align: left; }
.nota-items-tbl td { padding: 7px 0; font-size: 11.5px; border-bottom: 1px solid var(--gray-100); }
.nota-totals { background: var(--gray-50); border: 1px solid var(--gold-md); border-radius: 8px; padding: 10px 12px; }
.nota-tot-row { display: flex; justify-content: space-between; padding: 3px 0; }
.nota-tot-row.grand { border-top: 1px solid var(--gray-200); padding-top: 8px; margin-top: 4px; }
.modal-acts { padding: 0 16px 16px; display: flex; gap: 8px; }
.btn-print, .btn-share {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
}
.btn-print { background: var(--black); border: 1px solid var(--gold-rim); color: var(--gold-lt); }
.btn-share { background: white; border: 1px solid var(--gray-200); color: var(--gray-600); }
@media print {
    .sidebar, .topbar, .modal-acts, .nota-gen-btn, .btn-gold, .btn-white { display: none; }
    .overlay { position: relative; opacity: 1; background: white; backdrop-filter: none; }
    .modal { box-shadow: none; width: 100%; }
}
</style>
@endsection