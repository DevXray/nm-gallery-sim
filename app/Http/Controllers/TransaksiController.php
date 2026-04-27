<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\DraftTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // =====================================================================
    // HALAMAN FORM TRANSAKSI BARU
    // =====================================================================
    public function index()
    {
        return $this->create();
    }

    public function create()
    {
        $barangs = Barang::where('status_barang', 'Tersedia')->get();
        $pelanggans = Pelanggan::all();

        $selectedPelanggan = null;
        if (request()->has('pelanggan')) {
            $selectedPelanggan = Pelanggan::find(request()->get('pelanggan'));
        }

        $selectedBarang = null;
        if (request()->has('barang')) {
            $selectedBarang = Barang::find(request()->get('barang'));
        }

        return view('transaksi.index', compact('barangs', 'pelanggans', 'selectedPelanggan', 'selectedBarang'));
    }

    // =====================================================================
    // SIMPAN TRANSAKSI BARU (SUPPORT DP & LUNAS)
    // =====================================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan'  => 'required',
            'no_telp'         => 'required',
            'id_barang'       => 'required|exists:barang,id_barang',
            'tgl_sewa'        => 'required|date',
            'tgl_jatuh_tempo' => 'required|date|after:tgl_sewa',
            'metode_bayar'    => 'required|in:Lunas,DP',
        ]);

        DB::beginTransaction();
        try {
            // Cari atau buat pelanggan berdasarkan no_telp
            $pelanggan = Pelanggan::firstOrCreate(
                ['no_telp' => $request->no_telp],
                [
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'alamat'         => $request->alamat,
                ]
            );

            $barang = Barang::findOrFail($request->id_barang);

            // Hitung durasi dalam hari (minimal 1 hari)
            $tglSewa   = Carbon::parse($request->tgl_sewa)->startOfDay();
            $tglKembali = Carbon::parse($request->tgl_jatuh_tempo)->startOfDay();
            $durasi    = max(1, $tglSewa->diffInDays($tglKembali));

            $totalBiaya = $barang->harga_sewa * $durasi;

            // Hitung DP dan sisa tagihan
            $metodeBayar  = $request->metode_bayar;
            $jumlahDp     = 0;
            $sisaTagihan  = 0;

            if ($metodeBayar === 'DP') {
                // DP = 50% dari total biaya (atau bisa diinput manual)
                $jumlahDp    = $request->jumlah_dp ?? ($totalBiaya * 0.5);
                $sisaTagihan = $totalBiaya - $jumlahDp;
            } else {
                // Lunas = tidak ada sisa
                $jumlahDp    = $totalBiaya;
                $sisaTagihan = 0;
            }

            // Buat transaksi
            $transaksi = Transaksi::create([
                'id_pelanggan'    => $pelanggan->id_pelanggan,
                'id_user'         => session('user')['id_user'],
                'tgl_sewa'        => $request->tgl_sewa,
                'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                'total_biaya'     => $totalBiaya,
                'total_denda'     => 0,
                'status_transaksi' => 'Diproses',
                'metode_bayar'    => $metodeBayar,
                'jumlah_dp'       => $jumlahDp,
                'sisa_tagihan'    => $sisaTagihan,
            ]);

            // Ambil items dari request (JSON dari form)
            $items = json_decode($request->items, true) ?? [];

            if (!empty($items)) {
                foreach ($items as $item) {
                    DetailTransaksi::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_barang'    => $request->id_barang,
                        'ukuran'       => $item['size'] ?? null,
                        'kuantitas'    => $item['jumlah'] ?? 1,
                        'sub_total'    => ($item['harga'] ?? $barang->harga_sewa) * ($item['jumlah'] ?? 1) * $durasi,
                    ]);
                }
            } else {
                // Fallback jika tidak ada items array
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang'    => $request->id_barang,
                    'ukuran'       => null,
                    'kuantitas'    => 1,
                    'sub_total'    => $totalBiaya,
                ]);
            }

            // Update status barang menjadi Disewa
            $barang->update(['status_barang' => 'Disewa']);

            // Hapus draft jika ada yang dimuat
            if ($request->has('draft_id') && $request->draft_id) {
                DraftTransaksi::where('id_draft', $request->draft_id)->delete();
            }

            DB::commit();

            return redirect()
                ->route('transaksi.show', $transaksi->id_transaksi)
                ->with('success', 'Transaksi berhasil dibuat. Total: Rp ' . number_format($totalBiaya, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'Gagal membuat transaksi: ' . $e->getMessage()]);
        }
    }

    // =====================================================================
    // HALAMAN DETAIL TRANSAKSI + FORM PENGEMBALIAN
    // =====================================================================
    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])->findOrFail($id);

        // Hitung denda realtime dari pengaturan tarif
        $tarif = $this->getTarif();
        $dendaInfo = $this->hitungDenda($transaksi, $tarif);

        return view('transaksi.show', compact('transaksi', 'tarif', 'dendaInfo'));
    }

    // =====================================================================
    // PROSES PENGEMBALIAN BARANG
    // =====================================================================
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])->findOrFail($id);

        if ($transaksi->status_transaksi === 'Selesai') {
            return back()->withErrors(['message' => 'Transaksi ini sudah selesai.']);
        }

        DB::beginTransaction();
        try {
            $tarif    = $this->getTarif();
            $dendaInfo = $this->hitungDenda($transaksi, $tarif);

            $tglKembali    = Carbon::now();
            $totalDenda    = $dendaInfo['total_denda'];
            $sisaTagihan   = $transaksi->sisa_tagihan ?? 0;

            // Total yang harus dibayar saat pengembalian:
            // = sisa tagihan DP (jika ada) + denda keterlambatan (jika ada)
            $totalBayarKembali = $sisaTagihan + $totalDenda;

            $transaksi->update([
                'tgl_kembali'     => $tglKembali,
                'total_denda'     => $totalDenda,
                'status_transaksi' => 'Selesai',
                'sisa_tagihan'    => 0, // sudah lunas
            ]);

            // Kembalikan status barang menjadi Tersedia
            $detail = $transaksi->detailTransaksis->first();
            if ($detail && $detail->barang) {
                $detail->barang->update(['status_barang' => 'Tersedia']);
            }

            DB::commit();

            $pesan = 'Barang berhasil dikembalikan.';
            if ($totalDenda > 0) {
                $pesan .= ' Denda: Rp ' . number_format($totalDenda, 0, ',', '.');
            }
            if ($sisaTagihan > 0) {
                $pesan .= ' Sisa DP dilunasi: Rp ' . number_format($sisaTagihan, 0, ',', '.');
            }

            // Kirim WhatsApp jika pengaturan aktif
            $this->sendWhatsAppNotification($transaksi->fresh()->load('pelanggan'), $totalDenda, $sisaTagihan);

            return redirect()
                ->route('transaksi.show', $transaksi->id_transaksi)
                ->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'Gagal memproses pengembalian: ' . $e->getMessage()]);
        }
    }

    // =====================================================================
    // CETAK PDF — E-NOTA SEWA (dipanggil saat transaksi baru)
    // =====================================================================
    public function printPdf($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.pdf_sewa', [
            'transaksi' => $transaksi,
            'title'     => 'E-NOTA PENYEWAAN',
        ]);
        $pdf->setPaper('a6', 'portrait');
        return $pdf->stream('e-nota-sewa-' . $transaksi->id_transaksi . '.pdf');
    }

    // =====================================================================
    // CETAK PDF — E-NOTA PENGEMBALIAN (dipanggil setelah return selesai)
    // =====================================================================
    public function printReturnPdf($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksis.barang'])->findOrFail($id);

        if ($transaksi->status_transaksi !== 'Selesai') {
            abort(403, 'Pengembalian belum diproses.');
        }

        $tarif    = $this->getTarif();
        $dendaInfo = $this->hitungDenda($transaksi, $tarif);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.pdf_kembali', [
            'transaksi' => $transaksi,
            'dendaInfo' => $dendaInfo,
            'title'     => 'E-NOTA PENGEMBALIAN',
        ]);
        $pdf->setPaper('a6', 'portrait');
        return $pdf->stream('e-nota-pengembalian-' . $transaksi->id_transaksi . '.pdf');
    }

    // =====================================================================
    // PREVIEW PDF SEBELUM SIMPAN (dari form baru)
    // =====================================================================
    public function previewPdf(Request $request)
    {
        $barang = Barang::find($request->id_barang);

        $tglSewa  = Carbon::parse($request->tgl_sewa);
        $tglJatuh = Carbon::parse($request->tgl_jatuh_tempo);
        $durasi   = max(1, $tglSewa->diffInDays($tglJatuh));

        $items      = json_decode($request->items ?? '[]', true);
        $totalBiaya = 0;
        $detailItems = [];

        foreach ($items as $item) {
            $subtotal    = ($item['harga'] ?? 0) * ($item['jumlah'] ?? 1) * $durasi;
            $totalBiaya += $subtotal;
            $detailItems[] = [
                'nama'     => $barang->nama_barang ?? '-',
                'ukuran'   => $item['size'] ?? '-',
                'jumlah'   => $item['jumlah'] ?? 1,
                'harga'    => $item['harga'] ?? 0,
                'durasi'   => $durasi,
                'subtotal' => $subtotal,
            ];
        }

        $metodeBayar = $request->metode_bayar ?? 'Lunas';
        $jumlahDp    = $request->jumlah_dp ?? ($metodeBayar === 'DP' ? $totalBiaya * 0.5 : $totalBiaya);
        $sisaTagihan = $metodeBayar === 'DP' ? ($totalBiaya - $jumlahDp) : 0;

        $data = [
            'title'        => 'E-NOTA PENYEWAAN (PREVIEW)',
            'isPreview'    => true,
            'pelanggan'    => (object) [
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_telp'        => $request->no_telp,
                'alamat'         => $request->alamat,
            ],
            'barang'       => $barang,
            'tgl_sewa'     => $tglSewa,
            'tgl_jatuh'    => $tglJatuh,
            'durasi'       => $durasi,
            'total_biaya'  => $totalBiaya,
            'metode_bayar' => $metodeBayar,
            'jumlah_dp'    => $jumlahDp,
            'sisa_tagihan' => $sisaTagihan,
            'detailItems'  => $detailItems,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.pdf_sewa_preview', $data);
        $pdf->setPaper('a6', 'portrait');
        return $pdf->download('e-nota-preview.pdf');
    }

    // =====================================================================
    // SIMPAN DRAFT TRANSAKSI
    // =====================================================================
    public function saveDraft(Request $request)
    {
        $request->validate([
            'id_barang'       => 'required|exists:barang,id_barang',
            'nama_pelanggan'  => 'required',
            'no_telp'         => 'required',
        ]);

        try {
            $barang = Barang::findOrFail($request->id_barang);

            $tglSewa  = $request->tgl_sewa  ? Carbon::parse($request->tgl_sewa)  : null;
            $tglJatuh = $request->tgl_jatuh ? Carbon::parse($request->tgl_jatuh) : null;
            $durasi   = ($tglSewa && $tglJatuh) ? max(1, $tglSewa->diffInDays($tglJatuh)) : 0;

            $items      = json_decode($request->items ?? '[]', true);
            $totalBiaya = 0;
            foreach ($items as $item) {
                $totalBiaya += ($item['harga'] ?? $barang->harga_sewa) * ($item['jumlah'] ?? 1) * $durasi;
            }

            $metodeBayar = $request->metode_bayar ?? 'Lunas';
            $jumlahDp    = $metodeBayar === 'DP' ? ($request->jumlah_dp ?? $totalBiaya * 0.5) : $totalBiaya;

            $draft = DraftTransaksi::create([
                'id_user'         => session('user')['id_user'],
                'nama_pelanggan'  => $request->nama_pelanggan,
                'no_telp'         => $request->no_telp,
                'alamat'          => $request->alamat,
                'id_barang'       => $request->id_barang,
                'ukuran_dipilih'  => $request->items,
                'tgl_sewa'        => $tglSewa,
                'tgl_jatuh_tempo' => $tglJatuh,
                'total_biaya'     => $totalBiaya,
                'metode_bayar'    => $metodeBayar,
                'jumlah_dp'       => $jumlahDp,
                'catatan'         => $request->catatan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil disimpan.',
                'draft_id' => $draft->id_draft,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // AMBIL SEMUA DRAFT MILIK USER (untuk popup daftar draft)
    // =====================================================================
    public function getDrafts()
    {
        $drafts = DraftTransaksi::with('barang')
            ->where('id_user', session('user')['id_user'])
            ->latest()
            ->get()
            ->map(function ($d) {
                return [
                    'id_draft'        => $d->id_draft,
                    'nama_pelanggan'  => $d->nama_pelanggan,
                    'no_telp'         => $d->no_telp,
                    'barang'          => $d->barang->nama_barang ?? '-',
                    'total_biaya'     => $d->total_biaya,
                    'metode_bayar'    => $d->metode_bayar,
                    'tgl_sewa'        => $d->tgl_sewa ? $d->tgl_sewa->format('d/m/Y H:i') : '-',
                    'tgl_jatuh'       => $d->tgl_jatuh_tempo ? $d->tgl_jatuh_tempo->format('d/m/Y H:i') : '-',
                    'id_barang'       => $d->id_barang,
                    'items'           => $d->ukuran_dipilih,
                    'alamat'          => $d->alamat,
                    'created_at'      => $d->created_at->format('d/m/Y H:i'),
                    'catatan'         => $d->catatan,
                ];
            });

        return response()->json(['success' => true, 'drafts' => $drafts]);
    }

    // =====================================================================
    // HAPUS DRAFT
    // =====================================================================
    public function deleteDraft($id)
    {
        $draft = DraftTransaksi::where('id_draft', $id)
            ->where('id_user', session('user')['id_user'])
            ->firstOrFail();

        $draft->delete();

        return response()->json(['success' => true]);
    }

    // =====================================================================
    // HAPUS TRANSAKSI
    // =====================================================================
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Kembalikan status barang jika masih Diproses
        if ($transaksi->status_transaksi === 'Diproses') {
            $detail = $transaksi->detailTransaksis()->with('barang')->first();
            if ($detail && $detail->barang) {
                $detail->barang->update(['status_barang' => 'Tersedia']);
            }
        }

        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function edit($id)
    {
        abort(404);
    }

    // =====================================================================
    // HELPER: Baca tarif dari storage/app/tarif.json
    // =====================================================================
    private function getTarif(): array
    {
        $tarifFile = storage_path('app/tarif.json');
        if (file_exists($tarifFile)) {
            return json_decode(file_get_contents($tarifFile), true) ?? [];
        }
        return [
            'tarif_dasar'  => 150000,
            'tarif_fullset' => 650000,
            'jaminan'      => 200000,
            'denda'        => 50000,
        ];
    }

    // =====================================================================
    // HELPER: Hitung denda berdasarkan tarif pengaturan
    // =====================================================================
    private function hitungDenda(Transaksi $transaksi, array $tarif): array
    {
        $now             = Carbon::now()->startOfDay();
        $jatuhTempo      = Carbon::parse($transaksi->tgl_jatuh_tempo)->startOfDay();
        $terlambat       = $now->gt($jatuhTempo);
        $hariTelat       = $terlambat ? $jatuhTempo->diffInDays($now) : 0;
        $dendaPerHari    = $tarif['denda'] ?? 50000;
        $totalDenda      = $hariTelat * $dendaPerHari;

        // Jika sudah dikembalikan, hitung dari tgl_kembali actual
        if ($transaksi->tgl_kembali) {
            $tglKembali  = Carbon::parse($transaksi->tgl_kembali)->startOfDay();
            $terlambat   = $tglKembali->gt($jatuhTempo);
            $hariTelat   = $terlambat ? $jatuhTempo->diffInDays($tglKembali) : 0;
            $totalDenda  = $hariTelat * $dendaPerHari;
        }

        return [
            'terlambat'     => $terlambat,
            'hari_telat'    => $hariTelat,
            'denda_per_hari' => $dendaPerHari,
            'total_denda'   => $totalDenda,
        ];
    }

    // =====================================================================
    // HELPER: Kirim notifikasi WhatsApp jika pengaturan aktif
    // =====================================================================
    private function sendWhatsAppNotification(Transaksi $transaksi, float $denda, float $sisa): void
    {
        // Cek apakah fitur WA aktif di pengaturan
        // Untuk saat ini menggunakan flag file sederhana
        // Nanti bisa diganti dengan database setting
        $settingFile = storage_path('app/wa_setting.json');
        if (!file_exists($settingFile)) return;

        $setting = json_decode(file_get_contents($settingFile), true);
        if (empty($setting['kirim_enota_otomatis'])) return;

        // Implementasi via Fonnte API (uncomment jika API key tersedia)
        // $apiKey = env('FONNTE_API_KEY');
        // $noTelp = $transaksi->pelanggan->no_telp;
        // $pesan  = "Terima kasih *{$transaksi->pelanggan->nama_pelanggan}*, ...";
        // Http::withToken($apiKey)->post('https://api.fonnte.com/send', [...]);

        // Untuk sekarang: simpan flag agar view bisa tampilkan tombol WA
        session(['wa_send_trx_id' => $transaksi->id_transaksi]);
    }
}