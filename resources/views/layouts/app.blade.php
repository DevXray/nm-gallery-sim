<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NM Gallery SIM — @yield('title', 'Laporan')</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>

/* ══════════════════════════════════════════════
   DESIGN TOKENS
══════════════════════════════════════════════ */
:root {
  --black:    #0a0a0a;
  --black2:   #111111;
  --black3:   #1a1a1a;
  --black4:   #242424;
  --black5:   #2e2e2e;

  --gold:     #C9A84C;
  --gold-lt:  #e0c06e;
  --gold-dk:  #a07830;
  --gold-xs:  rgba(201,168,76,.08);
  --gold-sm:  rgba(201,168,76,.14);
  --gold-md:  rgba(201,168,76,.25);
  --gold-rim: rgba(201,168,76,.28);

  --white:    #ffffff;
  --gray-50:  #fafafa;
  --gray-100: #f4f4f5;
  --gray-200: #e4e4e7;
  --gray-300: #d4d4d8;
  --gray-400: #a1a1aa;
  --gray-500: #71717a;
  --gray-600: #52525b;
  --gray-700: #3f3f46;

  --ff:       'Plus Jakarta Sans', sans-serif;
  --ff-serif: 'Instrument Serif', serif;
  --ff-mono:  'JetBrains Mono', monospace;

  --r:   6px;
  --r2:  8px;
  --r3:  12px;
  --r4:  16px;

  /*
   * --sidebar-w mengontrol lebar sidebar.
   * Saat body.sidebar-collapsed, CSS transisi akan membawa lebar ke 0.
   * Kita definisikan sebagai custom property agar mudah diubah di satu tempat.
   */
  --sidebar:  230px;

  --sh-xs:   0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --sh-sm:   0 2px 8px rgba(0,0,0,.07), 0 1px 3px rgba(0,0,0,.05);
  --sh-md:   0 4px 16px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
  --sh-gold: 0 2px 12px rgba(201,168,76,.18);
}

/* ══════════════════════════════════════════════
   RESET & BASE
══════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 13.5px; -webkit-font-smoothing: antialiased; }
body {
  font-family: var(--ff);
  background: var(--gray-100);
  color: var(--black);
  height: 100vh;
  display: flex;
  overflow: hidden;
}
input, select, button, textarea { font-family: var(--ff); }
a { text-decoration: none; color: inherit; }
::selection { background: var(--gold-sm); }
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 10px; }

/* ══════════════════════════════════════════════
   SIDEBAR — dengan dukungan toggle collapse
══════════════════════════════════════════════ */
.sidebar {
  width: var(--sidebar);
  min-width: var(--sidebar);
  background: var(--black);
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  z-index: 30;

  /*
   * Transisi lebar: saat class .sidebar-collapsed ditambahkan ke <body>,
   * sidebar akan menyusut ke 0 secara halus selama 0.28s.
   * Kita animasikan width DAN min-width sekaligus karena flexbox
   * menggunakan min-width sebagai batas bawah penyusutan.
   */
  transition: width 0.28s cubic-bezier(.4,0,.2,1),
              min-width 0.28s cubic-bezier(.4,0,.2,1);
}

/* State collapsed: sidebar benar-benar tersembunyi */
body.sidebar-collapsed .sidebar {
  width: 0;
  min-width: 0;
}

.sidebar::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 160px;
  background: radial-gradient(ellipse 200px 100px at 50% 0%, rgba(201,168,76,.12) 0%, transparent 70%);
  pointer-events: none;
}
.sidebar::after {
  content: '';
  position: absolute;
  right: 0; top: 0; bottom: 0;
  width: 1px;
  background: linear-gradient(180deg, transparent 0%, var(--gold) 30%, var(--gold) 70%, transparent 100%);
  opacity: .16;
}

.s-logo {
  padding: 22px 18px 18px;
  border-bottom: 1px solid rgba(255,255,255,.06);
  display: flex;
  align-items: center;
  gap: 11px;
  white-space: nowrap; /* mencegah konten sidebar membungkus saat animasi */
}
.logo-icon {
  width: 36px; height: 36px;
  border-radius: 8px;
  background: linear-gradient(135deg, var(--gold-lt), var(--gold), var(--gold-dk));
  display: flex; align-items: center; justify-content: center;
  font-family: var(--ff-serif);
  font-size: 18px; font-style: italic;
  color: var(--black);
  flex-shrink: 0;
  box-shadow: 0 2px 10px rgba(201,168,76,.35), inset 0 1px 0 rgba(255,255,255,.3);
  position: relative; overflow: hidden;
}
.logo-icon::after {
  content: '';
  position: absolute;
  top: -50%; left: -60%;
  width: 50%; height: 200%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
  transform: skewX(-15deg);
  animation: logoShimmer 3.5s infinite;
}
@keyframes logoShimmer {
  0%, 60% { left: -60%; }
  80%      { left: 130%; }
  100%     { left: 130%; }
}
.logo-words { min-width: 0; white-space: nowrap; }
.logo-name { font-size: 14px; font-weight: 700; color: var(--white); letter-spacing: .1px; line-height: 1.2; }
.logo-sub  { font-size: 9.5px; color: rgba(255,255,255,.3); letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; font-weight: 500; }

.s-label {
  font-size: 9px; font-weight: 700;
  letter-spacing: 2px; text-transform: uppercase;
  color: rgba(255,255,255,.2);
  padding: 16px 18px 6px;
  white-space: nowrap;
}

