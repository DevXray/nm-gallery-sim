<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NM Gallery SIM - @yield('title', 'Dashboard')</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* GLOBAL STYLES */
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f4f4f5;
    color: #0a0a0a;
    height: 100vh;
    display: flex;
    overflow: hidden;
}
/* SIDEBAR */
.sidebar {
    width: 230px;
    min-width: 230px;
    background: #0a0a0a;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    z-index: 30;
}
.s-logo {
    padding: 22px 18px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    gap: 11px;
}
.logo-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #e0c06e, #C9A84C, #a07830);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Instrument Serif', serif;
    font-size: 18px;
    font-style: italic;
    color: #0a0a0a;
}
.logo-name {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
}
.logo-sub {
    font-size: 9.5px;
    color: rgba(255,255,255,0.3);
    text-transform: uppercase;
    margin-top: 2px;
}
.s-label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.2);
    padding: 16px 18px 6px;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 12px;
    margin: 1px 8px;
    border-radius: 8px;
    color: rgba(255,255,255,0.45);
    font-size: 12.5px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.15s;
}
.nav-item:hover {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.75);
}
.nav-item.active {
    background: linear-gradient(90deg, rgba(201,168,76,0.18) 0%, rgba(201,168,76,0.06) 100%);
    color: #e0c06e;
    font-weight: 600;
}
.nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    width: 3px;
    height: 100%;
    background: #C9A84C;
}
.n-ico {
    width: 15px;
    height: 15px;
    opacity: 0.6;
}
.nav-pill.info {
    margin-left: auto;
    background: rgba(201,168,76,0.08);
    color: #e0c06e;
    border: 1px solid rgba(201,168,76,0.25);
    padding: 1.5px 6px;
    border-radius: 8px;
    font-size: 9.5px;
}
.s-footer {
    margin-top: auto;
    padding: 12px 8px;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.s-user {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 11px;
    border-radius: 8px;
    cursor: pointer;
}
.s-user:hover {
    background: rgba(255,255,255,0.05);
}
.s-ava {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a07830, #C9A84C);
    border: 1.5px solid rgba(201,168,76,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 700;
    color: #0a0a0a;
}
.s-uname {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.75);
}
.s-urole {
    font-size: 10px;
    color: rgba(255,255,255,0.25);
}
/* MAIN CONTENT */
.main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #f4f4f5;
}
.topbar {
    height: 52px;
    background: #ffffff;
    border-bottom: 1px solid #e4e4e7;
    display: flex;
    align-items: center;
    padding: 0 24px;
    gap: 14px;
}
.tb-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #a1a1aa;
}
.tb-breadcrumb .cur {
    color: #0a0a0a;
    font-weight: 600;
}
.tb-search {
    flex: 1;
    max-width: 320px;
    display: flex;
    align-items: center;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    border-radius: 8px;
    padding: 0 11px;
    gap: 7px;
}
.tb-search input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    padding: 7px 0;
    font-size: 12.5px;
}
.tb-right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-gold {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 16px;
    background: #0a0a0a;
    color: #e0c06e;
    border: 1px solid rgba(201,168,76,0.28);
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}
.btn-gold:hover {
    background: #1a1a1a;
    box-shadow: 0 2px 12px rgba(201,168,76,0.18);
}
.content {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
}
.pg-head {
    margin-bottom: 22px;
}
.pg-title {
    font-size: 20px;
    font-weight: 700;
    color: #0a0a0a;
}
.pg-sub {
    font-size: 12.5px;
    color: #71717a;
    margin-top: 4px;
}
.card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    overflow: hidden;
}
.card.gold-top {
    border-top: 2px solid #C9A84C;
}
.card-head {
    padding: 14px 18px;
    border-bottom: 1px solid #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.card-title {
    font-size: 13px;
    font-weight: 700;
    color: #0a0a0a;
}
.stat-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.stat-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 20px 22px;
    position: relative;
}
.stat-ico {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(201,168,76,0.08);
    border: 1px solid rgba(201,168,76,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-bottom: 14px;
}
.stat-label {
    font-size: 11px;
    font-weight: 600;
    color: #71717a;
    text-transform: uppercase;
}
.stat-val {
    font-size: 32px;
    font-weight: 800;
    color: #0a0a0a;
    margin: 5px 0 10px;
}
.stat-tag {
    display: inline-flex;
    background: #f4f4f5;
    color: #71717a;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 11px;
}
.qa-section {
    margin-bottom: 20px;
}
.qa-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #71717a;
    text-transform: uppercase;
    margin-bottom: 12px;
}
.qa-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}
.qa-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    cursor: pointer;
}
.qa-card.primary {
    background: #0a0a0a;
    border-color: #0a0a0a;
}
.qa-label {
    font-size: 13px;
    font-weight: 600;
    color: #0a0a0a;
}
.qa-card.primary .qa-label {
    color: #ffffff;
}
.qa-desc {
    font-size: 10.5px;
    color: #a1a1aa;
}
.qa-card.primary .qa-desc {
    color: rgba(255,255,255,0.4);
}
.recent-list {
    padding: 2px 0;
}
.recent-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 18px;
    border-bottom: 1px solid #f4f4f5;
}
.ri-ava {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0a0a0a;
    border: 1.5px solid rgba(201,168,76,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 700;
    color: #e0c06e;
}
.ri-name {
    font-size: 12.5px;
    font-weight: 600;
    color: #0a0a0a;
}
.ri-detail {
    font-size: 11px;
    color: #a1a1aa;
}
.ri-amount {
    margin-left: auto;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    color: #0a0a0a;
}
.ri-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    margin-top: 2px;
    display: block;
    text-align: right;
}
.ri-badge.out {
    background: rgba(201,168,76,0.08);
    color: #a07830;
    border: 1px solid rgba(201,168,76,0.25);
}
</style>
</head>
<body>

