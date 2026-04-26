@extends('layouts.app')

@section('title', 'Transaksi Penyewaan')
@section('breadcrumb', 'Transaksi & E-Nota')

@section('content')
<div class="page active" id="page-transaction">

    <div class="pg-head">
        <div class="pg-title">Transaksi Penyewaan</div>
        <div class="pg-sub">Isi form di bawah untuk membuat transaksi sewa baru</div>
    </div>

    <div class="trx-layout">
        <!-- LEFT: Form -->
        <div class="form-card">
            <div class="form-sect" style="background: var(--gold-xs);">
                <div class="form-sect-lbl">🎯 Pilih Barang</div>
                <div class="fgrid">
                    <div class="field f-full">
                        <label class="flbl">Pilih Barang *</label>
                        <select id="pilih_barang" class="finput">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id_barang }}" 
                                        data-nama="{{ $barang->nama_barang }}"
                                        data-harga="{{ $barang->harga_sewa }}"
                                        data-stok="{{ $barang->stok }}">
                                    {{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_sewa, 0, ',', '.') }}/hari
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
                        <input type="text" id="nama_pelanggan" class="finput" placeholder="Nama lengkap">
                    </div>
                    <div class="field">
                        <label class="flbl">No. Telepon *</label>
                        <input type="text" id="no_telp" class="finput" placeholder="0812-xxxx-xxxx">
                    </div>
                    <div class="field f-full">
                        <label class="flbl">Alamat</label>
                        <input type="text" id="alamat" class="finput" placeholder="Alamat pelanggan">
                    </div>
                </div>
            </div>

            <div class="form-sect">
                <div class="form-sect-lbl">📅 Periode Sewa</div>
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
                        <label class="flbl">Pilih Ukuran & Jumlah</label>
                        <div id="ukuran-container" style="padding: 15px; background: #f5f5f5; border-radius: 8px; text-align: center;">Pilih barang terlebih dahulu</div>
                        <div id="stok-warning" style="display:none; margin-top: 8px; padding: 8px; background: #fee2e2; border-radius: 8px; color: red;"></div>
                    </div>
                    <div class="field">
                        <label class="flbl">Metode Bayar</label>
                        <select id="metode_bayar" class="fselect">
                            <option value="Bayar Penuh">Bayar Penuh</option>
                            <option value="DP 50%">DP 50%</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-sect" style="text-align: right; background: #fafafa;">
                <button type="button" class="btn-gold" id="btnCetakNota" disabled>📄 Cetak Nota</button>
            </div>
        </div>

        <!-- RIGHT: Preview -->
        <div class="nota-panel">
            <div class="nota-preview-hd">
                <div class="nota-preview-title">Preview E-Nota</div>
                <span style="font-size:10px;color:#aaa">Auto-generate</span>
            </div>
            <div class="nota-paper">
                <div class="nota-top">
                    <div class="nota-brand">NM Gallery</div>
                    <div class="nota-tagline">Baju Bodo Collection</div>
                    <div class="nota-trx-label">Nomor Transaksi</div>
                    <div class="nota-trx-num" id="previewNotaNum">#TRX-PREVIEW</div>
                </div>
                <div class="nota-body">
                    <div class="nota-row"><span class="nota-key">Pelanggan</span><span class="nota-val" id="previewPelanggan">—</span></div>
                    <div class="nota-row"><span class="nota-key">Barang</span><span class="nota-val" id="previewBarang">—</span></div>
                    <div class="nota-row"><span class="nota-key">Periode</span><span class="nota-val" id="previewPeriode">—</span></div>
                    <div class="nota-row"><span class="nota-key">Durasi</span><span class="nota-val" id="previewDurasi">—</span></div>
                    <div class="nota-row"><span class="nota-key">Harga/hari</span><span class="nota-val" id="previewHarga">—</span></div>
                    <div id="previewItems"></div>
                    <div class="nota-total-box">
                        <span class="nota-total-lbl">Total</span>
                        <span class="nota-total-val" id="previewTotal">Rp 0</span>
                    </div>
                </div>
            </div>
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
    let selectedBarangId = '';
    let selectedBarangNama = '';
    let selectedBarangHarga = 0;

    document.getElementById('pilih_barang').addEventListener('change', function() {
        let opt = this.options[this.selectedIndex];
        if (this.value) {
            selectedBarangId = this.value;
            selectedBarangNama = opt.getAttribute('data-nama');
            selectedBarangHarga = parseInt(opt.getAttribute('data-harga'));
            
            let stok = {};
            try { stok = JSON.parse(opt.getAttribute('data-stok')); } catch(e) { stok = {}; }
            
            let html = '';
            for (let size in stok) {
                html += `<div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                            <label style="width:60px;">Size ${size}</label>
                            <input type="number" class="jumlah-ukuran" data-size="${size}" data-stok="${stok[size]}" min="0" max="${stok[size]}" value="0" style="width:80px; padding:6px; border-radius:6px; border:1px solid #ddd;">
                            <span style="font-size:11px;">Stok: ${stok[size]}</span>
                        </div>`;
            }
            document.getElementById('ukuran-container').innerHTML = html || '<div style="color:red;">Tidak ada ukuran</div>';
            
            document.querySelectorAll('.jumlah-ukuran').forEach(input => {
                input.addEventListener('input', updatePreview);
            });
            updatePreview();
        } else {
            selectedBarangId = '';
            document.getElementById('ukuran-container').innerHTML = '<div style="padding:15px; text-align:center;">Pilih barang terlebih dahulu</div>';
            updatePreview();
        }
    });

    function getSelectedItems() {
        let items = [];
        let hasError = false;
        document.querySelectorAll('.jumlah-ukuran').forEach(input => {
            let jumlah = parseInt(input.value) || 0;
            let size = input.getAttribute('data-size');
            let stok = parseInt(input.getAttribute('data-stok'));
            if (jumlah > 0) {
                if (jumlah > stok) {
                    hasError = true;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '#ddd';
                    items.push({ size, jumlah, harga: selectedBarangHarga });
                }
            }
        });
        return { items, hasError };
    }

    function hitungHari() {
        let start = document.getElementById('tgl_sewa').value;
        let end = document.getElementById('tgl_jatuh_tempo').value;
        if (start && end) {
            let diff = Math.ceil((new Date(end) - new Date(start)) / (1000 * 60 * 60 * 24));
            return diff === 0 ? 1 : diff;
        }
        return 0;
    }

    function updatePreview() {
        let hari = hitungHari();
        let { items, hasError } = getSelectedItems();
        let total = 0;
        let itemsHtml = '';
        
        items.forEach(item => {
            let subtotal = item.harga * item.jumlah * hari;
            total += subtotal;
            itemsHtml += `<div class="nota-row"><span class="nota-key">Size ${item.size} x ${item.jumlah}</span><span class="nota-val">Rp ${subtotal.toLocaleString('id-ID')}</span></div>`;
        });
        
        document.getElementById('previewPelanggan').innerText = document.getElementById('nama_pelanggan').value || '—';
        document.getElementById('previewBarang').innerText = selectedBarangNama || '—';
        document.getElementById('previewPeriode').innerText = `${document.getElementById('tgl_sewa').value} s/d ${document.getElementById('tgl_jatuh_tempo').value}`;
        document.getElementById('previewDurasi').innerText = hari ? hari + ' hari' : '—';
        document.getElementById('previewHarga').innerText = selectedBarangHarga ? 'Rp ' + selectedBarangHarga.toLocaleString('id-ID') + '/hari' : '—';
        document.getElementById('previewItems').innerHTML = itemsHtml;
        document.getElementById('previewTotal').innerHTML = total ? 'Rp ' + total.toLocaleString('id-ID') : 'Rp 0';
        
        let btn = document.getElementById('btnCetakNota');
        let warning = document.getElementById('stok-warning');
        
        if (!selectedBarangId) {
            btn.disabled = true;
            warning.style.display = 'none';
        } else if (hasError) {
            btn.disabled = true;
            warning.style.display = 'block';
            warning.innerHTML = '⚠️ Stok tidak mencukupi!';
        } else if (items.length === 0) {
            btn.disabled = true;
            warning.style.display = 'block';
            warning.innerHTML = '⚠️ Pilih minimal 1 ukuran!';
        } else if (!document.getElementById('tgl_jatuh_tempo').value) {
            btn.disabled = true;
            warning.style.display = 'block';
            warning.innerHTML = '⚠️ Pilih tanggal kembali!';
        } else {
            btn.disabled = false;
            warning.style.display = 'none';
        }
    }

    document.getElementById('tgl_sewa').addEventListener('change', updatePreview);
    document.getElementById('tgl_jatuh_tempo').addEventListener('change', updatePreview);
    document.getElementById('nama_pelanggan').addEventListener('input', updatePreview);
    document.getElementById('no_telp').addEventListener('input', updatePreview);

    // CETAK PDF
    document.getElementById('btnCetakNota').addEventListener('click', function() {
        let pelanggan = {
            nama: document.getElementById('nama_pelanggan').value,
            telp: document.getElementById('no_telp').value,
            alamat: document.getElementById('alamat').value
        };
        
        let { items } = getSelectedItems();
        if (!pelanggan.nama) { alert('Isi nama pelanggan!'); return; }
        if (!pelanggan.telp) { alert('Isi no telepon!'); return; }
        if (items.length === 0) { alert('Pilih ukuran dan jumlah!'); return; }
        
        let formData = new FormData();
        formData.append('nama_pelanggan', pelanggan.nama);
        formData.append('no_telp', pelanggan.telp);
        formData.append('alamat', pelanggan.alamat);
        formData.append('id_barang', selectedBarangId);
        formData.append('items', JSON.stringify(items));
        formData.append('tgl_sewa', document.getElementById('tgl_sewa').value);
        formData.append('tgl_jatuh_tempo', document.getElementById('tgl_jatuh_tempo').value);
        formData.append('metode_bayar', document.getElementById('metode_bayar').value);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("transaksi.preview-pdf") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.blob())
        .then(blob => {
            let url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
        })
        .catch(error => {
            alert('Gagal cetak nota: ' + error);
        });
    });

    // Set default tanggal kembali +3 hari
    let defaultKembali = new Date();
    defaultKembali.setDate(defaultKembali.getDate() + 3);
    document.getElementById('tgl_jatuh_tempo').value = defaultKembali.toISOString().slice(0, 16);
    updatePreview();