.nav-item {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 12px;
  margin: 1px 8px;
  border-radius: var(--r2);
  cursor: pointer;
  color: rgba(255,255,255,.45);
  font-size: 12.5px; font-weight: 500;
  transition: all .15s ease;
  user-select: none;
  position: relative;
  text-decoration: none;
  white-space: nowrap;
}
.nav-item:hover { background: rgba(255,255,255,.06); color: rgba(255,255,255,.75); }
.nav-item.active {
  background: linear-gradient(90deg, rgba(201,168,76,.18) 0%, rgba(201,168,76,.06) 100%);
  color: var(--gold-lt);
  font-weight: 600;
}
.nav-item.active::before {
  content: '';
  position: absolute;
  left: -8px; top: 8px; bottom: 8px;
  width: 3px;
  background: var(--gold);
  border-radius: 0 2px 2px 0;
}
.n-ico { width: 15px; height: 15px; flex-shrink: 0; opacity: .6; }
.nav-item.active .n-ico { opacity: 1; }

.nav-pill {
  margin-left: auto;
  font-size: 9.5px; font-weight: 700;
  padding: 1.5px 6px; border-radius: 8px;
}
.nav-pill.warn { background: rgba(220,80,60,.18); color: #e87060; border: 1px solid rgba(220,80,60,.3); }
.nav-pill.info { background: var(--gold-xs); color: var(--gold-lt); border: 1px solid var(--gold-md); }

/* ══════════════════════════════════════════════
   SIDEBAR FOOTER — Area Profil dengan Dropdown
══════════════════════════════════════════════ */
.s-footer {
  margin-top: auto;
  padding: 8px 8px 12px;
  border-top: 1px solid rgba(255,255,255,.06);
  position: relative; /* anchor untuk dropdown */
}

/*
 * Dropdown profil: muncul di atas s-user (ke atas, bukan ke bawah)
 * menggunakan `bottom: 100%` agar selalu berada di atas tombol profil.
 */
.profile-dropdown {
  position: absolute;
  bottom: calc(100% - 8px); /* sedikit overlap untuk visual continuity */
  left: 8px;
  right: 8px;
  background: var(--black3);
  border: 1px solid rgba(255,255,255,.1);
  border-bottom: none; /* menyambung dengan s-user */
  border-radius: var(--r2) var(--r2) 0 0;
  overflow: hidden;
  box-shadow: 0 -8px 24px rgba(0,0,0,.4);

  /* Animasi: dropdown muncul dari bawah dengan scale */
  opacity: 0;
  transform: translateY(6px) scaleY(0.95);
  transform-origin: bottom center;
  pointer-events: none;
  transition: opacity .18s ease, transform .18s ease;
}

/* State aktif: dropdown terlihat dan bisa diklik */
.profile-dropdown.open {
  opacity: 1;
  transform: translateY(0) scaleY(1);
  pointer-events: all;
}

/* Header dropdown: info nama & role */
.pd-header {
  padding: 14px 14px 10px;
  border-bottom: 1px solid rgba(255,255,255,.07);
}
.pd-role-badge {
  display: inline-block;
  background: var(--gold-xs);
  color: var(--gold-lt);
  border: 1px solid var(--gold-md);
  padding: 2px 8px;
  border-radius: 5px;
  font-size: 10px;
  font-weight: 700;
  margin-bottom: 6px;
}
.pd-name {
  font-size: 13px;
  font-weight: 700;
  color: rgba(255,255,255,.9);
  margin-bottom: 1px;
}
.pd-username {
  font-size: 10.5px;
  color: rgba(255,255,255,.3);
}

/* Tombol aksi di dalam dropdown */
.pd-actions { padding: 8px; }
.pd-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 9px 11px;
  border-radius: var(--r);
  border: none;
  background: transparent;
  color: rgba(255,255,255,.5);
  font-size: 12px;
  font-family: var(--ff);
  font-weight: 500;
  cursor: pointer;
  transition: all .12s;
  text-align: left;
  text-decoration: none;
}
.pd-btn:hover {
  background: rgba(255,255,255,.07);
  color: rgba(255,255,255,.85);
}
.pd-btn.danger:hover {
  background: rgba(220,52,52,.12);
  color: #e87060;
}

/* Wrapper s-user — sekarang hanya sebagai tombol trigger */
.s-user {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 9px 11px;
  border-radius: var(--r2);
  cursor: pointer;
  transition: background .15s;
  user-select: none;
  position: relative;
  z-index: 1;
}
.s-user:hover { background: rgba(255,255,255,.05); }

/* Saat dropdown terbuka, s-user mendapat highlight */
.s-user.active {
  background: rgba(255,255,255,.07);
  border-radius: 0 0 var(--r2) var(--r2);
}

.s-ava {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold-dk), var(--gold));
  border: 1.5px solid rgba(201,168,76,.5);
  display: flex; align-items: center; justify-content: center;
  font-size: 11.5px; font-weight: 700;
  color: var(--black); flex-shrink: 0;
}
.s-uname { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.75); white-space: nowrap; }
.s-urole { font-size: 10px; color: rgba(255,255,255,.25); margin-top: 1px; }

/* Panah kecil di ujung kanan s-user sebagai visual affordance */
.s-chevron {
  margin-left: auto;
  color: rgba(255,255,255,.25);
  transition: transform .2s ease;
  flex-shrink: 0;
}
.s-user.active .s-chevron {
  transform: rotate(180deg); /* panah balik saat dropdown terbuka */
  color: var(--gold-lt);
}

/* ══════════════════════════════════════════════
   TOPBAR — dengan toggle button dan help button
══════════════════════════════════════════════ */
.topbar {
  height: 52px; min-height: 52px;
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
  display: flex; align-items: center;
  padding: 0 20px 0 16px; /* padding kiri lebih kecil untuk toggle button */
  gap: 12px;
  box-shadow: var(--sh-xs);
  position: relative; z-index: 10;
}

/*
 * Tombol toggle sidebar: selalu terlihat di pojok kiri topbar.
 * Animasinya sederhana — tiga garis yang bertransisi menjadi panah.
 */