<aside class="sidebar">
    <div class="s-logo">
        <div class="logo-icon">N</div>
        <div class="logo-words">
            <div class="logo-name">NM Gallery</div>
            <div class="logo-sub">SIM Baju Bodo</div>
        </div>
    </div>
    <div class="s-label">Menu</div>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm7 0a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1V4zM3 13a1 1 0 011-1h5a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm7 0a1 1 0 011-1h5a1 1 0 011 1v3a1 1 0 01-1 1h-5a1 1 0 01-1-1v-3z"/></svg>
        Dashboard
    </a>
    <a href="#" class="nav-item">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
        Inventaris & Stok
        <span class="nav-pill info">{{ $totalBarang ?? 0 }}</span>
    </a>
    <a href="#" class="nav-item">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
        Transaksi & E-Nota
    </a>
    <a href="#" class="nav-item">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
        Laporan Keuangan
    </a>
    <div class="s-label" style="margin-top:8px">Lainnya</div>
    <a href="#" class="nav-item">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/></svg>
        Pelanggan
    </a>
    <a href="#" class="nav-item">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
        Pengaturan
    </a>
    <div class="s-footer">
        <form action="#" method="POST">
            <div class="s-user">
                <div class="s-ava">N</div>
                <div>
                    <div class="s-uname">Nurhayati</div>
                    <div class="s-urole">Owner · Admin</div>
                </div>
            </div>
        </form>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <nav class="tb-breadcrumb">
            <span>NM Gallery</span>
            <span class="sep"> / </span>
            <span class="cur">@yield('breadcrumb', 'Dashboard')</span>
        </nav>
        <div class="tb-search">
            <span>🔍</span>
            <input placeholder="Cari baju, pelanggan, transaksi...">
        </div>
        <div class="tb-right">
            <button class="btn-gold">+ Sewa Baru</button>
        </div>
    </header>
    <div class="content">
        @yield('content')
    </div>
</div>

</body>
</html>
