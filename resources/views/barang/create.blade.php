@extends('layouts.app')

@section('title', 'Tambah Barang')
@section('breadcrumb', 'Tambah Koleksi Baru')

@section('content')
<div class="pg-head">
    <div class="pg-title">Tambah Koleksi Baru</div>
    <div class="pg-sub">Masukkan data baju baru ke inventaris</div>
</div>

<div class="form-card" style="max-width:580px;margin:0 auto">
    <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <div class="form-sect">
            <div class="form-sect-lbl">Informasi Baju</div>
            <div class="fgrid">
                <div class="field f-full">
                    <label class="flbl">Nama Baju *</label>
                    <input type="text" name="nama_barang" class="finput" required
                           placeholder="Contoh: Baju Bodo Sutra Hijau Emerald"
                           value="{{ old('nama_barang') }}">
                    @error('nama_barang')<div style="font-size:11px;color:#c0392b;margin-top:3px">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="flbl">Harga Sewa / Hari *</label>
                    <input type="number" name="harga_sewa" class="finput" required
                           placeholder="200000"
                           value="{{ old('harga_sewa') }}">
                    @error('harga_sewa')<div style="font-size:11px;color:#c0392b;margin-top:3px">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="flbl">Status</label>
                    <select name="status_barang" class="fselect">
                        <option value="Tersedia" {{ old('status_barang') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Disewa"   {{ old('status_barang') == 'Disewa' ? 'selected' : '' }}>Disewa</option>
                        <option value="Laundry"  {{ old('status_barang') == 'Laundry' ? 'selected' : '' }}>Laundry</option>
                        <option value="Rusak"    {{ old('status_barang') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;background:var(--gray-50)">
            <a href="{{ route('barang.index') }}" class="btn-white">Batal</a>
            <button type="submit" class="btn-gold">Simpan Koleksi</button>
        </div>
    </form>
</div>
@endsection