.sidebar-toggle-btn {
  width: 32px; height: 32px;
  border-radius: var(--r2);
  border: 1px solid var(--gray-200);
  background: transparent;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all .15s;
  color: var(--gray-500);
}
.sidebar-toggle-btn:hover {
  background: var(--gray-100);
  border-color: var(--gray-300);
  color: var(--black);
}
/* Ikon hamburger dibuat dari pseudo-elements + box-shadow */
.hamburger {
  width: 14px; height: 2px;
  background: currentColor;
  border-radius: 2px;
  position: relative;
  transition: all .22s ease;
}
.hamburger::before,
.hamburger::after {
  content: '';
  position: absolute;
  left: 0;
  width: 100%; height: 2px;
  background: currentColor;
  border-radius: 2px;
  transition: all .22s ease;
}
.hamburger::before { top: -5px; }
.hamburger::after  { top: 5px; width: 10px; } /* lebih pendek untuk estetika */

/* Saat sidebar collapsed, hamburger berubah menjadi tanda ">" */
body.sidebar-collapsed .hamburger {
  background: var(--gold-dk);
}
body.sidebar-collapsed .hamburger::before {
  top: -4px;
  transform: rotate(35deg);
  width: 8px;
  transform-origin: left;
}
body.sidebar-collapsed .hamburger::after {
  top: 2px;
  transform: rotate(-35deg);
  width: 8px;
  transform-origin: left;
}

.tb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--gray-400); }
.tb-breadcrumb .sep { opacity: .5; }
.tb-breadcrumb .cur { color: var(--black); font-weight: 600; }

.tb-search {
  flex: 1; max-width: 320px;
  margin-left: 8px;
  display: flex; align-items: center;
  background: var(--gray-100);
  border: 1px solid var(--gray-200);
  border-radius: var(--r2);
  padding: 0 11px; gap: 7px;
  transition: border-color .2s, box-shadow .2s;
}
.tb-search:focus-within { border-color: var(--gold-rim); background: var(--white); box-shadow: 0 0 0 3px var(--gold-xs); }
.tb-search input { flex: 1; border: none; background: transparent; outline: none; font-size: 12.5px; color: var(--black); padding: 7px 0; }
.tb-search input::placeholder { color: var(--gray-400); }
.tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }

/*
 * Tombol panduan "?" — ikon bundar minimalis di topbar kanan.
 * Warnanya netral (outline) agar tidak bersaing dengan CTA utama,
 * tapi cukup menonjol sebagai titik bantuan.
 */
.help-btn {
  width: 32px; height: 32px;
  border-radius: 50%;
  border: 1.5px solid var(--gray-200);
  background: var(--white);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  color: var(--gray-500);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: all .15s;
  flex-shrink: 0;
  box-shadow: var(--sh-xs);
  font-family: var(--ff);
  line-height: 1;
}
.help-btn:hover {
  border-color: var(--gold-rim);
  color: var(--gold-dk);
  background: var(--gold-xs);
  box-shadow: var(--sh-gold);
  transform: scale(1.05);
}
/* Tooltip sederhana via CSS */
.help-btn[title]:hover::after {
  content: attr(title);
  position: absolute;
  top: 44px; right: 0;
  background: var(--black);
  color: var(--gold-lt);
  font-size: 11px;
  font-weight: 500;
  padding: 5px 10px;
  border-radius: var(--r);
  white-space: nowrap;
  pointer-events: none;
  border: 1px solid var(--gold-rim);
  z-index: 100;
}
/* Kita butuh position: relative pada container agar tooltip ter-anchor */
.tb-right { position: relative; }

/* ══════════════════════════════════════════════
   MAIN AREA
══════════════════════════════════════════════ */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: var(--gray-100);
  /*
   * min-width: 0 penting di sini! Tanpa ini, flex child tidak akan
   * menyusut di bawah content intrinsic width-nya, membuat layout
   * meluap saat sidebar menghilang.
   */
  min-width: 0;
}

/* ══════════════════════════════════════════════
   BUTTONS (tidak berubah dari versi sebelumnya)
══════════════════════════════════════════════ */
.btn-gold {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 16px;
  background: var(--black);
  color: var(--gold-lt);
  border: 1px solid var(--gold-rim);
  border-radius: var(--r2);
  font-size: 12.5px; font-weight: 600;
  cursor: pointer; white-space: nowrap;
  transition: all .18s;
  position: relative; overflow: hidden;
  text-decoration: none;
}
.btn-gold::before {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(201,168,76,.08) 0%, transparent 60%);
}
.btn-gold:hover { background: var(--black3); box-shadow: var(--sh-gold); border-color: var(--gold); color: var(--gold-lt); }

.btn-outline {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 14px;
  background: var(--white);
  color: var(--gray-600);
  border: 1px solid var(--gray-200);
  border-radius: var(--r2);
  font-size: 12.5px; font-weight: 500;
  cursor: pointer; white-space: nowrap;
  transition: all .15s;
  text-decoration: none;
}
.btn-outline:hover { border-color: var(--gold-rim); color: var(--gold-dk); }

.btn-white {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 14px;
  background: var(--white);
  color: var(--black);
  border: 1px solid var(--gray-200);
  border-radius: var(--r2);
  font-size: 12.5px; font-weight: 500;
  cursor: pointer; white-space: nowrap;
  transition: all .15s;
  box-shadow: var(--sh-xs);
  text-decoration: none;
}
.btn-white:hover { border-color: var(--gray-300); box-shadow: var(--sh-sm); }

/* ══════════════════════════════════════════════
   CONTENT AREA & CARDS (tidak berubah)
══════════════════════════════════════════════ */
.content { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 24px; }

.pg-head { margin-bottom: 22px; }
.pg-title { font-size: 20px; font-weight: 700; color: var(--black); letter-spacing: -.2px; }
.pg-sub   { font-size: 12.5px; color: var(--gray-500); margin-top: 4px; }

