@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('breadcrumb', 'Edit Pelanggan')

@section('content')
<div class="pg-head">
    <div class="pg-title">Edit Pelanggan</div>
    <div class="pg-sub">Ubah data pelanggan</div>
</div>

<div class="form-card" style="max-width:600px; margin:0 auto;">
    <form action="{{ route('pelanggan.update', $pelanggan->id_pelanggan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-sect">
            <div class="field">
                <label class="flbl">Nama Pelanggan *</label>
                <input type="text" name="nama_pelanggan" class="finput" required value="{{ $pelanggan->nama_pelanggan }}">
            </div>
            <div class="field">
                <label class="flbl">No. Telepon *</label>
                <input type="text" name="no_telp" class="finput" required value="{{ $pelanggan->no_telp }}">
            </div>
            <div class="field">
                <label class="flbl">Alamat</label>
                <input type="text" name="alamat" class="finput" value="{{ $pelanggan->alamat }}">
            </div>
        </div>
        <div class="form-sect" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="{{ route('pelanggan.index') }}" class="btn-white">Batal</a>
            <button type="submit" class="btn-gold">Update</button>
        </div>
    </form>
</div>
@endsection
