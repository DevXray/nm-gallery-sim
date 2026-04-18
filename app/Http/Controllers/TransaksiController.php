<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // Halaman FORM transaksi (index)
    public function index()
    {
        $barangs = Barang::where('status_barang', 'Tersedia')->get();
        $pelanggans = Pelanggan::all();
        return view('transaksi.index', compact('barangs', 'pelanggans'));
    }

    // Simpan transaksi
    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required',
            'id_barang' => 'required',
            'tgl_sewa' => 'required|date',
            'tgl_jatuh_tempo' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($request->id_barang);
            
            // Hitung durasi
            $tglSewa = new \DateTime($request->tgl_sewa);
            $tglKembali = new \DateTime($request->tgl_jatuh_tempo);
            $durasi = $tglSewa->diff($tglKembali)->days;
            if ($durasi == 0) $durasi = 1;
            
            $total_biaya = $barang->harga_sewa * $durasi;

            // Buat transaksi
            $transaksi = Transaksi::create([
                'id_pelanggan' => $request->id_pelanggan,
                'id_user' => session('user')['id_user'],
                'tgl_sewa' => $request->tgl_sewa,
                'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                'total_biaya' => $total_biaya,
                'total_denda' => 0,
                'status_transaksi' => 'Diproses',
            ]);

            // Buat detail transaksi
            DetailTransaksi::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'id_barang' => $request->id_barang,
                'kuantitas' => 1,
                'sub_total' => $total_biaya,
            ]);

            // Update status barang
            $barang->update(['status_barang' => 'Disewa']);

            DB::commit();
            return redirect()->route('transaksi.show', $transaksi->id_transaksi)->with('success', 'Transaksi berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['message' => 'Gagal membuat transaksi: ' . $e->getMessage()]);
        }
    }

    // Halaman DETAIL transaksi (show)
    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])->findOrFail($id);
        return view('transaksi.show', compact('transaksi'));
    }

    // Proses pengembalian barang
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $tgl_kembali = now();
            $tgl_jatuh_tempo = $transaksi->tgl_jatuh_tempo;
            
            // Hitung denda jika terlambat
            $denda = 0;
            if ($tgl_kembali > $tgl_jatuh_tempo) {
                $hari_telat = $tgl_kembali->diffInDays($tgl_jatuh_tempo);
                $denda = $hari_telat * 50000;
            }
            
            $transaksi->update([
                'tgl_kembali' => $tgl_kembali,
                'total_denda' => $denda,
                'status_transaksi' => 'Selesai',
            ]);
            
            // Update status barang
            $detail = $transaksi->detailTransaksis->first();
            if ($detail) {
                $detail->barang->update(['status_barang' => 'Tersedia']);
            }
            
            DB::commit();
            return redirect()->route('transaksi.show', $transaksi->id_transaksi)->with('success', 'Barang berhasil dikembalikan. Denda: Rp ' . number_format($denda, 0, ',', '.'));
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['message' => 'Gagal memproses pengembalian']);
        }
    }

    // Hapus transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi dihapus');
    }

    // Method yang tidak dipakai (resource requirement)
    public function create()
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }
}