.card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--r3);
  box-shadow: var(--sh-xs);
  overflow: hidden;
}
.card.gold-top { border-top: 2px solid var(--gold); }
.card-head {
  padding: 14px 18px 12px;
  border-bottom: 1px solid var(--gray-100);
  display: flex; align-items: center; justify-content: space-between;
}
.card-title { font-size: 13px; font-weight: 700; color: var(--black); }
.card-sub   { font-size: 11px; color: var(--gray-400); margin-top: 2px; }

.stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
.stat-card {
  background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r3);
  padding: 20px 22px; position: relative; box-shadow: var(--sh-xs);
  transition: box-shadow .2s, border-color .2s; overflow: hidden;
}
.stat-card:hover { box-shadow: var(--sh-sm); border-color: var(--gold-rim); }
.stat-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; }
.stat-card:nth-child(1)::after { background: linear-gradient(90deg, var(--gold-dk), var(--gold), var(--gold-lt)); }
.stat-card:nth-child(2)::after { background: linear-gradient(90deg, #1a6b46, #2da66e, #52c896); }
.stat-card:nth-child(3)::after { background: linear-gradient(90deg, #c05020, #e07040, #f0a070); }
.stat-ico { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 14px; }
.stat-ico.gold-ico   { background: var(--gold-xs); border: 1px solid var(--gold-md); }
.stat-ico.green-ico  { background: rgba(45,166,110,.08); border: 1px solid rgba(45,166,110,.2); }
.stat-ico.orange-ico { background: rgba(224,112,64,.08); border: 1px solid rgba(224,112,64,.2); }
.stat-label { font-size: 11px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: .6px; }
.stat-val   { font-size: 32px; font-weight: 800; color: var(--black); line-height: 1.1; margin: 5px 0 10px; letter-spacing: -.5px; }
.stat-val .curr { font-size: 16px; color: var(--gray-400); font-weight: 500; vertical-align: top; margin-right: 1px; }
.stat-tag { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 5px; }
.stat-tag.up      { background: rgba(45,166,110,.08); color: #1a8050; }
.stat-tag.dn      { background: rgba(220,80,60,.07); color: #c04030; }
.stat-tag.neutral { background: var(--gray-100); color: var(--gray-500); }

/* Inventory & table styles */
.inv-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.filter-chips { display: flex; gap: 6px; align-items: center; }
.chip { padding: 5px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 500; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-500); cursor: pointer; transition: all .12s; }
.chip:hover { border-color: var(--gold-rim); color: var(--gold-dk); }
.chip.active { background: var(--black); border-color: var(--black); color: var(--gold-lt); }
.inv-table-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r3); overflow: hidden; box-shadow: var(--sh-xs); }
table.inv-tbl { width: 100%; border-collapse: collapse; }
.inv-tbl thead tr { background: var(--gray-50); border-bottom: 1px solid var(--gray-200); }
.inv-tbl th { padding: 10px 16px; text-align: left; font-size: 10.5px; font-weight: 700; color: var(--gray-500); text-transform: uppercase; letter-spacing: .8px; }
.inv-tbl tbody tr { border-bottom: 1px solid var(--gray-100); transition: background .1s; }
.inv-tbl tbody tr:last-child { border-bottom: none; }
.inv-tbl tbody tr:hover { background: var(--gray-50); }
.inv-tbl td { padding: 13px 16px; font-size: 12.5px; color: var(--black); vertical-align: middle; }
.badge { display: inline-flex; align-items: center; gap: 4.5px; padding: 3.5px 9px; border-radius: 5px; font-size: 11px; font-weight: 600; }
.badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
.badge-ready   { background: var(--gold-xs); color: var(--gold-dk); border: 1px solid var(--gold-md); }
.badge-ready::before   { background: var(--gold); }
.badge-out     { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); }
.badge-out::before     { background: var(--gray-400); }
.badge-laundry { background: rgba(59,130,246,.07); color: #2563eb; border: 1px solid rgba(59,130,246,.2); }
.badge-laundry::before { background: #60a5fa; }
.badge-damaged { background: rgba(220,80,60,.07); color: #c0392b; border: 1px solid rgba(220,80,60,.2); }
.badge-damaged::before { background: #e87060; animation: blink 1.4s infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }
.row-acts { display: flex; gap: 5px; opacity: 0; transition: opacity .12s; }
.inv-tbl tbody tr:hover .row-acts { opacity: 1; }
.row-btn { width: 27px; height: 27px; border-radius: 5px; border: 1px solid var(--gray-200); background: var(--white); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; color: var(--gray-500); transition: all .12s; text-decoration: none; }
.row-btn:hover { border-color: var(--gold-rim); color: var(--gold-dk); background: var(--gold-xs); }
.tbl-footer { padding: 11px 16px; border-top: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; }
.pg-info { font-size: 11.5px; color: var(--gray-400); }
.pg-btns { display: flex; gap: 3px; }
.pg-btn { width: 28px; height: 28px; border-radius: 5px; border: 1px solid var(--gray-200); background: var(--white); font-size: 11.5px; font-family: var(--ff); color: var(--gray-500); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .12s; }
.pg-btn:hover { border-color: var(--gold-rim); color: var(--gold-dk); }
.pg-btn.active { background: var(--black); border-color: var(--black); color: var(--gold-lt); font-weight: 700; }

/* Form & nota styles */
.trx-layout   { display: grid; grid-template-columns: 1fr 300px; gap: 18px; }
.form-card    { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r3); overflow: hidden; box-shadow: var(--sh-xs); }
.form-sect    { padding: 18px 20px; border-bottom: 1px solid var(--gray-100); }
.form-sect:last-child { border-bottom: none; }
.form-sect-lbl { font-size: 10.5px; font-weight: 700; color: var(--gray-400); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.form-sect-lbl::after { content: ''; flex: 1; height: 1px; background: var(--gray-100); }
.fgrid  { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; }
.f-full { grid-column: 1 / -1; }
.field  { display: flex; flex-direction: column; gap: 5px; }
.flbl   { font-size: 11.5px; font-weight: 600; color: var(--gray-600); }
.finput, .fselect {
  background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r2);
  padding: 8.5px 12px; color: var(--black); font-size: 13.5px; font-family: var(--ff);
  outline: none; transition: border-color .18s, box-shadow .18s; box-shadow: var(--sh-xs); width: 100%;
}
.finput:focus, .fselect:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-xs); }
.finput::placeholder { color: var(--gray-300); font-size: 13px; }
.finput[readonly] { background: var(--gray-50); color: var(--gray-500); cursor: default; }
.nota-panel { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r3); overflow: hidden; box-shadow: var(--sh-xs); position: sticky; top: 0; align-self: start; }
.nota-preview-hd { padding: 13px 16px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; }
.nota-preview-title { font-size: 12.5px; font-weight: 700; color: var(--black); }
.nota-paper    { margin: 14px; border: 1px solid var(--gray-200); border-radius: var(--r2); overflow: hidden; }
.nota-top      { background: var(--black); padding: 14px 16px; }
.nota-brand    { font-family: var(--ff-serif); font-style: italic; font-size: 20px; color: var(--gold-lt); letter-spacing: .3px; }
.nota-tagline  { font-size: 8.5px; color: rgba(255,255,255,.25); letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
.nota-trx-label { font-size: 8.5px; color: rgba(255,255,255,.3); margin-top: 8px; text-transform: uppercase; letter-spacing: 1px; }
.nota-trx-num  { font-family: var(--ff-mono); font-size: 12px; color: var(--gold-lt); margin-top: 1px; }
.nota-body      { padding: 13px 14px; background: var(--white); }
.nota-row       { display: flex; justify-content: space-between; padding: 5px 0; font-size: 11.5px; border-bottom: 1px solid var(--gray-100); }
.nota-row:last-child { border-bottom: none; }
.nota-key       { color: var(--gray-500); }
.nota-val       { color: var(--black); font-weight: 500; text-align: right; }
.nota-val.gold  { color: var(--gold-dk); font-weight: 700; font-family: var(--ff-mono); }
.nota-total-box  { background: var(--gray-50); border: 1px solid var(--gold-md); border-radius: var(--r2); padding: 10px 12px; margin: 10px 0; display: flex; justify-content: space-between; align-items: center; }
.nota-total-lbl  { font-size: 12px; font-weight: 700; color: var(--black); }
.nota-total-val  { font-family: var(--ff-mono); font-size: 15px; font-weight: 700; color: var(--gold-dk); }
.nota-footer     { font-size: 10px; color: var(--gray-400); text-align: center; padding-top: 8px; border-top: 1px dashed var(--gray-200); line-height: 1.6; }
.nota-gen-btn { margin: 0 14px 14px; width: calc(100% - 28px); padding: 11px; background: var(--black); border: 1px solid var(--gold-rim); border-radius: var(--r2); color: var(--gold-lt); font-size: 13px; font-weight: 700; font-family: var(--ff); cursor: pointer; letter-spacing: .3px; transition: all .18s; position: relative; overflow: hidden; box-shadow: var(--sh-xs); }
.nota-gen-btn::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(201,168,76,.1) 0%, transparent 60%); }
.nota-gen-btn:hover { background: var(--black3); box-shadow: var(--sh-gold); }
.reports-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px; }
.period-toggle { display: flex; background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: var(--r2); padding: 2px; gap: 2px; }
.pt-btn { padding: 5px 12px; border-radius: 5px; font-size: 11.5px; font-weight: 500; cursor: pointer; color: var(--gray-500); transition: all .12s; }
.pt-btn.active { background: var(--white); color: var(--black); font-weight: 700; box-shadow: var(--sh-xs); }
table.report-tbl { width: 100%; border-collapse: collapse; }
.report-tbl thead tr { background: var(--gray-50); border-bottom: 1px solid var(--gray-200); }
.report-tbl th  { padding: 9px 16px; text-align: left; font-size: 10.5px; font-weight: 700; color: var(--gray-500); text-transform: uppercase; letter-spacing: .8px; }
.report-tbl tbody tr { border-bottom: 1px solid var(--gray-100); transition: background .1s; }
.report-tbl tbody tr:last-child { border-bottom: none; }
.report-tbl tbody tr:hover { background: var(--gray-50); }
.report-tbl td  { padding: 11px 16px; font-size: 12.5px; color: var(--black); vertical-align: middle; }
.td-mono { font-family: var(--ff-mono); font-size: 12px; }
.td-gold { color: var(--gold-dk); font-weight: 700; }
.export-row { margin-top: 16px; background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r3); padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--sh-xs); }
.export-title { font-size: 13.5px; font-weight: 700; color: var(--black); }
.export-sub   { font-size: 11.5px; color: var(--gray-400); margin-top: 3px; }
.export-btns  { display: flex; gap: 8px; }

