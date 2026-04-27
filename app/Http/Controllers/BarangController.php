<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        $totalBarang = Barang::count();
        $barangTersedia = Barang::where('status_barang', 'Tersedia')->count();
        $barangDisewa = Barang::where('status_barang', 'Disewa')->count();
        $barangLaundry = Barang::where('status_barang', 'Laundry')->count();
        $barangRusak = Barang::where('status_barang', 'Rusak')->count();
        
        return view('barang.index', compact('barang', 'totalBarang', 'barangTersedia', 'barangDisewa', 'barangLaundry', 'barangRusak'));
    }

   public function store(Request $request)
{
    try {
        $request->validate([
            'nama_barang' => 'required',
            'harga_sewa' => 'required|numeric',
            'ukuran' => 'required',
            'stok' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/barang'), $filename);
            $fotoPath = 'uploads/barang/' . $filename;
        }

        Barang::create([
            'nama_barang' => $request->nama_barang,
            'ukuran' => $request->ukuran,
            'harga_sewa' => $request->harga_sewa,
            'stok' => $request->stok,
            'status_barang' => 'Tersedia',
            'foto' => $fotoPath,
        ]);

        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function update(Request $request, $id)
{
    try {
        $request->validate([
            'nama_barang' => 'required',
            'harga_sewa' => 'required|numeric',
            'ukuran' => 'required',
            'stok' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $barang = Barang::findOrFail($id);
        
        // Upload foto baru
        $fotoPath = $barang->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($barang->foto && file_exists(public_path($barang->foto))) {
                unlink(public_path($barang->foto));
            }
            $file = $request->file('foto');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/barang'), $filename);
            $fotoPath = 'uploads/barang/' . $filename;
        }
        
        // Hapus foto jika dicentang
        if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
            if ($barang->foto && file_exists(public_path($barang->foto))) {
                unlink(public_path($barang->foto));
            }
            $fotoPath = null;
        }

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'ukuran' => $request->ukuran,
            'harga_sewa' => $request->harga_sewa,
            'stok' => $request->stok,
            'foto' => $fotoPath,
        ]);

        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        if ($barang->status_barang === 'Disewa') {
        return response()->json(['success' => false, 'message' => 'Barang sedang disewa, tidak bisa dihapus!']);
        }
        if ($barang->foto && file_exists(public_path($barang->foto))) {
            unlink(public_path($barang->foto));
        }

        $barang->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }
}