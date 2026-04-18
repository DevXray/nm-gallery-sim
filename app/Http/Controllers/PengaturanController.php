<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = session('user');
        $pengguna = Pengguna::find($user['id_user']);
        
        // Data untuk tampilan (tidak pakai $periode)
        $data = [
            'pengguna' => $pengguna,
            'title' => 'Pengaturan',
            'breadcrumb' => 'Pengaturan'
        ];
        
        return view('pengaturan.index', $data);
    }
    
    public function updateProfile(Request $request)
    {
        $user = session('user');
        $pengguna = Pengguna::find($user['id_user']);
        
        $request->validate([
            'nama_lengkap' => 'required',
            'username' => 'required|unique:pengguna,username,' . $pengguna->id_user . ',id_user',
        ]);
        
        $pengguna->update([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
        ]);
        
        // Update session
        session(['user' => array_merge($user, [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
        ])]);
        
        return back()->with('success', 'Profil berhasil diupdate');
    }
    
    public function updatePassword(Request $request)
    {
        $user = session('user');
        $pengguna = Pengguna::find($user['id_user']);
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        
        if (!Hash::check($request->current_password, $pengguna->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah']);
        }
        
        $pengguna->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return back()->with('success', 'Password berhasil diubah');
    }
}