/* Modal overlay */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,.65); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all .25s ease; }
.modal-overlay.show { opacity: 1; visibility: visible; }
.modal-popup { background: var(--white); border-radius: 16px; width: 90%; max-width: 360px; text-align: center; padding: 28px 24px; transform: scale(.9) translateY(8px); transition: transform .25s cubic-bezier(.34,1.4,.64,1); border-top: 3px solid var(--gold); box-shadow: 0 20px 50px rgba(0,0,0,.2); }
.modal-overlay.show .modal-popup { transform: scale(1) translateY(0); }
.modal-popup-icon  { width: 56px; height: 56px; background: rgba(220,80,60,.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 26px; border: 1px solid rgba(220,80,60,.15); }
.modal-popup-title { font-size: 17px; font-weight: 700; color: #c0392b; margin-bottom: 8px; }
.modal-popup-msg   { font-size: 12.5px; color: var(--gray-500); margin-bottom: 20px; line-height: 1.6; }
.modal-popup-close { background: var(--black); color: var(--gold-lt); border: 1px solid var(--gold-rim); padding: 9px 28px; border-radius: var(--r2); font-weight: 700; font-size: 13px; cursor: pointer; transition: all .15s; }
.modal-popup-close:hover { background: var(--black3); box-shadow: var(--sh-gold); }

/* Utility classes */
.flex        { display: flex; }
.items-center{ align-items: center; }
.gap-2       { gap: 8px; }
.ml-auto     { margin-left: auto; }
.font-mono   { font-family: var(--ff-mono); }
.text-muted  { color: var(--gray-400); }
.text-gold   { color: var(--gold-dk); }
.mb-4        { margin-bottom: 16px; }
.mb-5        { margin-bottom: 20px; }

/* ══════════════════════════════════════════════
   RESPONSIVE — Mobile Overlay Sidebar & Grids
══════════════════════════════════════════════ */

/* Semi-transparent backdrop shown when mobile sidebar is open */
.sidebar-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.52);
  z-index: 25;
  opacity: 0;
  pointer-events: none;
  transition: opacity .28s ease;
}
body.mob-sidebar-open .sidebar-backdrop {
  opacity: 1;
  pointer-events: all;
}

