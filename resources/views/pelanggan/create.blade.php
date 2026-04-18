@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('breadcrumb', 'Tambah Pelanggan')

@section('content')
<div class="pg-head">
    <div class="pg-title">Tambah Pelanggan Baru</div>
    <div class="pg-sub">Masukkan data pelanggan</div>
</div>

<div class="form-card" style="max-width:600px; margin:0 auto;">
    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf
        <div class="form-sect">
            <div class="field">
                <label class="flbl">Nama Pelanggan *</label>
                <input type="text" name="nama_pelanggan" class="finput" required>
            </div>
            <div class="field">
                <label class="flbl">No. Telepon *</label>
                <input type="text" name="no_telp" class="finput" required>
            </div>
            <div class="field">
                <label class="flbl">Alamat</label>
                <input type="text" name="alamat" class="finput">
            </div>
        </div>
        <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('pelanggan.index') }}" class="btn-white">Batal</a>
            <button type="submit" class="btn-gold">Simpan</button>
        </div>
    </form>
</div>
@endsection
