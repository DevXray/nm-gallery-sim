<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna      = Pengguna::orderBy('created_at', 'desc')->get();
        $totalPengguna = $pengguna->count();
        $totalOwner    = $pengguna->where('role', 'Owner')->count();
        $totalKaryawan = $pengguna->where('role', 'Karyawan')->count();

        return view('pengguna.index', compact(
            'pengguna', 'totalPengguna', 'totalOwner', 'totalKaryawan'
        ));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'username'     => 'required|string|max:100|unique:pengguna,username',
                'email'        => 'nullable|email|max:255',
                'password'     => 'required|string|min:6',
                'role'         => 'required|in:Owner,Karyawan',
                'foto'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file     = $request->file('foto');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/pengguna'), $filename);
                $fotoPath = 'uploads/pengguna/' . $filename;
            }

            $pengguna = Pengguna::create([
                'nama_lengkap' => $request->nama_lengkap,
                'username'     => $request->username,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'role'         => $request->role,
                'foto'         => $fotoPath,
            ]);

            return response()->json(['success' => true, 'data' => $pengguna]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pengguna = Pengguna::findOrFail($id);

            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'username'     => 'required|string|max:100|unique:pengguna,username,' . $id . ',id_user',
                'email'        => 'nullable|email|max:255',
                'password'     => 'nullable|string|min:6',
                'role'         => 'required|in:Owner,Karyawan',
                'foto'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $fotoPath = $pengguna->foto;

            if ($request->hasFile('foto')) {
                if ($pengguna->foto && file_exists(public_path($pengguna->foto))) {
                    unlink(public_path($pengguna->foto));
                }
                $file     = $request->file('foto');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/pengguna'), $filename);
                $fotoPath = 'uploads/pengguna/' . $filename;
            }

            if ($request->hapus_foto == '1') {
                if ($pengguna->foto && file_exists(public_path($pengguna->foto))) {
                    unlink(public_path($pengguna->foto));
                }
                $fotoPath = null;
            }

            $data = [
                'nama_lengkap' => $request->nama_lengkap,
                'username'     => $request->username,
                'email'        => $request->email,
                'role'         => $request->role,
                'foto'         => $fotoPath,
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $pengguna->update($data);
            return response()->json(['success' => true, 'data' => $pengguna->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        // Tidak boleh hapus akun yang sedang login
        if (session('user')['id_user'] == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus akun yang sedang aktif!'
            ]);
        }

        $pengguna = Pengguna::findOrFail($id);
        if ($pengguna->foto && file_exists(public_path($pengguna->foto))) {
            unlink(public_path($pengguna->foto));
        }
        $pengguna->delete();

        return response()->json(['success' => true]);
    }
}