/* Responsive table wrapper — add this class around every <table> */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 0;
}

/* ── Mobile (≤ 768px) ─────────────────────────────── */
@media (max-width: 768px) {

  /* Sidebar becomes a fixed full-height overlay that slides in from left */
  .sidebar {
    position: fixed;
    top: 0; left: 0; bottom: 0;
    width: var(--sidebar) !important;
    min-width: var(--sidebar) !important;
    transform: translateX(-100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1) !important;
    z-index: 40;
  }
  /* Override the desktop collapsed state — on mobile width doesn't change */
  body.sidebar-collapsed .sidebar {
    width: var(--sidebar) !important;
    min-width: var(--sidebar) !important;
    transform: translateX(-100%);
  }
  body.mob-sidebar-open .sidebar {
    transform: translateX(0) !important;
  }

  /* Topbar */
  .topbar { padding: 0 12px; gap: 8px; }
  .tb-search { display: none; }
  .tb-breadcrumb { font-size: 11.5px; gap: 4px; }

  /* Content area padding */
  .content { padding: 14px; }

  /* Stat cards: single column on mobile */
  .stat-row { grid-template-columns: 1fr; }

  /* Form grids: single column */
  .fgrid { grid-template-columns: 1fr; }
  .f-full { grid-column: 1; }

  /* Report grid: single column */
  .reports-grid { grid-template-columns: 1fr; }

  /* Transaction form + nota: stack vertically */
  .trx-layout { grid-template-columns: 1fr; }
  .nota-panel { position: static; }

  /* Export action row: stack on mobile */
  .export-row { flex-direction: column; align-items: flex-start; gap: 12px; }
  .export-btns { width: 100%; }
  .export-btns .btn-outline,
  .export-btns .btn-gold { flex: 1; justify-content: center; }

  /* Page title */
  .pg-title { font-size: 17px; }
  .pg-head  { margin-bottom: 16px; }

  /* Inventory toolbar */
  .inv-toolbar { flex-direction: column; align-items: flex-start; }
  .filter-chips { flex-wrap: wrap; }

  /* Tables need minimum width so they scroll horizontally instead of wrapping */
  .inv-tbl   { min-width: 560px; }
  .report-tbl { min-width: 640px; }
}

/* ── Tablet (769px – 1024px) ──────────────────────── */
@media (min-width: 769px) and (max-width: 1024px) {
  .stat-row    { grid-template-columns: repeat(2, 1fr); }
  .reports-grid { grid-template-columns: 1fr; }
  .trx-layout  { grid-template-columns: 1fr; }
  .nota-panel  { position: static; }
  .content     { padding: 18px; }
}

/* ── Desktop sidebar restore (>768px): no overlay behaviour ── */
@media (min-width: 769px) {
  .sidebar-backdrop { display: none !important; }
  body.mob-sidebar-open .sidebar { transform: none !important; }
}


</style>
</head>
<body>

  <!-- Backdrop for mobile sidebar overlay -->
  <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>

  {{-- Progress bar untuk SPA navigation --}}
  <div id="spa-bar" style="position:fixed;top:0;left:0;height:2.5px;background:var(--gold);
       width:0%;z-index:9999;opacity:0;pointer-events:none"></div>

@php
    $userRole    = session('user')['role'] ?? null;
    $userName    = session('user')['nama_lengkap'] ?? 'User';
    $userInitial = strtoupper(substr($userName, 0, 1));
    $totalBarang = \App\Models\Barang::count();
@endphp

