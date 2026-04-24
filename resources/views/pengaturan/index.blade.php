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
        <button class="btn-outline" style="font-size:11.5px;padding:5px 12px" onclick="showEditProfilTokoModal()">✏ Edit</button>
    </div>
    <div style="padding:16px 18px;display:flex;flex-direction:column;gap:13px">
        <div style="display:flex;align-items:center;gap:14px;padding-bottom:13px;border-bottom:1px solid var(--gray-100)">
            <!-- LOGO PERUSAHAAN -->
            @if($profilToko->logo && file_exists(public_path($profilToko->logo)))
                <img src="{{ asset($profilToko->logo) }}" 
                     style="width: 52px; height: 52px; object-fit: cover; border-radius: 12px; border: 1.5px solid var(--gold-md);">
            @else
                <div style="width:52px;height:52px;border-radius:12px;background:var(--black);border:1.5px solid var(--gold-md);display:flex;align-items:center;justify-content:center;font-family:var(--ff-serif);font-style:italic;font-size:24px;color:var(--gold-lt);flex-shrink:0">N</div>
            @endif
            <div>
                <div style="font-size:15px;font-weight:700;color:var(--black)" id="profil_nama_toko">{{ $profilToko->nama_toko }}</div>
                <div style="font-size:11.5px;color:var(--gray-400);margin-top:2px">Penyewaan Baju Bodo Makassar</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Pemilik</div>
                <div style="font-size:12.5px;font-weight:500;color:var(--black)" id="profil_pemilik">{{ $profilToko->pemilik }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Telepon</div>
                <div style="font-size:12px;font-family:var(--ff-mono);color:var(--black)" id="profil_telepon">{{ $profilToko->telepon }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Alamat</div>
                <div style="font-size:12px;color:var(--black)" id="profil_alamat">{{ $profilToko->alamat }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px">Instagram</div>
                <div style="font-size:12px;color:var(--gold-dk);font-weight:600" id="profil_instagram">{{ $profilToko->instagram }}</div>
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
                    onclick="showTambahUserModal()">+ Tambah</button>
        </div>
        <div style="padding:4px 0">
            <!-- Owner -->
            <div class="user-item" onclick="showUserModal({{ $owner->id_user }}, '{{ $owner->nama_lengkap }}', '{{ $owner->username }}', 'Owner')">
                <div class="s-ava" style="width:36px;height:36px;font-size:13px">{{ substr($owner->nama_lengkap, 0, 1) }}</div>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600;color:var(--black)">{{ $owner->nama_lengkap }}</div>
                    <div style="font-size:11px;color:var(--gray-400)">{{ $owner->username }}</div>
                </div>
                <span style="background:var(--gold-xs);color:var(--gold-dk);border:1px solid var(--gold-md);padding:3px 9px;border-radius:5px;font-size:10.5px;font-weight:700">Owner</span>
            </div>
            <!-- Karyawan -->
            @foreach($karyawan as $item)
            <div class="user-item" style="border-top:1px solid var(--gray-100);" onclick="showUserModal({{ $item->id_user }}, '{{ $item->nama_lengkap }}', '{{ $item->username }}', 'Karyawan')">
                <div style="width:36px;height:36px;border-radius:50%;background:var(--gray-100);border:1.5px solid var(--gray-200);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--gray-500)">{{ substr($item->nama_lengkap, 0, 1) }}</div>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600;color:var(--black)">{{ $item->nama_lengkap }}</div>
                    <div style="font-size:11px;color:var(--gray-400)">{{ $item->username }}</div>
                </div>
                <span style="background:var(--gray-100);color:var(--gray-600);border:1px solid var(--gray-200);padding:3px 9px;border-radius:5px;font-size:10.5px;font-weight:700">Karyawan</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Tarif & Ketentuan -->
<div class="card gold-top">
    <div class="card-head">
        <div>
            <div class="card-title">Tarif &amp; Ketentuan</div>
            <div class="card-sub">Harga default dan denda keterlambatan</div>
        </div>
        <button class="btn-outline" style="font-size:11.5px;padding:5px 12px" id="editTarifBtn" onclick="showEditTarifModal()">✏ Edit</button>
    </div>
    <div style="padding:16px 18px;display:flex;flex-direction:column;gap:8px">
        <div class="tarif-item" data-key="tarif_dasar">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--gray-50);border-radius:var(--r2);border:1px solid var(--gray-200)">
                <div style="font-size:12.5px;color:var(--gray-600)">Tarif Dasar Baju Saja</div>
                <div style="font-family:var(--ff-mono);font-size:13px;font-weight:700;color:var(--gold-dk)" id="tarif_dasar_value">Rp {{ number_format($tarif['tarif_dasar'] ?? 150000, 0, ',', '.') }} / hari</div>
            </div>
        </div>
        <div class="tarif-item" data-key="tarif_fullset">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--gray-50);border-radius:var(--r2);border:1px solid var(--gray-200)">
                <div style="font-size:12.5px;color:var(--gray-600)">Tarif Full Set Pengantin</div>
                <div style="font-family:var(--ff-mono);font-size:13px;font-weight:700;color:var(--gold-dk)" id="tarif_fullset_value">Rp {{ number_format($tarif['tarif_fullset'] ?? 650000, 0, ',', '.') }} / set</div>
            </div>
        </div>
        <div class="tarif-item" data-key="jaminan">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--gray-50);border-radius:var(--r2);border:1px solid var(--gray-200)">
                <div style="font-size:12.5px;color:var(--gray-600)">Uang Jaminan Default</div>
                <div style="font-family:var(--ff-mono);font-size:13px;font-weight:700;color:var(--gold-dk)" id="jaminan_value">Rp {{ number_format($tarif['jaminan'] ?? 200000, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="tarif-item" data-key="denda">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:rgba(220,80,60,.04);border-radius:var(--r2);border:1px solid rgba(220,80,60,.15)">
                <div style="font-size:12.5px;color:var(--gray-600)">Denda Keterlambatan</div>
                <div style="font-family:var(--ff-mono);font-size:13px;font-weight:700;color:#c04030" id="denda_value">Rp {{ number_format($tarif['denda'] ?? 50000, 0, ',', '.') }} / hari</div>
            </div>
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
                <div onclick="toggleSwitch(this)"
                     data-on="{{ $on ? '1' : '0' }}"
                     style="width:36px;height:20px;border-radius:10px;position:relative;cursor:pointer;background:{{ $on ? 'var(--gold)' : 'var(--gray-200)' }};flex-shrink:0;transition:background .2s">
                    <div style="width:16px;height:16px;background:white;border-radius:50%;position:absolute;{{ $on ? 'right:2px' : 'left:2px' }};top:2px;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:all .2s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<!-- MODAL EDIT PROFIL TOKO -->
<div class="modal-overlay" id="editProfilTokoModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Edit Profil Toko</div>
                <div class="modal-popup-sub">Ubah informasi toko</div>
            </div>
            <button class="modal-popup-close" onclick="closeEditProfilTokoModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Logo Perusahaan</label>
                    <div id="logo_preview" style="margin-bottom: 8px;">
                        @if($profilToko->logo && file_exists(public_path($profilToko->logo)))
                            <img src="{{ asset($profilToko->logo) }}" style="max-width: 100px; max-height: 100px; border-radius: 8px; border: 1px solid var(--gray-200);">
                        @else
                            <span style="font-size: 12px; color: var(--gray-400);">Belum ada logo</span>
                        @endif
                    </div>
                    <input type="file" name="logo" id="edit_logo" class="modal-input" accept="image/jpeg,image/png,image/jpg">
                    <div style="margin-top: 8px;">
                        <label style="cursor: pointer;">
                            <input type="checkbox" name="hapus_logo" value="1" id="hapus_logo_checkbox"> Hapus logo yang ada
                        </label>
                    </div>
                    <small style="color: var(--gray-400); font-size: 10px;">Format: JPG, PNG. Max 2MB</small>
                </div>
                <div class="user-info-field">
                    <label>Nama Toko</label>
                    <input type="text" id="edit_nama_toko" class="modal-input" value="{{ $profilToko->nama_toko }}">
                </div>
                <div class="user-info-field">
                    <label>Pemilik</label>
                    <input type="text" id="edit_pemilik" class="modal-input" value="{{ $profilToko->pemilik }}">
                </div>
                <div class="user-info-field">
                    <label>Telepon</label>
                    <input type="text" id="edit_telepon" class="modal-input" value="{{ $profilToko->telepon }}">
                </div>
                <div class="user-info-field">
                    <label>Alamat</label>
                    <textarea id="edit_alamat" class="modal-input" rows="3">{{ $profilToko->alamat }}</textarea>
                </div>
                <div class="user-info-field">
                    <label>Instagram</label>
                    <input type="text" id="edit_instagram" class="modal-input" value="{{ $profilToko->instagram }}">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeEditProfilTokoModal()">Batal</button>
            <button class="btn-gold" onclick="updateProfilToko()">Simpan Perubahan</button>
        </div>
    </div>
</div>

<!-- MODAL POPUP UNTUK LIHAT & EDIT AKUN -->
<div class="modal-overlay" id="userModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Lihat Akun</div>
                <div class="modal-popup-sub" id="modalUserRole">Owner</div>
            </div>
            <button class="modal-popup-close" onclick="closeUserModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-avatar" id="modalUserAvatar">N</div>
            <div class="user-info">
                <div class="user-info-field">
                    <label>Nama Lengkap</label>
                    <input type="text" id="modalUserName" class="modal-input">
                </div>
                <div class="user-info-field">
                    <label>Username</label>
                    <input type="text" id="modalUserEmail" class="modal-input">
                </div>
                <div class="user-info-field">
                    <label>Role</label>
                    <input type="text" id="modalUserRoleInput" class="modal-input" readonly disabled>
                </div>
                <div class="user-info-divider"></div>
                <div class="user-info-field">
                    <label>Password Baru</label>
                    <input type="password" id="modalNewPassword" class="modal-input" placeholder="Kosongkan jika tidak ingin mengganti">
                </div>
                <div class="user-info-field">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" id="modalConfirmPassword" class="modal-input" placeholder="Ketik ulang password baru">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeUserModal()">Batal</button>
            <button class="btn-gold" onclick="saveUserChanges()">Simpan Perubahan</button>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH USER (KARYAWAN) -->
<div class="modal-overlay" id="tambahUserModal">
    <div class="modal-popup">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Tambah Akun Baru</div>
                <div class="modal-popup-sub">Tambahkan karyawan baru</div>
            </div>
            <button class="modal-popup-close" onclick="closeTambahUserModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Nama Lengkap</label>
                    <input type="text" id="newUserName" class="modal-input" placeholder="Nama lengkap karyawan">
                </div>
                <div class="user-info-field">
                    <label>Username</label>
                    <input type="text" id="newUserUsername" class="modal-input" placeholder="Username untuk login">
                </div>
                <div class="user-info-field">
                    <label>Password</label>
                    <input type="password" id="newUserPassword" class="modal-input" placeholder="Password minimal 6 karakter">
                </div>
                <div class="user-info-field">
                    <label>Konfirmasi Password</label>
                    <input type="password" id="newUserPasswordConfirm" class="modal-input" placeholder="Ketik ulang password">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeTambahUserModal()">Batal</button>
            <button class="btn-gold" onclick="saveNewUser()">Tambah Akun</button>
        </div>
    </div>
</div>

<!-- MODAL EDIT TARIF -->
<div class="modal-overlay" id="editTarifModal">
    <div class="modal-popup" style="max-width: 450px;">
        <div class="modal-popup-header">
            <div>
                <div class="modal-popup-title">Edit Tarif &amp; Ketentuan</div>
                <div class="modal-popup-sub">Ubah harga dan denda</div>
            </div>
            <button class="modal-popup-close" onclick="closeEditTarifModal()">✕</button>
        </div>
        <div class="modal-popup-body">
            <div class="user-info">
                <div class="user-info-field">
                    <label>Tarif Dasar Baju Saja (per hari)</label>
                    <input type="number" id="edit_tarif_dasar" class="modal-input" placeholder="150000">
                </div>
                <div class="user-info-field">
                    <label>Tarif Full Set Pengantin (per set)</label>
                    <input type="number" id="edit_tarif_fullset" class="modal-input" placeholder="650000">
                </div>
                <div class="user-info-field">
                    <label>Uang Jaminan Default</label>
                    <input type="number" id="edit_jaminan" class="modal-input" placeholder="200000">
                </div>
                <div class="user-info-field">
                    <label>Denda Keterlambatan (per hari)</label>
                    <input type="number" id="edit_denda" class="modal-input" placeholder="50000">
                </div>
            </div>
        </div>
        <div class="modal-popup-footer">
            <button class="btn-white" onclick="closeEditTarifModal()">Batal</button>
            <button class="btn-gold" onclick="saveTarifChanges()">Simpan Perubahan</button>
        </div>
    </div>
</div>

<style>
.user-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    cursor: pointer;
    transition: background 0.15s;
}
.user-item:hover {
    background: var(--gray-50);
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.25s ease;
}
.modal-overlay.show {
    opacity: 1;
    visibility: visible;
}
.modal-popup {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 480px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    transform: scale(0.95);
    transition: transform 0.25s ease;
}
.modal-overlay.show .modal-popup {
    transform: scale(1);
}
.modal-popup-header {
    padding: 20px 24px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.modal-popup-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--black);
}
.modal-popup-sub {
    font-size: 11px;
    color: var(--gold-dk);
    margin-top: 4px;
}
.modal-popup-close {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    background: white;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.15s;
}
.modal-popup-close:hover {
    border-color: var(--gold-rim);
    color: var(--gold-dk);
}
.modal-popup-body {
    padding: 24px;
    max-height: 60vh;
    overflow-y: auto;
}
.user-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #e0c06e, #C9A84C, #a07830);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Instrument Serif', serif;
    font-size: 32px;
    font-style: italic;
    color: #0a0a0a;
    margin: 0 auto 20px;
    box-shadow: 0 5px 15px rgba(201,168,76,0.3);
}
.user-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.user-info-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.user-info-field label {
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.modal-input {
    padding: 12px 14px;
    border: 1.5px solid var(--gray-200);
    border-radius: 10px;
    font-size: 13px;
    font-family: inherit;
    transition: all 0.2s;
}
.modal-input:focus {
    outline: none;
    border-color: #C9A84C;
    box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
}
.modal-input:disabled {
    background: var(--gray-50);
    color: var(--gray-500);
}
.user-info-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 8px 0;
}
.modal-popup-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}
</style>

