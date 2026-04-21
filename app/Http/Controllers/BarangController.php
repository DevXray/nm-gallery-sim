<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

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

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_sewa' => 'required|numeric',
            'status_barang' => 'required'
        ]);

        Barang::create($request->all());
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_sewa' => 'required|numeric',
            'status_barang' => 'required'
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->all());
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }
}