{{-- ════════════════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════════════════ --}}
<aside class="sidebar" id="appSidebar">

    <div class="s-logo">
        <div class="logo-icon">N</div>
        <div class="logo-words">
            <div class="logo-name">NM Gallery</div>
            <div class="logo-sub">SIM Baju Bodo</div>
        </div>
    </div>

    <div class="s-label">Menu</div>

    <a href="{{ route('barang.index') }}"
       class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor">
            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
        </svg>
        Inventaris &amp; Stok
        <span class="nav-pill info">{{ $totalBarang }}</span>
    </a>

    @if($userRole === 'Karyawan')
    <a href="{{ route('transaksi.index') }}"
       class="nav-item {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
        </svg>
        Transaksi &amp; E-Nota
    </a>
    @endif

    @if($userRole === 'Owner')
    <a href="{{ route('laporan') }}"
       class="nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
        </svg>
        Laporan Keuangan
    </a>
    @endif

    <div class="s-label" style="margin-top:8px">Lainnya</div>

    <a href="{{ route('pelanggan.index') }}"
       class="nav-item {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
        </svg>
        Pelanggan
    </a>

    @if($userRole === 'Owner')
    <a href="{{ route('pengaturan') }}"
       class="nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
        </svg>
        Pengaturan
    </a>
    @endif

    {{-- ── FOOTER PROFIL dengan Dropdown ── --}}
    <div class="s-footer">
        <form action="{{ route('logout') }}" method="POST" id="logoutForm">@csrf</form>

        {{-- Dropdown — muncul ke atas saat profil diklik --}}
        <div class="profile-dropdown" id="profileDropdown">
            <div class="pd-header">
                <div class="pd-role-badge">{{ $userRole ?? '—' }}</div>
                <div class="pd-name">{{ $userName }}</div>
                <div class="pd-username">@{{ session('user')['username'] ?? '' }}</div>
            </div>
            <div class="pd-actions">
                {{-- Tombol logout di dalam dropdown --}}
                <button type="button" class="pd-btn danger"
                    onclick="confirmLogout()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar dari Sistem
                </button>
            </div>
        </div>

        {{-- Tombol profil — trigger dropdown, bukan langsung logout --}}
        <div class="s-user" id="profileTrigger" onclick="toggleProfileDropdown()"
             role="button" aria-haspopup="true" aria-expanded="false">
            <div class="s-ava">{{ $userInitial }}</div>
            <div style="min-width:0">
                <div class="s-uname">{{ $userName }}</div>
                <div class="s-urole">{{ $userRole ?? '—' }}</div>
            </div>
            {{-- Chevron sebagai visual affordance bahwa ada dropdown --}}
            <svg class="s-chevron" width="12" height="12" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"/>
            </svg>
        </div>
    </div>

</aside>

{{-- ════════════════════════════════════════════════════
     MAIN AREA
════════════════════════════════════════════════════ --}}
<div class="main">

    <header class="topbar">

        {{-- ── Tombol Toggle Sidebar ── --}}
        <button class="sidebar-toggle-btn" id="sidebarToggle"
                onclick="toggleSidebar()"
                title="Sembunyikan / tampilkan sidebar">
            <div class="hamburger"></div>
        </button>

        {{-- Breadcrumb --}}
        <nav class="tb-breadcrumb">
            <span>NM Gallery</span>
            <span class="sep"> / </span>
            <span class="cur">@yield('breadcrumb', 'Laporan')</span>
        </nav>

        {{-- Grup tombol kanan --}}
        <div class="tb-right">

            @if($userRole == 'Karyawan')
            <a href="{{ route('transaksi.create') }}" class="btn-gold">+ Sewa Baru</a>
            @endif

            {{--
                Tombol Panduan "?"
                Ganti href di bawah dengan URL panduan Anda:
                  - Route Laravel  : route('panduan')
                  - File statis    : '/panduan.pdf'
                  - Dokumen Google : 'https://docs.google.com/...'
                Atribut target="_blank" membuka panduan di tab baru
                agar pengguna tidak meninggalkan aplikasi.
            --}}
            <a href="/panduan"
               target="_blank"
               rel="noopener noreferrer"
               class="help-btn"
               title="Buka Buku Panduan">
                ?
            </a>

        </div>
    </header>

    <div class="content">
        @yield('content')
    </div>
</div>

{{-- Access Denied Modal --}}
<div class="modal-overlay" id="accessModal">
    <div class="modal-popup">
        <div class="modal-popup-icon"><i class="bi bi-shield-lock-fill" style="font-size:26px;color:#c0392b"></i></div>
        <div class="modal-popup-title">Akses Ditolak</div>
        <div class="modal-popup-msg" id="accessModalMsg">Anda tidak memiliki akses ke halaman ini.</div>
        <button class="modal-popup-close" onclick="closeAccessModal()">OK, Mengerti</button>
    </div>
</div>

<script>
/* ════════════════════════════════════════════════════
   1. SIDEBAR TOGGLE
   ════════════════════════════════════════════════════
   Strategi: tambah/hapus class `sidebar-collapsed` pada <body>.
   CSS yang mengontrol transisi width sudah didefinisikan di atas.
   localStorage dipakai agar preferensi bertahan saat user reload
   atau navigasi antar halaman (SPA router mempertahankan layout
   karena tidak me-reload <body>).
*/
(function initSidebarToggle() {
    // Pulihkan state terakhir saat halaman pertama kali dimuat
    if (localStorage.getItem('sidebar-collapsed') === '1') {
        document.body.classList.add('sidebar-collapsed');
    }
})();

function isMobile() { return window.innerWidth <= 768; }

function closeMobileSidebar() {
    document.body.classList.remove('mob-sidebar-open');
}

function toggleSidebar() {
    if (isMobile()) {
        // On mobile: slide-in/out overlay — don't touch localStorage
        document.body.classList.toggle('mob-sidebar-open');
    } else {
        // On desktop: classic collapse (width → 0) — persist preference
        const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed ? '1' : '0');
    }
}

// Close mobile sidebar when window resizes to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) document.body.classList.remove('mob-sidebar-open');
});

/* ════════════════════════════════════════════════════
   2. PROFILE DROPDOWN
   ════════════════════════════════════════════════════
   Logika penting yang perlu dipahami:
   - "click outside to close": kita pasang satu event listener pada
     document yang akan menutup dropdown KECUALI jika klik terjadi
     di dalam #profileTrigger atau #profileDropdown itu sendiri.
   - Menggunakan stopPropagation di dalam elemen dropdown akan
     mencegah document listener menangkap klik tersebut.
*/
const profileTrigger  = document.getElementById('profileTrigger');
const profileDropdown = document.getElementById('profileDropdown');

function toggleProfileDropdown() {
    const isOpen = profileDropdown.classList.toggle('open');
    profileTrigger.classList.toggle('active', isOpen);
    profileTrigger.setAttribute('aria-expanded', isOpen);
}