<script>
    let currentUserId = null;
    let currentUserRole = null;

    function showUserModal(id, name, username, role) {
        currentUserId = id;
        currentUserRole = role;
        document.getElementById('modalUserAvatar').innerText = name.charAt(0);
        document.getElementById('modalUserName').value = name;
        document.getElementById('modalUserEmail').value = username;
        document.getElementById('modalUserRoleInput').value = role;
        document.getElementById('modalUserRole').innerText = role;
        document.getElementById('modalNewPassword').value = '';
        document.getElementById('modalConfirmPassword').value = '';
        document.getElementById('userModal').classList.add('show');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.remove('show');
    }

    function saveUserChanges() {
        const namaLengkap = document.getElementById('modalUserName').value;
        const username = document.getElementById('modalUserEmail').value;
        const newPassword = document.getElementById('modalNewPassword').value;
        const confirmPassword = document.getElementById('modalConfirmPassword').value;

        if (newPassword !== confirmPassword) {
            alert('Password baru dan konfirmasi password tidak sama!');
            return;
        }

        const formData = new FormData();
        formData.append('id_user', currentUserId);
        formData.append('nama_lengkap', namaLengkap);
        formData.append('username', username);
        if (newPassword) {
            formData.append('new_password', newPassword);
            formData.append('new_password_confirmation', confirmPassword);
        }
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("pengaturan.update.profile") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profil berhasil diupdate');
                location.reload();
            } else {
                alert(data.message || 'Gagal update profil');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }

    function showTambahUserModal() {
        document.getElementById('newUserName').value = '';
        document.getElementById('newUserUsername').value = '';
        document.getElementById('newUserPassword').value = '';
        document.getElementById('newUserPasswordConfirm').value = '';
        document.getElementById('tambahUserModal').classList.add('show');
    }

    function closeTambahUserModal() {
        document.getElementById('tambahUserModal').classList.remove('show');
    }

    function saveNewUser() {
        const namaLengkap = document.getElementById('newUserName').value;
        const username = document.getElementById('newUserUsername').value;
        const password = document.getElementById('newUserPassword').value;
        const passwordConfirm = document.getElementById('newUserPasswordConfirm').value;

        if (!namaLengkap) { alert('Nama lengkap harus diisi!'); return; }
        if (!username) { alert('Username harus diisi!'); return; }
        if (!password) { alert('Password harus diisi!'); return; }
        if (password !== passwordConfirm) { alert('Password tidak sama!'); return; }
        if (password.length < 6) { alert('Password minimal 6 karakter!'); return; }

        const formData = new FormData();
        formData.append('nama_lengkap', namaLengkap);
        formData.append('username', username);
        formData.append('password', password);
        formData.append('role', 'Karyawan');
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("pengaturan.tambah.user") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Akun karyawan berhasil ditambahkan');
                location.reload();
            } else {
                alert(data.message || 'Gagal menambah akun');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }

    function toggleSwitch(el) {
        const isOn = el.dataset.on === '1';
        const newState = isOn ? '0' : '1';
        el.dataset.on = newState;
        el.style.background = newState === '1' ? 'var(--gold)' : 'var(--gray-200)';
        const dot = el.querySelector('div');
        if (newState === '1') {
            dot.style.left = '';
            dot.style.right = '2px';
        } else {
            dot.style.left = '2px';
            dot.style.right = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUserModal();
            closeTambahUserModal();
            closeEditTarifModal();
            closeEditProfilTokoModal();
        }
    });

    // EDIT TARIF
    function showEditTarifModal() {
        let tarifDasar = document.getElementById('tarif_dasar_value').innerText.replace('Rp ', '').replace(' / hari', '').replace(/\./g, '');
        let tarifFullset = document.getElementById('tarif_fullset_value').innerText.replace('Rp ', '').replace(' / set', '').replace(/\./g, '');
        let jaminan = document.getElementById('jaminan_value').innerText.replace('Rp ', '').replace(/\./g, '');
        let denda = document.getElementById('denda_value').innerText.replace('Rp ', '').replace(' / hari', '').replace(/\./g, '');
        
        document.getElementById('edit_tarif_dasar').value = parseInt(tarifDasar);
        document.getElementById('edit_tarif_fullset').value = parseInt(tarifFullset);
        document.getElementById('edit_jaminan').value = parseInt(jaminan);
        document.getElementById('edit_denda').value = parseInt(denda);
        
        document.getElementById('editTarifModal').classList.add('show');
    }

    function closeEditTarifModal() {
        document.getElementById('editTarifModal').classList.remove('show');
    }

    function saveTarifChanges() {
        const tarifDasar = document.getElementById('edit_tarif_dasar').value;
        const tarifFullset = document.getElementById('edit_tarif_fullset').value;
        const jaminan = document.getElementById('edit_jaminan').value;
        const denda = document.getElementById('edit_denda').value;
        
        const formData = new FormData();
        formData.append('tarif_dasar', tarifDasar);
        formData.append('tarif_fullset', tarifFullset);
        formData.append('jaminan', jaminan);
        formData.append('denda', denda);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("pengaturan.update.tarif") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('tarif_dasar_value').innerHTML = 'Rp ' + parseInt(tarifDasar).toLocaleString('id-ID') + ' / hari';
                document.getElementById('tarif_fullset_value').innerHTML = 'Rp ' + parseInt(tarifFullset).toLocaleString('id-ID') + ' / set';
                document.getElementById('jaminan_value').innerHTML = 'Rp ' + parseInt(jaminan).toLocaleString('id-ID');
                document.getElementById('denda_value').innerHTML = 'Rp ' + parseInt(denda).toLocaleString('id-ID') + ' / hari';
                
                alert('Tarif berhasil diupdate');
                closeEditTarifModal();
            } else {
                alert(data.message || 'Gagal update tarif');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }

    // EDIT PROFIL TOKO
    function showEditProfilTokoModal() {
        document.getElementById('edit_nama_toko').value = document.getElementById('profil_nama_toko').innerText;
        document.getElementById('edit_pemilik').value = document.getElementById('profil_pemilik').innerText;
        document.getElementById('edit_telepon').value = document.getElementById('profil_telepon').innerText;
        document.getElementById('edit_alamat').value = document.getElementById('profil_alamat').innerText;
        document.getElementById('edit_instagram').value = document.getElementById('profil_instagram').innerText;
        
        let chkHapus = document.getElementById('hapus_logo_checkbox');
        if (chkHapus) chkHapus.checked = false;
        
        document.getElementById('editProfilTokoModal').classList.add('show');
    }

    function closeEditProfilTokoModal() {
        document.getElementById('editProfilTokoModal').classList.remove('show');
    }

    function updateProfilToko() {
        const formData = new FormData();
        formData.append('nama_toko', document.getElementById('edit_nama_toko').value);
        formData.append('pemilik', document.getElementById('edit_pemilik').value);
        formData.append('telepon', document.getElementById('edit_telepon').value);
        formData.append('alamat', document.getElementById('edit_alamat').value);
        formData.append('instagram', document.getElementById('edit_instagram').value);
        formData.append('_token', '{{ csrf_token() }}');
        
        let logoFile = document.getElementById('edit_logo').files[0];
        if (logoFile) {
            formData.append('logo', logoFile);
        }
        
        let hapusLogo = document.getElementById('hapus_logo_checkbox').checked;
        if (hapusLogo) {
            formData.append('hapus_logo', '1');
        }
        
        fetch('{{ route("pengaturan.update.profil_toko") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profil toko berhasil diupdate');
                location.reload();
            } else {
                alert(data.message || 'Gagal update profil toko');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    }
</script>
@endsection