</script>

<style>
.trx-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 18px;
}
.form-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
}
.form-sect {
    padding: 20px 24px;
    border-bottom: 1px solid #eee;
}
.form-sect-lbl {
    font-size: 11px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    margin-bottom: 14px;
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
    font-size: 11px;
    font-weight: 600;
    color: #666;
}
.finput, .fselect {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 13px;
}
.nota-panel {
    background: white;
    border: 1px solid #ddd;
    border-radius: 12px;
    position: sticky;
    top: 0;
}
.nota-preview-hd {
    padding: 13px 16px;
    background: #fafafa;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}
.nota-preview-title {
    font-size: 12px;
    font-weight: 700;
}
.nota-paper {
    margin: 14px;
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
}
.nota-top {
    background: #0a0a0a;
    padding: 14px 16px;
}
.nota-brand {
    font-size: 20px;
    font-style: italic;
    color: #e0c06e;
}
.nota-tagline {
    font-size: 8px;
    color: rgba(255,255,255,0.3);
}
.nota-trx-label {
    font-size: 8px;
    color: rgba(255,255,255,0.3);
    margin-top: 8px;
}
.nota-trx-num {
    font-family: monospace;
    font-size: 12px;
    color: #e0c06e;
}
.nota-body {
    padding: 13px 14px;
}
.nota-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 11px;
    border-bottom: 1px solid #f0f0f0;
}
.nota-key {
    color: #888;
}
.nota-val {
    color: #333;
    text-align: right;
}
.nota-total-box {
    background: #fafafa;
    border: 1px solid #e0c06e;
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
}
.nota-total-lbl {
    font-size: 12px;
    font-weight: 700;
}
.nota-total-val {
    font-family: monospace;
    font-size: 14px;
    font-weight: 700;
    color: #C9A84C;
}
.btn-gold {
    background: #C9A84C;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    color: white;
}
.btn-gold:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
@endsection