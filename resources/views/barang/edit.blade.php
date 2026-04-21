@extends('layouts.app')

@section('title', 'Edit Barang')
@section('breadcrumb', 'Edit Koleksi')

@section('content')
<div class="pg-head">
    <div class="pg-title">Edit Koleksi</div>
    <div class="pg-sub">Ubah data baju yang sudah ada</div>
</div>

<div class="form-card" style="max-width:580px;margin:0 auto">
    <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-sect">
            <div class="form-sect-lbl">Informasi Baju</div>
            <div class="fgrid">
                <div class="field f-full">
                    <label class="flbl">Nama Baju *</label>
                    <input type="text" name="nama_barang" class="finput" required
                           value="{{ old('nama_barang', $barang->nama_barang) }}">
                    @error('nama_barang')<div style="font-size:11px;color:#c0392b;margin-top:3px">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="flbl">Harga Sewa / Hari *</label>
                    <input type="number" name="harga_sewa" class="finput" required
                           value="{{ old('harga_sewa', $barang->harga_sewa) }}">
                    @error('harga_sewa')<div style="font-size:11px;color:#c0392b;margin-top:3px">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label class="flbl">Status</label>
                    <select name="status_barang" class="fselect">
                        <option value="Tersedia" {{ $barang->status_barang == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Disewa"   {{ $barang->status_barang == 'Disewa' ? 'selected' : '' }}>Disewa</option>
                        <option value="Laundry"  {{ $barang->status_barang == 'Laundry' ? 'selected' : '' }}>Laundry</option>
                        <option value="Rusak"    {{ $barang->status_barang == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;background:var(--gray-50)">
            <a href="{{ route('barang.index') }}" class="btn-white">Batal</a>
            <button type="submit" class="btn-gold">Update Koleksi</button>
        </div>
    </form>
</div>
@endsection