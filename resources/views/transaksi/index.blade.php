@extends('layouts.app')

@section('title', 'Transaksi & E-Nota')
@section('breadcrumb', 'Transaksi & E-Nota')

@section('content')

@php
    $today = \Carbon\Carbon::now()->translatedFormat('l, d F Y');

    // Data barang untuk JavaScript
    $posBarangData = $barangs->map(function($b) {
        $stok = [];
        if ($b->stok) {
            $decoded = json_decode($b->stok, true);
            if (is_array($decoded)) $stok = $decoded;
        }
        return [
            'id'     => $b->id_barang,
            'nama'   => $b->nama_barang,
            'stok'   => $stok,
            'harga'  => (float) $b->harga_sewa,
            'status' => $b->status_barang,
            'foto'   => $b->foto,
            'ukuran' => $b->ukuran ?? '',
        ];
    });

    // Data transaksi aktif untuk tab Pengembalian
    $transaksiData = $transaksiAktif->map(function($t) use ($dendaPerHari) {
        $jatuhTempo  = \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->startOfDay();
        $nowDay      = \Carbon\Carbon::now()->startOfDay();
        $terlambat   = $nowDay->gt($jatuhTempo);
        $hariTelat   = $terlambat ? $jatuhTempo->diffInDays($nowDay) : 0;
        $totalDenda  = $hariTelat * $dendaPerHari;
        $sisaTagihan = $t->sisa_tagihan ?? 0;
        $detail      = $t->detailTransaksis->first();
        return [
            'id'                  => $t->id_transaksi,
            'no_trx'              => '#TRX-' . str_pad($t->id_transaksi, 4, '0', STR_PAD_LEFT),
            'pelanggan'           => $t->pelanggan->nama_pelanggan ?? '-',
            'no_telp'             => $t->pelanggan->no_telp ?? '-',
            'barang'              => $detail->barang->nama_barang ?? '-',
            'ukuran'              => $detail->ukuran ?? '-',
            'tgl_sewa'            => \Carbon\Carbon::parse($t->tgl_sewa)->format('d/m/Y'),
            'tgl_jatuh'           => \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->format('d/m/Y'),
            'durasi'              => \Carbon\Carbon::parse($t->tgl_sewa)->diffInDays($t->tgl_jatuh_tempo),
            'total_biaya'         => (float) $t->total_biaya,
            'metode_bayar'        => $t->metode_bayar ?? 'Lunas',
            'jumlah_dp'           => (float) ($t->jumlah_dp ?? 0),
            'sisa_tagihan'        => (float) $sisaTagihan,
            'terlambat'           => $terlambat,
            'hari_telat'          => $hariTelat,
            'denda_per_hari'      => (float) $dendaPerHari,
            'total_denda'         => (float) $totalDenda,
            'total_bayar_kembali' => (float) ($sisaTagihan + $totalDenda),
        ];
    });

    $jumlahTerlambat = $transaksiAktif->filter(function($t) {
        return \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($t->tgl_jatuh_tempo)->startOfDay());
    })->count();
@endphp

<style>
:root {
  --pos-black:#0a0a0a; --pos-black3:#1e1e1e;
  --pos-gold:#C9A84C;  --pos-gold-lt:#E8C96A; --pos-gold-dk:#A07830;
  --pos-gold-xs:rgba(201,168,76,.10); --pos-gold-md:rgba(201,168,76,.25);
  --pos-surface:#f8f7f4;
  --pos-border:rgba(0,0,0,.09); --pos-border2:rgba(0,0,0,.15);
  --pos-muted:#6b6b6b; --pos-hint:#bbb;
  --pos-red:#e03434; --pos-green:#1a8050;
  --pos-r:8px; --pos-r2:12px; --pos-r3:16px;
}

/* halaman mengisi penuh area .content */
.pos-page {
  display:flex; flex-direction:column;
  height:calc(100vh - 52px); /* 52px = tinggi topbar layout */
  overflow:hidden;
  margin:-24px; /* hapus padding default .content */
}

