<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = Pengguna::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session(['user' => $user]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['message' => 'Username atau password salah!']);
    }

    public function dashboard()
    {
        $user = session('user');
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Data untuk dashboard
        $totalBarang = \App\Models\Barang::count();
        $totalPelanggan = \App\Models\Pelanggan::count();
        $totalTransaksi = \App\Models\Transaksi::count();
        $barangTersedia = \App\Models\Barang::where('status_barang', 'Tersedia')->count();

        return view('dashboard', compact('user', 'totalBarang', 'totalPelanggan', 'totalTransaksi', 'barangTersedia'));
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('login');
    }
}