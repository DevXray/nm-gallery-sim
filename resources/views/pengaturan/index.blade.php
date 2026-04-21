@extends('layouts.app')

@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')

@section('content')
<div class="pg-head">
    <div class="pg-title">Pengaturan</div>
    <div class="pg-sub">Kelola informasi toko, akun, dan preferensi sistem</div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Profil Toko -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Profil Toko</div>
                <div class="card-sub">Informasi NM Gallery</div>
            </div>
            <button class="btn-outline" style="font-size:11.5px;padding:5px 12px"
                    onclick="alert('Fitur edit profil toko dalam pengembangan')">✏ Edit</button>
        </div>
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:13px">
            <div style="display:flex;align-items:center;gap:14px;padding-bottom:13px;border-bottom:1px solid var(--gray-100)">
                <div style="width:52px;height:52px;border-radius:12px;background:var(--black);border:1.5px solid var(--gold-md);display:flex;align-items:center;justify-content:center;font-family:var(--ff-serif);font-style:italic;font-size:24px;color:var(--gold-lt);flex-shrink:0">N</div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--black)">NM Gallery</div>
                    <div style="font-size:11.5px;color:var(--gray-400);margin-top:2px">Penyewaan Baju Bodo Makassar</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Pemilik</div>
                    <div style="font-size:12.5px;font-weight:500;color:var(--black)">Nurhayati</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Telepon</div>
                    <div style="font-size:12px;font-family:var(--ff-mono);color:var(--black)">+62 411-xxx-xxxx</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Alamat</div>
                    <div style="font-size:12px;color:var(--black)">Jl. Somba Opu No. 12, Makassar</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Instagram</div>
                    <div style="font-size:12px;color:var(--gold-dk);font-weight:600">@nmgallery.id</div>
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
            <button class="btn-outline" style="font-size:11.5px;padding:5px 12px"
                    onclick="alert('Fitur tambah user dalam pengembangan')">+ Tambah</button>
        </div>
        <div style="padding:4px 0">
            <div style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid var(--gray-100)">
                <div class="s-ava" style="width:36px;height:36px;font-size:13px">N</div>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600;color:var(--black)">Nurhayati</div>
                    <div style="font-size:11px;color:var(--gray-400)">nurhayati@nmgallery.id</div>
                </div>
                <span style="background:var(--gold-xs);color:var(--gold-dk);border:1px solid var(--gold-md);padding:3px 9px;border-radius:5px;font-size:10.5px;font-weight:700">Owner</span>
            </div>
            <div style="display:flex;align-items:center;gap:12px;padding:12px 18px">
                <div style="width:36px;height:36px;border-radius:50%;background:var(--gray-100);border:1.5px solid var(--gray-200);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--gray-500)">R</div>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600;color:var(--black)">Rini Astuti</div>
                    <div style="font-size:11px;color:var(--gray-400)">rini@nmgallery.id</div>
                </div>
                <span style="background:var(--gray-100);color:var(--gray-600);border:1px solid var(--gray-200);padding:3px 9px;border-radius:5px;font-size:10.5px;font-weight:700">Karyawan</span>
            </div>
        </div>
    </div>

    <!-- Tarif & Ketentuan -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Tarif &amp; Ketentuan</div>
                <div class="card-sub">Harga default dan denda keterlambatan</div>
            </div>
            <button class="btn-outline" style="font-size:11.5px;padding:5px 12px"
                    onclick="alert('Fitur edit tarif dalam pengembangan')">✏ Edit</button>
        </div>
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:8px">
            @foreach([
                ['Tarif Dasar Baju Saja', 'Rp 150.000 / hari', false],
                ['Tarif Full Set Pengantin', 'Rp 650.000 / set', false],
                ['Uang Jaminan Default', 'Rp 200.000', false],
                ['Denda Keterlambatan', 'Rp 50.000 / hari', true],
            ] as [$label, $val, $danger])
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:{{ $danger ? 'rgba(220,80,60,.04)' : 'var(--gray-50)' }};border-radius:var(--r2);border:1px solid {{ $danger ? 'rgba(220,80,60,.15)' : 'var(--gray-200)' }}">
                <div style="font-size:12.5px;color:var(--gray-600)">{{ $label }}</div>
                <div style="font-family:var(--ff-mono);font-size:13px;font-weight:700;color:{{ $danger ? '#c04030' : 'var(--gold-dk)' }}">{{ $val }}</div>
            </div>
            @endforeach
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
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:10px">
            @foreach([
                ['Pengingat H-1 Kembali', 'WA otomatis sehari sebelum jatuh tempo', true],
                ['Notif Keterlambatan', 'WA otomatis jika melewati jatuh tempo', true],
                ['Kirim E-Nota Otomatis', 'Otomatis kirim nota ke WA pelanggan', false],
            ] as [$title, $desc, $on])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--gray-50);border-radius:var(--r2);border:1px solid var(--gray-200)">
                <div>
                    <div style="font-size:12.5px;font-weight:600;color:var(--black)">{{ $title }}</div>
                    <div style="font-size:11px;color:var(--gray-400);margin-top:2px">{{ $desc }}</div>
                </div>
                <div onclick="this.dataset.on = this.dataset.on == '1' ? '0' : '1'; toggleSwitch(this)"
                     data-on="{{ $on ? '1' : '0' }}"
                     style="width:36px;height:20px;border-radius:10px;position:relative;cursor:pointer;background:{{ $on ? 'var(--gold)' : 'var(--gray-200)' }};flex-shrink:0;transition:background .2s">
                    <div style="width:16px;height:16px;background:white;border-radius:50%;position:absolute;{{ $on ? 'right:2px' : 'left:2px' }};top:2px;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:all .2s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Profil Saya -->
    <div class="card gold-top">
        <div class="card-head">
            <div>
                <div class="card-title">Profil Saya</div>
                <div class="card-sub">Update informasi akun Anda</div>
            </div>
        </div>
        <div style="padding:16px 20px">
            @if(session('success') && str_contains(session('success'), 'Profil'))
            <div style="background:rgba(45,166,110,.08);border:1px solid rgba(45,166,110,.2);color:#1a8050;padding:10px 14px;border-radius:var(--r2);font-size:12.5px;margin-bottom:14px">
                {{ session('success') }}
            </div>
            @endif
            <form method="POST" action="{{ route('pengaturan.update.profile') }}">
                @csrf
                <div class="field" style="margin-bottom:12px">
                    <label class="flbl">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="finput" required value="{{ $pengguna->nama_lengkap ?? '' }}">
                </div>
                <div class="field" style="margin-bottom:14px">
                    <label class="flbl">Username</label>
                    <input type="text" name="username" class="finput" required value="{{ $pengguna->username ?? '' }}">
                </div>
                <button type="submit" class="btn-gold">Update Profil</button>
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
        <div style="padding:16px 20px">
            @if($errors->any())
            <div style="background:rgba(220,80,60,.07);border:1px solid rgba(220,80,60,.2);color:#c0392b;padding:10px 14px;border-radius:var(--r2);font-size:12.5px;margin-bottom:14px">
                {{ $errors->first() }}
            </div>
            @endif
            <form method="POST" action="{{ route('pengaturan.update.password') }}">
                @csrf
                <div class="field" style="margin-bottom:12px">
                    <label class="flbl">Password Lama</label>
                    <input type="password" name="current_password" class="finput" required>
                </div>
                <div class="field" style="margin-bottom:12px">
                    <label class="flbl">Password Baru</label>
                    <input type="password" name="new_password" class="finput" required>
                </div>
                <div class="field" style="margin-bottom:14px">
                    <label class="flbl">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="finput" required>
                </div>
                <button type="submit" class="btn-gold">Ganti Password</button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function toggleSwitch(el) {
    const isOn = el.dataset.on === '1';
    el.style.background = isOn ? 'var(--gold)' : 'var(--gray-200)';
    const dot = el.querySelector('div');
    dot.style.left  = isOn ? '' : '2px';
    dot.style.right = isOn ? '2px' : '';
}
</script>
@endpush
@endsection