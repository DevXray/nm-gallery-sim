<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\ProfilToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function index()
    {
        $owner = Pengguna::where('role', 'Owner')->first();
        $karyawan = Pengguna::where('role', 'Karyawan')->get();
        
        // Ambil atau buat profil toko
        $profilToko = ProfilToko::first();
        if (!$profilToko) {
            $profilToko = ProfilToko::create([
                'nama_toko' => 'NM Gallery',
                'pemilik' => 'Nurhayati',
                'telepon' => '+62 411-xxx-xxxx',
                'alamat' => 'Jl. Somba Opu No. 12, Makassar',
                'instagram' => '@nmgallery.id',
            ]);
        }
        
        // Load tarif dari file
        $tarifFile = storage_path('app/tarif.json');
        if (file_exists($tarifFile)) {
            $tarif = json_decode(file_get_contents($tarifFile), true);
        } else {
            $tarif = [
                'tarif_dasar' => 150000,
                'tarif_fullset' => 650000,
                'jaminan' => 200000,
                'denda' => 50000,
            ];
        }
        
        return view('pengaturan.index', compact('owner', 'karyawan', 'profilToko', 'tarif'));
    }
    
    public function updateProfilToko(Request $request)
{
    try {
        $profilToko = ProfilToko::first();
        
        $request->validate([
            'nama_toko' => 'required',
            'pemilik' => 'required',
            'telepon' => 'required',
            'alamat' => 'required',
            'instagram' => 'nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Upload logo baru
        $logoPath = $profilToko->logo;
        if ($request->hasFile('logo')) {
            // Hapus logo lama
            if ($profilToko->logo && file_exists(public_path($profilToko->logo))) {
                unlink(public_path($profilToko->logo));
            }
            $file = $request->file('logo');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/toko'), $filename);
            $logoPath = 'uploads/toko/' . $filename;
        }
        
        // Hapus logo jika dicentang
        if ($request->has('hapus_logo') && $request->hapus_logo == '1') {
            if ($profilToko->logo && file_exists(public_path($profilToko->logo))) {
                unlink(public_path($profilToko->logo));
            }
            $logoPath = null;
        }
        
        $profilToko->update([
            'nama_toko' => $request->nama_toko,
            'pemilik' => $request->pemilik,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'instagram' => $request->instagram,
            'logo' => $logoPath,
        ]);
        
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    
    public function updateProfile(Request $request)
    {
        $pengguna = Pengguna::findOrFail($request->id_user);
        
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:pengguna,username,' . $pengguna->id_user . ',id_user',
        ]);
        
        $updateData = [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
        ];
        
        if ($request->filled('new_password')) {
            $request->validate([
                'new_password' => 'min:6|confirmed',
            ]);
            $updateData['password'] = Hash::make($request->new_password);
        }
        
        $pengguna->update($updateData);
        
        // Update session jika yang diupdate adalah user yang sedang login
        if (session('user')['id_user'] == $pengguna->id_user) {
            session(['user' => array_merge(session('user'), [
                'nama_lengkap' => $request->nama_lengkap,
                'username' => $request->username,
            ])]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function tambahUser(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:pengguna,username',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);
        
        Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        return response()->json(['success' => true]);
    }
    public function updateTarif(Request $request)
{
    // Simpan tarif ke file config atau database
    // Untuk sementara simpan ke session atau file json
    $tarif = [
        'tarif_dasar' => $request->tarif_dasar,
        'tarif_fullset' => $request->tarif_fullset,
        'jaminan' => $request->jaminan,
        'denda' => $request->denda,
    ];
    
    // Simpan ke file storage
    file_put_contents(storage_path('app/tarif.json'), json_encode($tarif));
    
    return response()->json(['success' => true]);
}
}

