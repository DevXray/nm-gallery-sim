@extends('layouts.app')

@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')

@section('content')
<div class="pg-head">
    <div class="pg-title">Pengaturan</div>
    <div class="pg-sub">Kelola informasi toko, akun, dan preferensi sistem</div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

    <!-- Profil Toko -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Profil Toko</div>
                <div class="card-sub">Informasi NM Gallery</div>
            </div>
            <button class="btn-outline" style="font-size:11.5px;padding:5px 12px" onclick="alert('Fitur edit profil toko dalam pengembangan')">✏ Edit</button>
        </div>
        <div style="padding: 16px 18px; display: flex; flex-direction: column; gap: 13px;">
            <div style="display: flex; align-items: center; gap: 14px; padding-bottom: 13px; border-bottom: 1px solid var(--gray-100);">
                <div style="width: 56px; height: 56px; border-radius: 12px; background: var(--black); border: 1.5px solid var(--gold-md); display: flex; align-items: center; justify-content: center; font-family: 'Instrument Serif', serif; font-style: italic; font-size: 26px; color: var(--gold-lt);">N</div>
                <div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--black);">NM Gallery</div>
                    <div style="font-size: 11.5px; color: var(--gray-400); margin-top: 2px;">Penyewaan Baju Bodo Makassar</div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--gray-400); text-transform: uppercase; margin-bottom: 4px;">Pemilik</div>
                    <div style="font-size: 12.5px; font-weight: 500; color: var(--black);">Nurhayati</div>
                </div>
                <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--gray-400); text-transform: uppercase; margin-bottom: 4px;">Telepon</div>
                    <div style="font-size: 12.5px; font-family: monospace; color: var(--black);">+62 411-xxx-xxxx</div>
                </div>
                <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--gray-400); text-transform: uppercase; margin-bottom: 4px;">Alamat</div>
                    <div style="font-size: 12.5px; color: var(--black);">Jl. Somba Opu No. 12, Makassar</div>
                </div>
                <div>
                    <div style="font-size: 10.5px; font-weight: 700; color: var(--gray-400); text-transform: uppercase; margin-bottom: 4px;">Instagram</div>
                    <div style="font-size: 12.5px; color: var(--gold-dk);">@nmgallery.id</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Akun & Pengguna -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Akun &amp; Pengguna</div>
                <div class="card-sub">Kelola akses admin dan owner</div>
            </div>
            <button class="btn-outline" style="font-size:11.5px;padding:5px 12px" onclick="alert('Fitur tambah user dalam pengembangan')">+ Tambah</button>
        </div>
        <div style="padding: 4px 0;">
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px 18px; border-bottom: 1px solid var(--gray-100);">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--black); border: 1.5px solid var(--gold-md); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--gold-lt);">N</div>
                <div style="flex: 1;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--black);">Nurhayati</div>
                    <div style="font-size: 11px; color: var(--gray-400);">nurhayati@nmgallery.id</div>
                </div>
                <span style="background: var(--gold-xs); color: var(--gold-dk); border: 1px solid var(--gold-md); padding: 3px 9px; border-radius: 5px; font-size: 10.5px; font-weight: 700;">Owner</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px 18px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--gray-100); border: 1.5px solid var(--gray-200); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--gray-500);">R</div>
                <div style="flex: 1;">
                    <div style="font-size: 13px; font-weight: 600; color: var(--black);">Rini Astuti</div>
                    <div style="font-size: 11px; color: var(--gray-400);">rini@nmgallery.id</div>
                </div>
                <span style="background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); padding: 3px 9px; border-radius: 5px; font-size: 10.5px; font-weight: 700;">Admin</span>
            </div>
        </div>
    </div>

    <!-- Tarif & Denda -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Tarif &amp; Ketentuan</div>
                <div class="card-sub">Atur harga default dan denda keterlambatan</div>
            </div>
            <button class="btn-outline" style="font-size:11.5px;padding:5px 12px" onclick="alert('Fitur edit tarif dalam pengembangan')">✏ Edit</button>
        </div>
        <div style="padding: 16px 18px; display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                <div style="font-size: 12.5px; color: var(--gray-600);">Tarif Dasar Baju Saja</div>
                <div style="font-family: monospace; font-size: 13px; font-weight: 700; color: var(--gold-dk);">Rp 150.000 / hari</div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                <div style="font-size: 12.5px; color: var(--gray-600);">Tarif Full Set Pengantin</div>
                <div style="font-family: monospace; font-size: 13px; font-weight: 700; color: var(--gold-dk);">Rp 650.000 / set</div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: rgba(220,80,60,0.04); border-radius: 8px; border: 1px solid rgba(220,80,60,0.15);">
                <div style="font-size: 12.5px; color: var(--gray-600);">Denda Keterlambatan</div>
                <div style="font-family: monospace; font-size: 13px; font-weight: 700; color: #c04030;">Rp 50.000 / hari</div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                <div style="font-size: 12.5px; color: var(--gray-600);">Uang Jaminan Default</div>
                <div style="font-family: monospace; font-size: 13px; font-weight: 700; color: var(--gold-dk);">Rp 200.000</div>
            </div>
        </div>
    </div>

    <!-- Notifikasi -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Notifikasi</div>
                <div class="card-sub">Pengingat otomatis via WhatsApp</div>
            </div>
        </div>
        <div style="padding: 16px 18px; display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                <div>
                    <div style="font-size: 12.5px; font-weight: 600; color: var(--black);">Pengingat H-1 Kembali</div>
                    <div style="font-size: 11px; color: var(--gray-400); margin-top: 2px;">WA otomatis sehari sebelum jatuh tempo</div>
                </div>
                <div style="width: 36px; height: 20px; background: var(--gold); border-radius: 10px; position: relative; cursor: pointer;">
                    <div style="width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; right: 2px; top: 2px;"></div>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                <div>
                    <div style="font-size: 12.5px; font-weight: 600; color: var(--black);">Notif Keterlambatan</div>
                    <div style="font-size: 11px; color: var(--gray-400); margin-top: 2px;">WA otomatis jika melewati jatuh tempo</div>
                </div>
                <div style="width: 36px; height: 20px; background: var(--gold); border-radius: 10px; position: relative; cursor: pointer;">
                    <div style="width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; right: 2px; top: 2px;"></div>
                </div>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                <div>
                    <div style="font-size: 12.5px; font-weight: 600; color: var(--black);">Kirim E-Nota Otomatis</div>
                    <div style="font-size: 11px; color: var(--gray-400); margin-top: 2px;">Otomatis kirim nota ke WA pelanggan</div>
                </div>
                <div style="width: 36px; height: 20px; background: var(--gray-200); border-radius: 10px; position: relative; cursor: pointer;">
                    <div style="width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; left: 2px; top: 2px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Profil -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Profil Saya</div>
                <div class="card-sub">Update informasi akun Anda</div>
            </div>
        </div>
        <div style="padding: 16px 20px;">
            <form method="POST" action="{{ route('pengaturan.update.profile') }}">
                @csrf
                <div class="field">
                    <label class="flbl">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="finput" value="{{ $pengguna->nama_lengkap ?? '' }}" required>
                </div>
                <div class="field">
                    <label class="flbl">Username</label>
                    <input type="text" name="username" class="finput" value="{{ $pengguna->username ?? '' }}" required>
                </div>
                <button type="submit" class="btn-gold" style="margin-top: 12px;">Update Profil</button>
            </form>
        </div>
    </div>

    <!-- Ganti Password -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Ganti Password</div>
                <div class="card-sub">Perbarui password akun Anda</div>
            </div>
        </div>
        <div style="padding: 16px 20px;">
            <form method="POST" action="{{ route('pengaturan.update.password') }}">
                @csrf
                <div class="field">
                    <label class="flbl">Password Lama</label>
                    <input type="password" name="current_password" class="finput" required>
                </div>
                <div class="field">
                    <label class="flbl">Password Baru</label>
                    <input type="password" name="new_password" class="finput" required>
                </div>
                <div class="field">
                    <label class="flbl">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="finput" required>
                </div>
                <button type="submit" class="btn-gold" style="margin-top: 12px;">Ganti Password</button>
            </form>
        </div>
    </div>

</div>

@if(session('success'))
<div style="position: fixed; bottom: 20px; right: 20px; background: #2da66e; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000;">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="position: fixed; bottom: 20px; right: 20px; background: #dc2626; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000;">
    {{ $errors->first() }}
</div>
@endif

<style>
.field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 12px;
}
.flbl {
    font-size: 12px;
    font-weight: 600;
    color: #52525b;
}
.finput {
    padding: 10px 14px;
    border: 1px solid #e4e4e7;
    border-radius: 8px;
    font-size: 13px;
}
.finput:focus {
    outline: none;
    border-color: #C9A84C;
    box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
}
</style>
@endsection