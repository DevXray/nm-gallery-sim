<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::withCount('transaksi')
            ->withSum('transaksi', 'total_biaya')
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($pelanggan as $p) {
            $p->terakhir_sewa = Transaksi::where('id_pelanggan', $p->id_pelanggan)
                ->max('tgl_sewa');
        }
        
        $totalPelanggan = Pelanggan::count();
        $sedangMenyewa = Transaksi::where('status_transaksi', 'Diproses')->distinct('id_pelanggan')->count('id_pelanggan');
        $pelangganSetia = Pelanggan::has('transaksi', '>=', 3)->count();
        $pelangganBaruBulanIni = Pelanggan::whereMonth('created_at', date('m'))->count();
        
        return view('pelanggan.index', compact('pelanggan', 'totalPelanggan', 'sedangMenyewa', 'pelangganSetia', 'pelangganBaruBulanIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15|unique:pelanggan,no_telp',
            'alamat' => 'nullable|string'
        ]);

        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat
        ]);

        return response()->json(['success' => true, 'data' => $pelanggan]);
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15|unique:pelanggan,no_telp,' . $id . ',id_pelanggan',
            'alamat' => 'nullable|string'
        ]);

        $pelanggan->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat
        ]);

        return response()->json(['success' => true, 'data' => $pelanggan]);
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        if ($pelanggan->transaksi()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus pelanggan karena masih memiliki riwayat transaksi!');
        }
        
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }

    public function show($id)
    {
        // Redirect ke index karena tidak perlu halaman detail
        return redirect()->route('pelanggan.index');
    }

    public function exportPDF()
    {
        $pelanggan = Pelanggan::withCount('transaksi')
            ->withSum('transaksi', 'total_biaya')
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($pelanggan as $p) {
            $p->terakhir_sewa = Transaksi::where('id_pelanggan', $p->id_pelanggan)
                ->max('tgl_sewa');
        }
        
        $totalPelanggan = Pelanggan::count();
        $sedangMenyewa = Transaksi::where('status_transaksi', 'Diproses')->distinct('id_pelanggan')->count('id_pelanggan');
        $pelangganSetia = Pelanggan::has('transaksi', '>=', 3)->count();
        $totalPendapatan = Transaksi::sum('total_biaya');
        
        $data = [
            'pelanggan' => $pelanggan,
            'totalPelanggan' => $totalPelanggan,
            'sedangMenyewa' => $sedangMenyewa,
            'pelangganSetia' => $pelangganSetia,
            'totalPendapatan' => $totalPendapatan,
            'title' => 'LAPORAN DATA PELANGGAN'
        ];
        
        $pdf = Pdf::loadView('pelanggan.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan_pelanggan_' . date('Y-m-d') . '.pdf');
    }
}