function closeProfileDropdown() {
    profileDropdown.classList.remove('open');
    profileTrigger.classList.remove('active');
    profileTrigger.setAttribute('aria-expanded', 'false');
}

// Cegah klik di dalam dropdown merambat ke document listener
profileDropdown.addEventListener('click', e => e.stopPropagation());
profileTrigger.addEventListener('click', e => e.stopPropagation());

// Tutup dropdown saat klik di mana saja di luar elemen tersebut
document.addEventListener('click', () => closeProfileDropdown());

// Tutup dropdown saat Escape ditekan (aksesibilitas keyboard)
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeProfileDropdown();
});

function confirmLogout() {
    closeProfileDropdown();
    // Dialog konfirmasi sebelum logout agar tidak terjadi "accidental logout"
    if (confirm('Yakin ingin keluar dari sistem NM Gallery?')) {
        document.getElementById('logoutForm').submit();
    }
}

/* ════════════════════════════════════════════════════
   3. SPA ROUTER (tidak berubah dari versi sebelumnya)
   ════════════════════════════════════════════════════ */
if (!window.__spaReady) {
    window.__spaReady = true;

    const bar = document.getElementById('spa-bar');
    let barTimer;

    function barStart() {
        clearTimeout(barTimer);
        bar.style.transition = 'width .6s ease';
        bar.style.opacity    = '1';
        bar.style.width      = '40%';
    }
    function barDone() {
        bar.style.transition = 'width .15s ease';
        bar.style.width      = '100%';
        barTimer = setTimeout(() => {
            bar.style.transition = 'opacity .3s ease';
            bar.style.opacity    = '0';
            setTimeout(() => { bar.style.width = '0%'; }, 350);
        }, 200);
    }

    function reExecScripts(container) {
        const scripts = [...container.querySelectorAll('script')];

        const externalScripts = scripts.filter(s => s.src);
        const inlineScripts   = scripts.filter(s => !s.src);

        if (externalScripts.length === 0) {
            // Tidak ada CDN, langsung jalankan semua script inline
            inlineScripts.forEach(execInlineScript);
            return;
        }

        let loadedCount = 0;
        externalScripts.forEach(old => {
            const s = document.createElement('script');
            [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));

            s.onload = s.onerror = () => {
                loadedCount++;
                if (loadedCount === externalScripts.length) {
                    // Semua CDN sudah siap, baru jalankan script inline
                    inlineScripts.forEach(execInlineScript);
                }
            };

            old.parentNode.replaceChild(s, old);
        });
    }

    function execInlineScript(old) {
    const text = old.textContent;
    const s = document.createElement('script');

    const fnPattern = /^(?:async\s+)?function\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*\(/gm;
    const fnNames = new Set();
    let match;
    while ((match = fnPattern.exec(text)) !== null) {
        fnNames.add(match[1]);
    }

    const exposures = [...fnNames]
        .map(n => `try{ if(typeof ${n}==='function') window.${n}=${n}; }catch(e){}`)
        .join('\n');

    s.textContent = `(function(){\n${text}\n\n${exposures}\n})();`;

    old.parentNode.replaceChild(s, old);
    }

    function syncNav(url) {
        const path = new URL(url).pathname.replace(/\/$/, '') || '/';
        document.querySelectorAll('a.nav-item').forEach(link => {
            const lp = new URL(link.href, location.origin).pathname.replace(/\/$/, '') || '/';
            link.classList.toggle('active', path === lp || (lp !== '/' && path.startsWith(lp)));
        });
    }

    async function go(url, push = true) {
        barStart();
        closeProfileDropdown(); // tutup dropdown saat navigasi
        const contentEl = document.querySelector('.content');
        try {
            const res = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-SPA': '1', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok || !res.headers.get('content-type')?.includes('text/html'))
                throw new Error('bad');

            const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
            const newContent = doc.querySelector('.content');
            if (!newContent) throw new Error('no-content');

            contentEl.style.transition = 'opacity .1s ease';
            contentEl.style.opacity    = '0';
            await new Promise(r => setTimeout(r, 80));

            contentEl.innerHTML = newContent.innerHTML;

            document.title = doc.title;
            const nc = doc.querySelector('.tb-breadcrumb .cur');
            const oc = document.querySelector('.tb-breadcrumb .cur');
            if (nc && oc) oc.textContent = nc.textContent;

            syncNav(url);
            if (push) history.pushState({ spa: url }, doc.title, url);

            reExecScripts(contentEl);

            contentEl.scrollTop    = 0;
            contentEl.style.opacity = '1';
            barDone();
        } catch (_) {
            barDone();
            window.location.href = url;
        }
    }

    document.addEventListener('click', e => {
        const link = e.target.closest('a.nav-item');
        if (!link) return;
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript')) return;
        const url = new URL(href, location.origin);
        if (url.origin !== location.origin) return;
        e.preventDefault();
        if (url.pathname === location.pathname) return;
        go(url.href);
    });

    window.addEventListener('popstate', e => {
        go(e.state?.spa || location.href, false);
    });

    history.replaceState({ spa: location.href }, document.title, location.href);
}

/* ════════════════════════════════════════════════════
   4. ACCESS DENIED MODAL HELPERS
   ════════════════════════════════════════════════════ */
function showAccessDenied(pageName, requiredRole) {
    document.getElementById('accessModalMsg').innerHTML =
        `Anda tidak dapat mengakses <strong>${pageName}</strong>.<br><br>
         Halaman ini hanya untuk <strong>${requiredRole}</strong>.`;
    document.getElementById('accessModal').classList.add('show');
}
function closeAccessModal() {
    document.getElementById('accessModal').classList.remove('show');
}
document.getElementById('accessModal').addEventListener('click', function(e) {
    if (e.target === this) closeAccessModal();
});
</script>

@stack('scripts')
</body>
</html>