/* ── TAB BAR ── */
.pos-tabbar {
  background:#fff; border-bottom:1px solid var(--gray-200);
  display:flex; align-items:center; padding:0 20px; gap:4px;
  flex-shrink:0; height:44px;
}
.pos-tab {
  padding:0 18px; height:100%; display:flex; align-items:center; gap:7px;
  font-size:12.5px; font-weight:500; color:var(--gray-500);
  cursor:pointer; border-bottom:2px solid transparent; transition:.15s;
}
.pos-tab:hover { color:var(--pos-gold-dk); }
.pos-tab.active { color:#0a0a0a; font-weight:600; border-bottom-color:var(--pos-gold); }
.pos-tab-badge {
  background:var(--pos-gold); color:var(--pos-black); font-size:9.5px;
  font-weight:700; min-width:18px; height:18px; border-radius:9px;
  display:flex; align-items:center; justify-content:center; padding:0 4px;
}
.pos-tab-badge.red { background:var(--pos-red); color:#fff; }

/* ── TAB CONTENT ── */
.pos-tab-content { display:none; flex:1; overflow:hidden; }
.pos-tab-content.active { display:flex; }

/* ══════════════════════════
   TAB 1 — POS KASIR
══════════════════════════ */
.pos-split { display:flex; flex:1; overflow:hidden; }

/* KATALOG */
.pos-katalog { flex:1; display:flex; flex-direction:column; overflow:hidden; background:var(--pos-surface); }
.pos-kat-top { background:#fff; border-bottom:1px solid var(--pos-border); padding:11px 16px; display:flex; flex-direction:column; gap:9px; flex-shrink:0; }
.pos-search { display:flex; align-items:center; gap:8px; background:var(--pos-surface); border:1.5px solid var(--pos-border2); border-radius:var(--pos-r2); padding:0 12px; transition:.2s; }
.pos-search:focus-within { border-color:var(--pos-gold); background:#fff; }
.pos-search input { flex:1; border:none; background:transparent; outline:none; padding:8px 0; font-size:13px; color:#0a0a0a; font-family:inherit; }
.pos-search input::placeholder { color:var(--pos-hint); }
.pos-chips { display:flex; gap:6px; overflow-x:auto; }
.pos-chips::-webkit-scrollbar { height:0; }
.pos-chip { padding:4px 13px; border-radius:20px; font-size:11.5px; font-weight:500; border:1px solid var(--pos-border2); background:#fff; color:var(--pos-muted); cursor:pointer; white-space:nowrap; transition:.12s; flex-shrink:0; }
.pos-chip:hover:not(.active) { border-color:var(--pos-gold-dk); color:var(--pos-gold-dk); }
.pos-chip.active { background:var(--pos-black); border-color:var(--pos-black); color:var(--pos-gold-lt); }
.pos-grid { flex:1; overflow-y:auto; padding:12px 14px; display:grid; grid-template-columns:repeat(auto-fill, minmax(148px,1fr)); gap:10px; align-content:start; }
.pos-grid::-webkit-scrollbar { width:4px; }
.pos-grid::-webkit-scrollbar-thumb { background:var(--pos-border2); border-radius:2px; }

/* ITEM CARD */
.pos-card { background:#fff; border:1.5px solid var(--pos-border); border-radius:var(--pos-r2); overflow:hidden; cursor:pointer; transition:.18s; position:relative; }
.pos-card:hover { border-color:var(--pos-gold); box-shadow:0 3px 14px rgba(201,168,76,.14); transform:translateY(-2px); }
.pos-card.disewa { opacity:.52; cursor:not-allowed; }
.pos-card.disewa:hover { transform:none; box-shadow:none; border-color:var(--pos-border); }
.pos-card-img { height:96px; display:flex; align-items:center; justify-content:center; font-size:38px; background:linear-gradient(135deg,#faf5e8,#f5edd6); position:relative; overflow:hidden; }
.pos-card-img img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.pos-badge { position:absolute; top:6px; right:6px; font-size:9px; font-weight:700; padding:2px 7px; border-radius:10px; letter-spacing:.3px; z-index:1; }
.pos-badge.ok  { background:rgba(26,128,80,.1); color:#1a8050; border:1px solid rgba(26,128,80,.2); }
.pos-badge.out { background:rgba(220,52,52,.08); color:#c0392b; border:1px solid rgba(220,52,52,.18); }
.pos-card-body { padding:9px 11px; }
.pos-card-name  { font-size:11.5px; font-weight:600; color:#0a0a0a; line-height:1.3; margin-bottom:3px; }
.pos-card-meta  { font-size:10px; color:var(--pos-hint); margin-bottom:6px; }
.pos-card-price { font-size:13px; font-weight:700; color:var(--pos-gold-dk); }
.pos-card-price small { font-size:9.5px; font-weight:400; color:var(--pos-hint); }

/* CART */
.pos-cart { width:278px; background:#fff; border-left:1px solid var(--pos-border); display:flex; flex-direction:column; flex-shrink:0; }
.pos-cart-head { background:var(--pos-black); padding:12px 15px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.pos-cart-title { font-size:13px; font-weight:600; color:var(--pos-gold-lt); display:flex; align-items:center; gap:8px; }
.pos-cart-count { background:var(--pos-gold); color:var(--pos-black); font-size:10px; font-weight:700; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
.pos-cart-body { flex:1; overflow-y:auto; }
.pos-cart-body::-webkit-scrollbar { width:3px; }
.pos-cart-body::-webkit-scrollbar-thumb { background:var(--pos-border2); }
.pos-cart-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:10px; color:var(--pos-hint); padding:24px; text-align:center; }
.pos-cart-empty-ico { font-size:32px; opacity:.35; }
.pos-cart-empty-txt { font-size:11.5px; line-height:1.6; }
.pos-cart-item { padding:10px 13px; border-bottom:1px solid var(--pos-border); position:relative; }
.pos-item-name  { font-size:11.5px; font-weight:600; color:#0a0a0a; margin-bottom:2px; padding-right:20px; }
.pos-item-sub   { font-size:10px; color:var(--pos-muted); margin-bottom:7px; }
.pos-item-row   { display:flex; align-items:center; justify-content:space-between; }
.pos-qc { display:flex; align-items:center; gap:6px; }
.pos-qb { width:22px; height:22px; border-radius:6px; border:1px solid var(--pos-border2); background:var(--pos-surface); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; color:#0a0a0a; transition:.12s; line-height:1; }
.pos-qb:hover { border-color:var(--pos-gold); color:var(--pos-gold-dk); background:var(--pos-gold-xs); }
.pos-qn { font-size:12px; font-weight:600; min-width:18px; text-align:center; }
.pos-item-price { font-size:12px; font-weight:700; color:var(--pos-gold-dk); }
.pos-del { position:absolute; top:9px; right:11px; border:none; background:transparent; cursor:pointer; color:var(--pos-hint); font-size:13px; padding:2px; border-radius:3px; transition:.12s; line-height:1; }
.pos-del:hover { background:rgba(220,52,52,.08); color:var(--pos-red); }
.pos-cart-foot { padding:13px 15px; border-top:1px solid var(--pos-border); flex-shrink:0; }
.pos-sub-row   { display:flex; justify-content:space-between; font-size:11.5px; color:var(--pos-muted); padding:3px 0; }
.pos-total-row { display:flex; justify-content:space-between; align-items:baseline; padding:9px 0 12px; border-top:1px solid var(--pos-border); margin-top:5px; }
.pos-total-lbl { font-size:13.5px; font-weight:600; }
.pos-total-val { font-size:18px; font-weight:700; color:var(--pos-gold-dk); font-family:'JetBrains Mono',monospace; }
.pos-btn-pay { width:100%; padding:11px; background:var(--pos-black); border:1.5px solid rgba(201,168,76,.4); border-radius:var(--pos-r2); color:var(--pos-gold-lt); font-size:13px; font-weight:600; cursor:pointer; transition:.18s; font-family:inherit; }
.pos-btn-pay:hover { background:var(--pos-black3); box-shadow:0 4px 16px rgba(201,168,76,.2); }
.pos-btn-pay:disabled { opacity:.38; cursor:not-allowed; box-shadow:none; }
.pos-btn-reset { width:100%; margin-top:7px; padding:7px; background:transparent; border:1px solid var(--pos-border2); border-radius:var(--pos-r2); color:var(--pos-muted); font-size:11.5px; cursor:pointer; transition:.12s; font-family:inherit; }
.pos-btn-reset:hover { border-color:var(--pos-red); color:var(--pos-red); }

/* ══════════════════════════
   TAB 2 — PENGEMBALIAN
══════════════════════════ */
.kembali-wrap { flex:1; overflow-y:auto; padding:20px; background:var(--pos-surface); }
.kembali-wrap::-webkit-scrollbar { width:5px; }
.kembali-wrap::-webkit-scrollbar-thumb { background:var(--gray-300); border-radius:3px; }
.kembali-header { margin-bottom:16px; }
.kembali-title    { font-size:16px; font-weight:700; color:#0a0a0a; }
.kembali-subtitle { font-size:12px; color:var(--pos-muted); margin-top:3px; }
.kembali-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px,1fr)); gap:14px; }
.trx-card { background:#fff; border:1.5px solid var(--pos-border); border-radius:var(--pos-r2); overflow:hidden; transition:.18s; }
.trx-card:hover { border-color:var(--pos-gold); box-shadow:0 3px 12px rgba(201,168,76,.12); }
.trx-card.terlambat { border-color:rgba(224,52,52,.3); }
.trx-card-head { padding:11px 14px; background:var(--pos-surface); border-bottom:1px solid var(--pos-border); display:flex; align-items:center; justify-content:space-between; }
.trx-no { font-size:11px; font-weight:700; color:var(--pos-gold-dk); font-family:'JetBrains Mono',monospace; }
.trx-status-ok   { font-size:10px; font-weight:600; padding:2px 9px; border-radius:10px; background:rgba(26,128,80,.1); color:#1a8050; border:1px solid rgba(26,128,80,.2); }
.trx-status-late { font-size:10px; font-weight:600; padding:2px 9px; border-radius:10px; background:rgba(224,52,52,.1); color:#c0392b; border:1px solid rgba(224,52,52,.2); }
.trx-card-body { padding:13px 14px; }
.trx-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px; }
.trx-field-k { font-size:9.5px; text-transform:uppercase; color:var(--pos-hint); font-weight:600; letter-spacing:.6px; margin-bottom:2px; }
.trx-field-v { font-size:12.5px; font-weight:500; color:#0a0a0a; }
.trx-biaya-box { background:var(--pos-surface); border-radius:var(--pos-r); padding:10px 12px; margin-bottom:12px; }
.trx-biaya-row   { display:flex; justify-content:space-between; font-size:11.5px; color:var(--pos-muted); padding:2px 0; }
.trx-biaya-total { display:flex; justify-content:space-between; padding-top:8px; margin-top:6px; border-top:1px solid var(--pos-border2); }
.trx-biaya-total-lbl { font-size:13px; font-weight:600; }
.trx-biaya-total-val { font-size:15px; font-weight:700; color:var(--pos-gold-dk); font-family:'JetBrains Mono',monospace; }
.trx-biaya-total-val.danger { color:var(--pos-red); }
.btn-kembalikan { width:100%; padding:10px; background:var(--pos-black); border:1.5px solid rgba(201,168,76,.4); border-radius:var(--pos-r2); color:var(--pos-gold-lt); font-size:12.5px; font-weight:600; cursor:pointer; transition:.18s; font-family:inherit; }
.btn-kembalikan:hover { background:var(--pos-black3); box-shadow:0 3px 12px rgba(201,168,76,.2); }
.btn-kembalikan.danger { background:#b71c1c; border-color:rgba(220,52,52,.4); }
.btn-kembalikan.danger:hover { background:#c62828; box-shadow:0 3px 12px rgba(220,52,52,.2); }
.kembali-empty { text-align:center; padding:60px 20px; color:var(--pos-hint); }
.kembali-empty-ico { font-size:40px; opacity:.3; margin-bottom:12px; }

/* ══════════════════════════
   OVERLAY
══════════════════════════ */
.pos-ov { position:fixed; inset:0; background:rgba(0,0,0,.62); display:flex; align-items:center; justify-content:center; z-index:500; opacity:0; pointer-events:none; transition:opacity .2s; padding:16px; }
.pos-ov.show { opacity:1; pointer-events:all; }

/* SIZE PICKER */
.pos-spick { background:#fff; border-radius:var(--pos-r3); width:340px; overflow:hidden; transform:scale(.94) translateY(8px); transition:.22s cubic-bezier(.34,1.4,.64,1); }
.pos-ov.show .pos-spick { transform:scale(1) translateY(0); }
.sp-head { background:var(--pos-black); padding:14px 18px; display:flex; justify-content:space-between; align-items:flex-start; }
.sp-name  { font-size:14px; font-weight:600; color:var(--pos-gold-lt); margin-bottom:2px; }
.sp-price { font-size:11px; color:rgba(255,255,255,.38); }
.sp-close { background:none; border:none; color:rgba(255,255,255,.38); cursor:pointer; font-size:18px; padding:2px; line-height:1; transition:.12s; }
.sp-close:hover { color:var(--pos-gold-lt); }
.sp-body { padding:16px; }
.sp-lbl  { font-size:10px; font-weight:700; color:var(--pos-muted); text-transform:uppercase; letter-spacing:.9px; margin-bottom:9px; }
.sz-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:14px; }
.sz-opt  { border:1.5px solid var(--pos-border2); border-radius:var(--pos-r); background:var(--pos-surface); cursor:pointer; padding:9px 4px; text-align:center; transition:.15s; }
.sz-opt:hover:not(.sz-dis):not(.sz-active) { border-color:var(--pos-gold-dk); }
.sz-opt.sz-active { border-color:var(--pos-gold); background:var(--pos-gold-xs); }
.sz-opt.sz-dis { opacity:.32; cursor:not-allowed; }
.sz-lbl  { font-size:12px; font-weight:600; }
.sz-stok { font-size:9.5px; color:var(--pos-muted); margin-top:2px; }
.qty-row  { display:flex; align-items:center; justify-content:space-between; background:var(--pos-surface); border-radius:var(--pos-r); padding:10px 13px; margin-bottom:13px; }
.qty-lbl  { font-size:12px; color:var(--pos-muted); }
.qty-ctrl { display:flex; align-items:center; gap:10px; }
.qty-btn  { width:26px; height:26px; border-radius:7px; border:1.5px solid var(--pos-border2); background:#fff; cursor:pointer; font-size:15px; display:flex; align-items:center; justify-content:center; transition:.12s; line-height:1; font-family:inherit; }
.qty-btn:hover { border-color:var(--pos-gold); color:var(--pos-gold-dk); }
.qty-n { font-size:14px; font-weight:700; min-width:22px; text-align:center; }
.btn-add-cart { width:100%; padding:11px; background:var(--pos-black); border:1.5px solid rgba(201,168,76,.35); border-radius:var(--pos-r2); color:var(--pos-gold-lt); font-size:13px; font-weight:600; cursor:pointer; transition:.15s; font-family:inherit; }
.btn-add-cart:hover { background:var(--pos-black3); }
.btn-add-cart:disabled { opacity:.38; cursor:not-allowed; }

/* PAYMENT MODAL — 2 kolom */
.pay-modal-wrap { background:#fff; border-radius:var(--pos-r3); width:860px; max-width:95vw; max-height:92vh; display:flex; overflow:hidden; transform:scale(.94) translateY(8px); transition:.22s cubic-bezier(.34,1.4,.64,1); }
.pos-ov.show .pay-modal-wrap { transform:scale(1) translateY(0); }

.pay-form-col { width:420px; flex-shrink:0; display:flex; flex-direction:column; border-right:1px solid var(--pos-border); }
.pay-form-head { background:var(--pos-black); padding:14px 18px; flex-shrink:0; }
.pay-form-title { font-size:14px; font-weight:600; color:var(--pos-gold-lt); }
.pay-form-sub   { font-size:11px; color:rgba(255,255,255,.32); margin-top:2px; }
.pay-form-body  { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:15px; }
.pay-form-body::-webkit-scrollbar { width:3px; }
.pay-form-body::-webkit-scrollbar-thumb { background:var(--pos-border2); }
.pm-sec-title { font-size:10px; font-weight:700; color:var(--pos-muted); text-transform:uppercase; letter-spacing:.9px; padding-bottom:7px; border-bottom:1px solid var(--pos-border); margin-bottom:4px; }
.pm-field { display:flex; flex-direction:column; gap:4px; }
.pm-label { font-size:11px; font-weight:600; color:var(--pos-muted); }
.pm-input { width:100%; padding:8px 11px; border:1.5px solid var(--pos-border2); border-radius:var(--pos-r); font-size:13px; background:var(--pos-surface); color:#0a0a0a; outline:none; transition:.18s; font-family:inherit; }
.pm-input:focus { border-color:var(--pos-gold); background:#fff; }
.pm-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.metode-wrap { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.met-opt { padding:10px 12px; border:1.5px solid var(--pos-border2); border-radius:var(--pos-r); cursor:pointer; text-align:center; transition:.15s; }
.met-opt.active { border-color:var(--pos-gold); background:var(--pos-gold-xs); }
.met-lbl  { font-size:12px; font-weight:600; }
.met-desc { font-size:10px; color:var(--pos-muted); margin-top:2px; }
.dp-box   { padding:11px; background:var(--pos-surface); border-radius:var(--pos-r); border:1px solid var(--pos-border); display:none; }
.dp-box.show { display:block; }
.durasi-info { font-size:11px; color:var(--pos-muted); padding:7px 10px; background:var(--pos-gold-xs); border-radius:var(--pos-r); border:1px solid var(--pos-gold-md); }
.pay-form-foot { padding:13px 16px; border-top:1px solid var(--pos-border); flex-shrink:0; background:#fafafa; }
.btn-konfirm { width:100%; padding:11px; background:var(--pos-black); border:1.5px solid rgba(201,168,76,.4); border-radius:var(--pos-r2); color:var(--pos-gold-lt); font-size:13.5px; font-weight:600; cursor:pointer; transition:.18s; font-family:inherit; }
.btn-konfirm:hover { background:var(--pos-black3); box-shadow:0 4px 16px rgba(201,168,76,.2); }
.btn-pay-cancel { width:100%; padding:8px; background:transparent; border:1px solid var(--pos-border2); border-radius:var(--pos-r2); color:var(--pos-muted); font-size:11.5px; cursor:pointer; margin-top:7px; transition:.12s; font-family:inherit; }
.btn-pay-cancel:hover { border-color:var(--pos-red); color:var(--pos-red); }

/* E-NOTA kolom kanan modal */
.pay-nota-col { flex:1; display:flex; flex-direction:column; background:var(--pos-surface); }
.pay-nota-head { padding:11px 16px; background:var(--pos-surface); border-bottom:1px solid var(--pos-border); display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.pay-nota-title { font-size:12px; font-weight:600; color:#0a0a0a; }
.pay-nota-badge { font-size:9.5px; background:var(--pos-gold-xs); color:var(--pos-gold-dk); border:1px solid var(--pos-gold-md); padding:2px 8px; border-radius:10px; font-weight:600; }
.pay-nota-body  { flex:1; overflow-y:auto; padding:14px; }
.pay-nota-body::-webkit-scrollbar { width:3px; }
.pay-nota-body::-webkit-scrollbar-thumb { background:var(--pos-border2); }
.nota-paper { border:1px solid var(--gray-200); border-radius:var(--pos-r2); overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.06); }
.nota-top { background:var(--pos-black); padding:14px 16px; }
.nota-brand { font-family:'Instrument Serif',serif; font-style:italic; font-size:20px; color:var(--pos-gold-lt); }
.nota-tagline   { font-size:8px; color:rgba(255,255,255,.25); letter-spacing:2px; text-transform:uppercase; margin-top:2px; }
.nota-trx-label { font-size:8px; color:rgba(255,255,255,.3); margin-top:8px; text-transform:uppercase; letter-spacing:1px; }
.nota-trx-num   { font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--pos-gold-lt); margin-top:1px; }
.nota-content { padding:13px 14px; background:#fff; }
.nota-row { display:flex; justify-content:space-between; padding:4.5px 0; font-size:11.5px; border-bottom:1px solid #f0f0f0; }
.nota-row:last-child { border-bottom:none; }
.nota-key { color:var(--pos-muted); flex-shrink:0; }
.nota-val { color:#0a0a0a; font-weight:500; text-align:right; max-width:58%; word-break:break-word; }
.nota-val.gold { color:var(--pos-gold-dk); font-weight:700; font-family:'JetBrains Mono',monospace; }
.nota-sep { height:1px; background:repeating-linear-gradient(90deg,var(--pos-border) 0,var(--pos-border) 4px,transparent 4px,transparent 8px); margin:8px 0; }
.nota-total-box { background:var(--pos-surface); border:1px solid var(--pos-gold-md); border-radius:var(--pos-r); padding:10px 12px; margin:10px 0; display:flex; justify-content:space-between; align-items:center; }
.nota-total-lbl { font-size:12px; font-weight:700; }
.nota-total-val { font-family:'JetBrains Mono',monospace; font-size:15px; font-weight:700; color:var(--pos-gold-dk); }
.nota-foot { font-size:9.5px; color:var(--pos-hint); text-align:center; padding-top:8px; border-top:1px dashed var(--gray-200); line-height:1.6; }

/* KONFIRMASI KEMBALI MODAL */
.konfirm-modal { background:#fff; border-radius:var(--pos-r3); width:420px; overflow:hidden; transform:scale(.94) translateY(8px); transition:.22s cubic-bezier(.34,1.4,.64,1); }
.pos-ov.show .konfirm-modal { transform:scale(1) translateY(0); }
.km-head  { background:var(--pos-black); padding:14px 18px; display:flex; justify-content:space-between; align-items:center; }
.km-title { font-size:14px; font-weight:600; color:var(--pos-gold-lt); }
.km-close { background:none; border:none; color:rgba(255,255,255,.38); cursor:pointer; font-size:18px; line-height:1; transition:.12s; }
.km-close:hover { color:var(--pos-gold-lt); }
.km-body  { padding:18px; }
.km-info  { background:var(--pos-surface); border-radius:var(--pos-r2); padding:13px; margin-bottom:13px; }
.km-info-row { display:flex; justify-content:space-between; font-size:12px; color:var(--pos-muted); padding:3px 0; }
.km-alert { padding:10px 13px; border-radius:var(--pos-r); margin-bottom:13px; font-size:12px; font-weight:500; }
.km-alert.ok   { background:rgba(26,128,80,.08); color:#1a8050; border:1px solid rgba(26,128,80,.2); }
.km-alert.late { background:rgba(220,52,52,.07); color:#c0392b; border:1px solid rgba(220,52,52,.2); }
.km-total { display:flex; justify-content:space-between; align-items:center; padding:12px 13px; background:var(--pos-surface); border-radius:var(--pos-r2); border:1.5px solid var(--pos-gold-md); margin-bottom:15px; }
.km-total-lbl { font-size:13px; font-weight:600; }
.km-total-val { font-size:17px; font-weight:700; font-family:'JetBrains Mono',monospace; color:var(--pos-gold-dk); }
.km-total-val.danger { color:var(--pos-red); }
.btn-km-konfirm { width:100%; padding:12px; background:var(--pos-green); border:none; border-radius:var(--pos-r2); color:#fff; font-size:13.5px; font-weight:600; cursor:pointer; transition:.18s; font-family:inherit; margin-bottom:8px; }
.btn-km-konfirm:hover { background:#15704a; box-shadow:0 3px 12px rgba(26,128,80,.25); }
.btn-km-cancel { width:100%; padding:9px; background:transparent; border:1px solid var(--pos-border2); border-radius:var(--pos-r2); color:var(--pos-muted); font-size:12px; cursor:pointer; transition:.12s; font-family:inherit; }
.btn-km-cancel:hover { border-color:var(--pos-red); color:var(--pos-red); }
</style>

<div class="pos-page">

  {{-- TAB BAR --}}
  <div class="pos-tabbar">
    <div class="pos-tab active" id="tabPOS" onclick="switchTab('pos')">
      🧾 Transaksi Baru
    </div>
    <div class="pos-tab" id="tabKembali" onclick="switchTab('kembali')">
      ↩️ Pengembalian
      <div class="pos-tab-badge {{ $jumlahTerlambat > 0 ? 'red' : '' }}" id="kembaliCount">
        {{ $transaksiAktif->count() }}
      </div>
    </div>
  </div>

  {{-- ══════ TAB 1: POS ══════ --}}
  <div class="pos-tab-content active" id="contentPOS">
    <div class="pos-split">

      {{-- KATALOG --}}
      <div class="pos-katalog">
        <div class="pos-kat-top">
          <div class="pos-search">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input id="posSearch" placeholder="Cari nama baju atau ukuran..." oninput="posRenderGrid()">
          </div>
          <div class="pos-chips" id="posChips"></div>
        </div>
        <div class="pos-grid" id="posGrid"></div>
      </div>

      {{-- CART --}}
      <div class="pos-cart">
        <div class="pos-cart-head">
          <div class="pos-cart-title">
            🛍 Pesanan
            <div class="pos-cart-count" id="posCartCount">0</div>
          </div>
        </div>
        <div class="pos-cart-body" id="posCartBody">
          <div class="pos-cart-empty">
            <div class="pos-cart-empty-ico">🛒</div>
            <div class="pos-cart-empty-txt">Pilih baju dari katalog<br>untuk mulai transaksi</div>
          </div>
        </div>
        <div class="pos-cart-foot">
          <div class="pos-sub-row"><span>Subtotal</span><span id="posSubVal">Rp 0</span></div>
          <div class="pos-total-row">
            <div class="pos-total-lbl">Total</div>
            <div class="pos-total-val" id="posTotalVal">Rp 0</div>
          </div>
          <button class="pos-btn-pay" id="posBtnPay" disabled onclick="posOpenPayModal()">
            🧾 Bayar Sekarang
          </button>
          <button class="pos-btn-reset" onclick="posClearCart()">Batal / Reset</button>
        </div>
      </div>

    </div>
  </div>

  {{-- ══════ TAB 2: PENGEMBALIAN ══════ --}}
  <div class="pos-tab-content" id="contentKembali">
    <div class="kembali-wrap">
      <div class="kembali-header">
        <div class="kembali-title">↩️ Konfirmasi Pengembalian</div>
        <div class="kembali-subtitle">
          {{ $transaksiAktif->count() }} transaksi aktif
          @if($jumlahTerlambat > 0)
            · <span style="color:var(--pos-red);font-weight:600">{{ $jumlahTerlambat }} terlambat</span>
          @endif
        </div>
      </div>

      @if($transaksiAktif->isEmpty())
        <div class="kembali-empty">
          <div class="kembali-empty-ico">✅</div>
          <div style="font-size:14px;font-weight:600;margin-bottom:6px;color:#0a0a0a">Semua baju sudah kembali</div>
          <div style="font-size:12px">Tidak ada transaksi aktif saat ini</div>
        </div>
      @else
        <div class="kembali-grid">
          @foreach($transaksiAktif as $trx)
          @php
            $jatuh     = \Carbon\Carbon::parse($trx->tgl_jatuh_tempo)->startOfDay();
            $nowDay    = \Carbon\Carbon::now()->startOfDay();
            $terlambat = $nowDay->gt($jatuh);
            $hariTelat = $terlambat ? $jatuh->diffInDays($nowDay) : 0;
            $totalDendaTrx = $hariTelat * $dendaPerHari;
            $sisaTagihanTrx = $trx->sisa_tagihan ?? 0;
            $totalBayarKembaliTrx = $sisaTagihanTrx + $totalDendaTrx;
            $detail = $trx->detailTransaksis->first();
          @endphp
          <div class="trx-card{{ $terlambat ? ' terlambat' : '' }}">
            <div class="trx-card-head">
              <div class="trx-no">#TRX-{{ str_pad($trx->id_transaksi, 4, '0', STR_PAD_LEFT) }}</div>
              @if($terlambat)
                <span class="trx-status-late">⚠️ Telat {{ $hariTelat }} hari</span>
              @else
                <span class="trx-status-ok">✓ Tepat waktu</span>
              @endif
            </div>
            <div class="trx-card-body">
              <div class="trx-info-grid">
                <div>
                  <div class="trx-field-k">Pelanggan</div>
                  <div class="trx-field-v">{{ $trx->pelanggan->nama_pelanggan ?? '-' }}</div>
                </div>
                <div>
                  <div class="trx-field-k">No. Telepon</div>
                  <div class="trx-field-v">{{ $trx->pelanggan->no_telp ?? '-' }}</div>
                </div>
                <div>
                  <div class="trx-field-k">Barang</div>
                  <div class="trx-field-v">
                    {{ $detail->barang->nama_barang ?? '-' }}
                    {{ $detail->ukuran ? '('.$detail->ukuran.')' : '' }}
                  </div>
                </div>
                <div>
                  <div class="trx-field-k">Jatuh Tempo</div>
                  <div class="trx-field-v" style="{{ $terlambat ? 'color:var(--pos-red);font-weight:600' : '' }}">
                    {{ \Carbon\Carbon::parse($trx->tgl_jatuh_tempo)->format('d M Y') }}
                  </div>
                </div>
              </div>

              <div class="trx-biaya-box">
                <div class="trx-biaya-row">
                  <span>Total biaya sewa</span>
                  <span>Rp {{ number_format($trx->total_biaya, 0, ',', '.') }}</span>
                </div>
                @if(($trx->metode_bayar ?? 'Lunas') === 'DP' && $sisaTagihanTrx > 0)
                <div class="trx-biaya-row">
                  <span>Sisa DP belum lunas</span>
                  <span style="color:var(--pos-red)">Rp {{ number_format($sisaTagihanTrx, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($terlambat)
                <div class="trx-biaya-row">
                  <span>Denda {{ $hariTelat }} hari × Rp {{ number_format($dendaPerHari, 0, ',', '.') }}</span>
                  <span style="color:var(--pos-red)">Rp {{ number_format($totalDendaTrx, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="trx-biaya-total">
                  <div class="trx-biaya-total-lbl">
                    {{ $totalBayarKembaliTrx > 0 ? 'Tagihan saat kembali' : 'Tidak ada tagihan' }}
                  </div>
                  <div class="trx-biaya-total-val{{ $terlambat ? ' danger' : '' }}">
                    Rp {{ number_format($totalBayarKembaliTrx, 0, ',', '.') }}
                  </div>
                </div>
              </div>

              <button class="btn-kembalikan{{ $terlambat ? ' danger' : '' }}"
                      onclick="openKonfirmKembali({{ $trx->id_transaksi }})">
                ↩️ {{ $terlambat ? 'Proses Pengembalian + Denda' : 'Konfirmasi Pengembalian' }}
              </button>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>{{-- end .pos-page --}}

{{-- ══════════ SIZE PICKER OVERLAY ══════════ --}}
<div class="pos-ov" id="posSizeOv" onclick="if(event.target===this)posCloseSizePicker()">
  <div class="pos-spick">
    <div class="sp-head">
      <div>
        <div class="sp-name" id="spName">-</div>
        <div class="sp-price" id="spPrice">-</div>
      </div>
      <button class="sp-close" onclick="posCloseSizePicker()">✕</button>
    </div>
    <div class="sp-body">
      <div class="sp-lbl">Pilih Ukuran</div>
      <div class="sz-grid" id="szGrid"></div>
      <div class="qty-row">
        <div class="qty-lbl">Jumlah</div>
        <div class="qty-ctrl">
          <button class="qty-btn" onclick="posAdjQty(-1)">−</button>
          <div class="qty-n" id="posQtyN">1</div>
          <button class="qty-btn" onclick="posAdjQty(1)">+</button>
        </div>
      </div>
      <button class="btn-add-cart" id="posBtnAdd" disabled onclick="posAddToCart()">
        + Tambah ke Pesanan
      </button>
    </div>
  </div>
</div>

{{-- ══════════ PAYMENT MODAL (2 kolom) ══════════ --}}
<div class="pos-ov" id="posPayOv" onclick="if(event.target===this)posClosePayModal()">
  <div class="pay-modal-wrap">

    {{-- KIRI: FORM --}}
    <div class="pay-form-col">
      <div class="pay-form-head">
        <div class="pay-form-title">🧾 Detail Penyewaan</div>
        <div class="pay-form-sub">Lengkapi data sebelum konfirmasi transaksi</div>
      </div>
      <div class="pay-form-body">

        <div>
          <div class="pm-sec-title">👤 Data Pelanggan</div>
          <div style="display:flex;flex-direction:column;gap:9px;margin-top:8px">
            <div class="pm-field">
              <label class="pm-label">Nama Pelanggan *</label>
              <input class="pm-input" id="pmNama" placeholder="Nama lengkap pelanggan" oninput="posUpdateNota()">
            </div>
            <div class="pm-grid2">
              <div class="pm-field">
                <label class="pm-label">No. Telepon *</label>
                <input class="pm-input" id="pmTelp" placeholder="0812-xxxx-xxxx">
              </div>
              <div class="pm-field">
                <label class="pm-label">Alamat</label>
                <input class="pm-input" id="pmAlamat" placeholder="Opsional">
              </div>
            </div>
          </div>
        </div>

        <div>
          <div class="pm-sec-title">📅 Periode Sewa</div>
          <div style="display:flex;flex-direction:column;gap:9px;margin-top:8px">
            <div class="pm-grid2">
              <div class="pm-field">
                <label class="pm-label">Tanggal Mulai *</label>
                <input class="pm-input" id="pmTglSewa" type="date" onchange="posRecalc()">
              </div>
              <div class="pm-field">
                <label class="pm-label">Tanggal Kembali *</label>
                <input class="pm-input" id="pmTglKembali" type="date" onchange="posRecalc()">
              </div>
            </div>
            <div class="durasi-info" id="posDurasiInfo">Pilih tanggal untuk melihat kalkulasi biaya</div>
          </div>
        </div>

        <div>
          <div class="pm-sec-title">💳 Metode Pembayaran</div>
          <div style="display:flex;flex-direction:column;gap:9px;margin-top:8px">
            <div class="metode-wrap">
              <div class="met-opt active" id="posOptLunas" onclick="posSetMetode('lunas')">
                <div class="met-lbl">Lunas</div>
                <div class="met-desc">Bayar penuh sekarang</div>
              </div>
              <div class="met-opt" id="posOptDP" onclick="posSetMetode('dp')">
                <div class="met-lbl">DP</div>
                <div class="met-desc">Bayar sebagian dulu</div>
              </div>
            </div>
            <div class="dp-box" id="posDpBox">
              <div class="pm-field">
                <label class="pm-label">Jumlah DP (Rp)</label>
                <input class="pm-input" id="pmDP" type="number" placeholder="Nominal DP" oninput="posRecalc()">
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="pay-form-foot">
        <form id="posForm" action="{{ route('transaksi.store') }}" method="POST">
          @csrf
          <input type="hidden" name="nama_pelanggan" id="fNama">
          <input type="hidden" name="no_telp"        id="fTelp">
          <input type="hidden" name="alamat"          id="fAlamat">
          <input type="hidden" name="id_barang"       id="fBarang">
          <input type="hidden" name="items"           id="fItems">
          <input type="hidden" name="tgl_sewa"        id="fTglSewa">
          <input type="hidden" name="tgl_jatuh_tempo" id="fTglJatuh">
          <input type="hidden" name="metode_bayar"    id="fMetode">
          <input type="hidden" name="jumlah_dp"       id="fDP">
          <button type="button" class="btn-konfirm" onclick="posKonfirmasi()">
            ✓ Konfirmasi &amp; Simpan Transaksi
          </button>
        </form>
        <button class="btn-pay-cancel" onclick="posClosePayModal()">Batal</button>
      </div>
    </div>

    {{-- KANAN: E-NOTA PREVIEW --}}
    <div class="pay-nota-col">
      <div class="pay-nota-head">
        <div class="pay-nota-title">Preview E-Nota</div>
        <div class="pay-nota-badge">Auto-update</div>
      </div>
      <div class="pay-nota-body">
        <div class="nota-paper">
          <div class="nota-top">
            <div class="nota-brand">NM Gallery</div>
            <div class="nota-tagline">Baju Bodo Collection · Makassar</div>
            <div class="nota-trx-label">BUKTI PENYEWAAN</div>
            <div class="nota-trx-num">#TRX-PREVIEW</div>
          </div>
          <div class="nota-content">
            <div class="nota-row">
              <span class="nota-key">Pelanggan</span>
              <span class="nota-val" id="nPelanggan">—</span>
            </div>
            <div class="nota-row">
              <span class="nota-key">Barang</span>
              <span class="nota-val" id="nBarang">—</span>
            </div>
            <div class="nota-row">
              <span class="nota-key">Periode</span>
              <span class="nota-val" id="nPeriode">—</span>
            </div>
            <div class="nota-row">
              <span class="nota-key">Durasi</span>
              <span class="nota-val" id="nDurasi">—</span>
            </div>
            <div id="nItemsWrap"></div>
            <div class="nota-sep"></div>
            <div class="nota-row">
              <span class="nota-key">Total Sewa</span>
              <span class="nota-val gold" id="nTotalSewa">Rp 0</span>
            </div>
            <div class="nota-row" id="nDPRow" style="display:none">
              <span class="nota-key">DP Dibayar</span>
              <span class="nota-val" id="nDP" style="color:#1a8050">—</span>
            </div>
            <div class="nota-row" id="nSisaRow" style="display:none">
              <span class="nota-key">Sisa saat kembali</span>
              <span class="nota-val" id="nSisa" style="color:#c0392b">—</span>
            </div>
            <div class="nota-total-box">
              <span class="nota-total-lbl" id="nTotalLabel">Dibayar Sekarang</span>
              <span class="nota-total-val" id="nTotalBayar">Rp 0</span>
            </div>
            <div class="nota-foot">
              Terima kasih telah mempercayakan<br>
              momen Anda kepada <strong>NM Gallery</strong> ✦
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ══════════ KONFIRMASI PENGEMBALIAN MODAL ══════════ --}}
<div class="pos-ov" id="konfirmOv" onclick="if(event.target===this)closeKonfirmKembali()">
  <div class="konfirm-modal">
    <div class="km-head">
      <div class="km-title">↩️ Konfirmasi Pengembalian</div>
      <button class="km-close" onclick="closeKonfirmKembali()">✕</button>
    </div>
    <div class="km-body">
      <div class="km-info" id="kmInfo"></div>
      <div class="km-alert" id="kmAlert"></div>
      <div class="km-total">
        <div class="km-total-lbl">Total Tagihan Saat Kembali</div>
        <div class="km-total-val" id="kmTotalVal">Rp 0</div>
      </div>
      <form id="kmForm" method="POST">
        @csrf
        @method('PUT')
        <button type="submit" class="btn-km-konfirm">
          ✅ Konfirmasi Pengembalian &amp; Selesai
        </button>
      </form>
      <button class="btn-km-cancel" onclick="closeKonfirmKembali()">Batal</button>
    </div>
  </div>
</div>

<script>
// ─── DATA FROM SERVER ───
const POS_BARANG     = @json($posBarangData);
const TRX_AKTIF      = @json($transaksiData);
const DENDA_PER_HARI = {{ $dendaPerHari }};

// ─── STATE ───
let posFilter    = 'Semua';
let posCart      = [];
let posPicked    = null, posPickedSize = null, posPickedQty = 1;
let posMetode    = 'lunas';
const POS_FILTERS = ['Semua', 'Tersedia', 'Disewa'];

const posFmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

// ─── TAB SWITCH ───
function switchTab(tab) {
    ['pos','kembali'].forEach(t => {
        document.getElementById('tab' + (t==='pos'?'POS':'Kembali')).classList.toggle('active', t===tab);
        document.getElementById('content' + (t==='pos'?'POS':'Kembali')).classList.toggle('active', t===tab);
    });
}

// ─── CHIPS ───
function posRenderChips() {
    document.getElementById('posChips').innerHTML = POS_FILTERS.map(f =>
        `<div class="pos-chip${f===posFilter?' active':''}" onclick="posSetFilter('${f}')">${f}</div>`
    ).join('');
}
function posSetFilter(f) { posFilter = f; posRenderGrid(); }

// ─── GRID ───
function posRenderGrid() {
    const q    = document.getElementById('posSearch').value.toLowerCase();
    const data = POS_BARANG.filter(b => {
        if (posFilter === 'Tersedia' && b.status !== 'Tersedia') return false;
        if (posFilter === 'Disewa'   && b.status !== 'Disewa')   return false;
        if (q && !b.nama.toLowerCase().includes(q)) return false;
        return true;
    });
    const grid = document.getElementById('posGrid');
    if (!data.length) {
        grid.innerHTML = '<div style="grid-column:1/-1;padding:40px;text-align:center;color:#bbb;font-size:12px">Tidak ada barang yang sesuai</div>';
        posRenderChips(); return;
    }
    grid.innerHTML = data.map(b => {
        const isOut     = b.status !== 'Tersedia';
        const stokTotal = Object.values(b.stok).reduce((a,v)=>a+v, 0);
        const hasFoto   = b.foto && b.foto !== 'null';
        return `<div class="pos-card${isOut?' disewa':''}" onclick="${isOut?'':'posOpenSizePicker('+b.id+')'}">
          <div class="pos-card-img">
            ${hasFoto ? `<img src="/${b.foto}" onerror="this.style.display='none'">` : ''}
            <span style="font-size:36px${hasFoto?';display:none':''}">👘</span>
            <div class="pos-badge ${isOut?'out':'ok'}">${isOut?'DISEWA':'TERSEDIA'}</div>
          </div>
          <div class="pos-card-body">
            <div class="pos-card-name">${b.nama}</div>
            <div class="pos-card-meta">${isOut?'Sedang disewa':'Stok: '+stokTotal+' pcs · '+(b.ukuran||Object.keys(b.stok).join(', '))}</div>
            <div class="pos-card-price">${posFmt(b.harga)}<small>/hari</small></div>
          </div>
        </div>`;
    }).join('');
    posRenderChips();
}

// ─── SIZE PICKER ───
function posOpenSizePicker(id) {
    posPicked = POS_BARANG.find(b => b.id === id);
    posPickedSize = null; posPickedQty = 1;
    document.getElementById('spName').textContent  = posPicked.nama;
    document.getElementById('spPrice').textContent = posFmt(posPicked.harga) + ' / hari';
    document.getElementById('posQtyN').textContent = 1;
    document.getElementById('posBtnAdd').disabled  = true;
    const sizes = Object.keys(posPicked.stok);
    document.getElementById('szGrid').innerHTML = sizes.length
        ? sizes.map(s => `<div class="sz-opt${posPicked.stok[s]===0?' sz-dis':''}" id="sz${s}" onclick="posPickSize('${s}')">
            <div class="sz-lbl">${s}</div>
            <div class="sz-stok">${posPicked.stok[s]} pcs</div>
          </div>`).join('')
        : '<div style="grid-column:1/-1;font-size:11px;color:#bbb;text-align:center;padding:8px">Tidak ada stok</div>';
    document.getElementById('posSizeOv').classList.add('show');
}
function posCloseSizePicker() { document.getElementById('posSizeOv').classList.remove('show'); }

function posPickSize(s) {
    if (!posPicked || posPicked.stok[s] === 0) return;
    posPickedSize = s; posPickedQty = 1;
    document.getElementById('posQtyN').textContent = 1;
    document.querySelectorAll('.sz-opt').forEach(el => el.classList.remove('sz-active'));
    document.getElementById('sz'+s).classList.add('sz-active');
    document.getElementById('posBtnAdd').disabled = false;
}
function posAdjQty(d) {
    if (!posPickedSize) return;
    posPickedQty = Math.min(posPicked.stok[posPickedSize], Math.max(1, posPickedQty + d));
    document.getElementById('posQtyN').textContent = posPickedQty;
}
function posAddToCart() {
    if (!posPicked || !posPickedSize) return;
    const key = posPicked.id + '-' + posPickedSize;
    const ex  = posCart.find(c => c.key === key);
    if (ex) ex.qty = Math.min(posPicked.stok[posPickedSize], ex.qty + posPickedQty);
    else posCart.push({ key, id:posPicked.id, nama:posPicked.nama, size:posPickedSize, qty:posPickedQty, harga:posPicked.harga });
    posCloseSizePicker();
    posRenderCart();
}

// ─── CART ───
function posRenderCart() {
    const total = posCart.reduce((s,c) => s + c.harga * c.qty, 0);
    const count = posCart.reduce((s,c) => s + c.qty, 0);
    document.getElementById('posCartCount').textContent = count;
    document.getElementById('posSubVal').textContent    = posFmt(total);
    document.getElementById('posTotalVal').textContent  = posFmt(total);
    document.getElementById('posBtnPay').disabled       = posCart.length === 0;
    const body = document.getElementById('posCartBody');
    if (!posCart.length) {
        body.innerHTML = '<div class="pos-cart-empty"><div class="pos-cart-empty-ico">🛒</div><div class="pos-cart-empty-txt">Pilih baju dari katalog<br>untuk mulai transaksi</div></div>';
        return;
    }
    body.innerHTML = posCart.map((c,i) => `
      <div class="pos-cart-item">
        <div class="pos-item-name">${c.nama}</div>
        <div class="pos-item-sub">Ukuran ${c.size} · ${posFmt(c.harga)}/hari</div>
        <div class="pos-item-row">
          <div class="pos-qc">
            <button class="pos-qb" onclick="posAdjCartQty(${i},-1)">−</button>
            <div class="pos-qn">${c.qty}</div>
            <button class="pos-qb" onclick="posAdjCartQty(${i},1)">+</button>
          </div>
          <div class="pos-item-price">${posFmt(c.harga * c.qty)}</div>
        </div>
        <button class="pos-del" onclick="posRemoveCart(${i})">✕</button>
      </div>`).join('');
    posUpdateNota();
}
function posAdjCartQty(i, d) {
    const b = POS_BARANG.find(x => x.id === posCart[i].id);
    posCart[i].qty = Math.min(b?(b.stok[posCart[i].size]||99):99, Math.max(1, posCart[i].qty+d));
    posRenderCart();
}
function posRemoveCart(i) { posCart.splice(i,1); posRenderCart(); }
function posClearCart()   { posCart = []; posRenderCart(); }

// ─── PAYMENT MODAL ───
function posOpenPayModal() {
    const now = new Date(), d3 = new Date();
    d3.setDate(d3.getDate() + 3);
    const fmt = d => d.toISOString().slice(0,10);
    document.getElementById('pmTglSewa').value    = fmt(now);
    document.getElementById('pmTglKembali').value = fmt(d3);
    document.getElementById('pmNama').value   = '';
    document.getElementById('pmTelp').value   = '';
    document.getElementById('pmAlamat').value = '';
    document.getElementById('pmDP').value     = '';
    posSetMetode('lunas');
    posRecalc();
    document.getElementById('posPayOv').classList.add('show');
}
function posClosePayModal() { document.getElementById('posPayOv').classList.remove('show'); }

function posSetMetode(m) {
    posMetode = m;
    document.getElementById('posOptLunas').classList.toggle('active', m==='lunas');
    document.getElementById('posOptDP').classList.toggle('active', m==='dp');
    document.getElementById('posDpBox').classList.toggle('show', m==='dp');
    posRecalc();
}

function posGetDurasi() {
    const s = document.getElementById('pmTglSewa').value;
    const k = document.getElementById('pmTglKembali').value;
    if (!s||!k) return 0;
    return Math.max(1, Math.ceil((new Date(k)-new Date(s))/86400000));
}

function posRecalc() {
    const dur     = posGetDurasi();
    const perHari = posCart.reduce((s,c) => s + c.harga * c.qty, 0);
    const total   = perHari * dur;
    document.getElementById('posDurasiInfo').textContent = dur
        ? `Durasi ${dur} hari · Total biaya sewa: ${posFmt(total)}`
        : 'Pilih tanggal untuk melihat kalkulasi biaya';
    posUpdateNota();
}

// ─── UPDATE NOTA REAL-TIME ───
function posUpdateNota() {
    const dur     = posGetDurasi();
    const nama    = document.getElementById('pmNama')?.value || '—';
    const tglSewa = document.getElementById('pmTglSewa')?.value  || '';
    const tglJatuh= document.getElementById('pmTglKembali')?.value || '';
    const perHari = posCart.reduce((s,c) => s + c.harga * c.qty, 0);
    const total   = perHari * dur;
    const first   = posCart[0];

    document.getElementById('nPelanggan').textContent = nama || '—';
    document.getElementById('nBarang').textContent    = first
        ? first.nama + ' (' + first.size + ')' + (posCart.length > 1 ? ' +' + (posCart.length-1) + ' lainnya' : '')
        : '—';
    document.getElementById('nPeriode').textContent   = (tglSewa && tglJatuh) ? tglSewa + ' s/d ' + tglJatuh : '—';
    document.getElementById('nDurasi').textContent    = dur ? dur + ' hari' : '—';
    document.getElementById('nTotalSewa').textContent = dur ? posFmt(total) : 'Rp 0';

    // Item rows
    document.getElementById('nItemsWrap').innerHTML = posCart.map(c => {
        const subtotal = c.harga * c.qty * Math.max(1, dur);
        return `<div class="nota-row"><span class="nota-key">${c.nama} (${c.size}) ×${c.qty}</span><span class="nota-val">${posFmt(subtotal)}</span></div>`;
    }).join('');

    // DP/Lunas
    if (posMetode === 'dp') {
        const dp   = parseInt(document.getElementById('pmDP')?.value) || Math.round(total * 0.5);
        const sisa = Math.max(0, total - dp);
        document.getElementById('nDPRow').style.display   = 'flex';
        document.getElementById('nSisaRow').style.display = 'flex';
        document.getElementById('nDP').textContent   = posFmt(dp);
        document.getElementById('nSisa').textContent = posFmt(sisa);
        document.getElementById('nTotalLabel').textContent = 'DP Dibayar Sekarang';
        document.getElementById('nTotalBayar').textContent = posFmt(dp);
    } else {
        document.getElementById('nDPRow').style.display   = 'none';
        document.getElementById('nSisaRow').style.display = 'none';
        document.getElementById('nTotalLabel').textContent = 'Dibayar Sekarang';
        document.getElementById('nTotalBayar').textContent = dur ? posFmt(total) : 'Rp 0';
    }
}

// ─── KONFIRMASI SUBMIT ───
function posKonfirmasi() {
    const nama = document.getElementById('pmNama').value.trim();
    const telp = document.getElementById('pmTelp').value.trim();
    if (!nama) { alert('Nama pelanggan harus diisi!'); document.getElementById('pmNama').focus(); return; }
    if (!telp) { alert('No. telepon harus diisi!'); document.getElementById('pmTelp').focus(); return; }
    if (!posGetDurasi()) { alert('Pilih tanggal sewa dan tanggal kembali!'); return; }
    if (!posCart.length) { alert('Pilih barang terlebih dahulu!'); return; }

    const dur     = posGetDurasi();
    const perHari = posCart.reduce((s,c) => s + c.harga * c.qty, 0);
    const total   = perHari * dur;
    const dp      = posMetode === 'dp'
        ? (parseInt(document.getElementById('pmDP').value) || Math.round(total * 0.5))
        : total;

    document.getElementById('fNama').value     = nama;
    document.getElementById('fTelp').value     = telp;
    document.getElementById('fAlamat').value   = document.getElementById('pmAlamat').value;
    document.getElementById('fBarang').value   = posCart[0].id;
    document.getElementById('fItems').value    = JSON.stringify(posCart.map(c => ({ size:c.size, jumlah:c.qty, harga:c.harga })));
    document.getElementById('fTglSewa').value  = document.getElementById('pmTglSewa').value;
    document.getElementById('fTglJatuh').value = document.getElementById('pmTglKembali').value;
    document.getElementById('fMetode').value   = posMetode === 'lunas' ? 'Lunas' : 'DP';
    document.getElementById('fDP').value       = dp;

    document.getElementById('posForm').submit();
}

// ─── KONFIRMASI PENGEMBALIAN ───
function openKonfirmKembali(trxId) {
    const trx = TRX_AKTIF.find(t => t.id === trxId);
    if (!trx) return;

    document.getElementById('kmInfo').innerHTML = `
        <div class="km-info-row"><span>No. Transaksi</span><span style="font-family:monospace;font-weight:600">${trx.no_trx}</span></div>
        <div class="km-info-row"><span>Pelanggan</span><span style="font-weight:600">${trx.pelanggan}</span></div>
        <div class="km-info-row"><span>Barang</span><span>${trx.barang} (${trx.ukuran})</span></div>
        <div class="km-info-row"><span>Tgl Sewa → Jatuh Tempo</span><span>${trx.tgl_sewa} → ${trx.tgl_jatuh}</span></div>
        <div class="km-info-row"><span>Total biaya sewa</span><span>${posFmt(trx.total_biaya)}</span></div>
        ${trx.sisa_tagihan > 0 ? `<div class="km-info-row"><span>Sisa DP belum lunas</span><span style="color:#c0392b">${posFmt(trx.sisa_tagihan)}</span></div>` : ''}
        ${trx.terlambat ? `<div class="km-info-row"><span>Denda (${trx.hari_telat} hari × ${posFmt(trx.denda_per_hari)})</span><span style="color:#c0392b">${posFmt(trx.total_denda)}</span></div>` : ''}
    `;

    const alertEl = document.getElementById('kmAlert');
    if (trx.terlambat) {
        alertEl.className = 'km-alert late';
        alertEl.innerHTML = `⚠️ Terlambat <strong>${trx.hari_telat} hari</strong> dari jatuh tempo ${trx.tgl_jatuh}. Denda: <strong>${posFmt(trx.total_denda)}</strong>`;
    } else {
        alertEl.className = 'km-alert ok';
        alertEl.innerHTML = '✅ Pengembalian tepat waktu — tidak ada denda';
    }

    const totalEl = document.getElementById('kmTotalVal');
    totalEl.textContent = posFmt(trx.total_bayar_kembali);
    totalEl.className   = 'km-total-val' + (trx.total_bayar_kembali > 0 ? ' danger' : '');

    // Set form action ke route pengembalian
    document.getElementById('kmForm').action = `/transaksi/${trxId}`;

    document.getElementById('konfirmOv').classList.add('show');
}
function closeKonfirmKembali() {
    document.getElementById('konfirmOv').classList.remove('show');
}

// ─── EVENT LISTENERS ───
document.addEventListener('DOMContentLoaded', () => {
    ['pmNama','pmTelp','pmTglSewa','pmTglKembali','pmDP'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input',  posUpdateNota);
            el.addEventListener('change', posUpdateNota);
        }
    });
});

// ─── INIT ───
posRenderChips();
posRenderGrid();
</script>

@endsection