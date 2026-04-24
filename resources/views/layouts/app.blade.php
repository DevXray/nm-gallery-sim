<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NM Gallery SIM — @yield('title', 'Dashboard')</title>
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
  --sidebar: 230px;

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
   SIDEBAR
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
.logo-words { min-width: 0; }
.logo-name { font-size: 14px; font-weight: 700; color: var(--white); letter-spacing: .1px; line-height: 1.2; }
.logo-sub  { font-size: 9.5px; color: rgba(255,255,255,.3); letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; font-weight: 500; }

.s-label {
  font-size: 9px; font-weight: 700;
  letter-spacing: 2px; text-transform: uppercase;
  color: rgba(255,255,255,.2);
  padding: 16px 18px 6px;
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

.s-footer {
  margin-top: auto;
  padding: 12px 8px;
  border-top: 1px solid rgba(255,255,255,.06);
}
.s-user {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 11px;
  border-radius: var(--r2);
  cursor: pointer;
  transition: background .15s;
}
.s-user:hover { background: rgba(255,255,255,.05); }
.s-ava {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold-dk), var(--gold));
  border: 1.5px solid rgba(201,168,76,.5);
  display: flex; align-items: center; justify-content: center;
  font-size: 11.5px; font-weight: 700;
  color: var(--black); flex-shrink: 0;
}
.s-uname { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.75); }
.s-urole { font-size: 10px; color: rgba(255,255,255,.25); margin-top: 1px; }

/* ══════════════════════════════════════════════
   MAIN AREA
══════════════════════════════════════════════ */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: var(--gray-100);
}

.topbar {
  height: 52px; min-height: 52px;
  background: var(--white);
  border-bottom: 1px solid var(--gray-200);
  display: flex; align-items: center;
  padding: 0 24px; gap: 14px;
  box-shadow: var(--sh-xs);
  position: relative; z-index: 10;
}
.tb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--gray-400); }
.tb-breadcrumb .sep { opacity: .5; }
.tb-breadcrumb .cur { color: var(--black); font-weight: 600; }

.tb-search {
  flex: 1; max-width: 320px;
  margin-left: 16px;
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

/* ══════════════════════════════════════════════
   BUTTONS
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
   CONTENT
══════════════════════════════════════════════ */
.content { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 24px; }

/* Page heading */
.pg-head { margin-bottom: 22px; }
.pg-title { font-size: 20px; font-weight: 700; color: var(--black); letter-spacing: -.2px; }
.pg-sub   { font-size: 12.5px; color: var(--gray-500); margin-top: 4px; }

/* Cards */
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

/* ══════════════════════════════════════════════
   STAT CARDS
══════════════════════════════════════════════ */
.stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
.stat-card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--r3);
  padding: 20px 22px;
  position: relative;
  box-shadow: var(--sh-xs);
  transition: box-shadow .2s, border-color .2s;
  overflow: hidden;
}
.stat-card:hover { box-shadow: var(--sh-sm); border-color: var(--gold-rim); }
.stat-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; }
.stat-card:nth-child(1)::after { background: linear-gradient(90deg, var(--gold-dk), var(--gold), var(--gold-lt)); }
.stat-card:nth-child(2)::after { background: linear-gradient(90deg, #1a6b46, #2da66e, #52c896); }
.stat-card:nth-child(3)::after { background: linear-gradient(90deg, #c05020, #e07040, #f0a070); }
.stat-ico {
  width: 36px; height: 36px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; margin-bottom: 14px;
}
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

/* ══════════════════════════════════════════════
   QUICK ACTIONS
══════════════════════════════════════════════ */
.qa-section  { margin-bottom: 20px; }
.qa-title    { font-size: 11.5px; font-weight: 700; color: var(--gray-500); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
.qa-grid     { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.qa-card {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--r3);
  padding: 16px 18px;
  cursor: pointer;
  transition: all .18s;
  display: flex; align-items: center; gap: 12px;
  box-shadow: var(--sh-xs);
  text-decoration: none; color: inherit;
}
.qa-card:hover { border-color: var(--gold-rim); box-shadow: var(--sh-sm); transform: translateY(-1px); }
.qa-card.primary { background: var(--black); border-color: var(--black); color: var(--white); }
.qa-card.primary:hover { background: var(--black3); box-shadow: var(--sh-gold); border-color: var(--gold-rim); }
.qa-ico { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.qa-ico.black { background: rgba(255,255,255,.08); }
.qa-ico.gold  { background: var(--gold-xs); border: 1px solid var(--gold-md); }
.qa-ico.soft  { background: var(--gray-100); border: 1px solid var(--gray-200); }
.qa-label    { font-size: 13px; font-weight: 600; }
.qa-desc     { font-size: 10.5px; color: var(--gray-400); margin-top: 1px; }
.qa-card.primary .qa-desc { color: rgba(255,255,255,.4); }

/* ══════════════════════════════════════════════
   RECENT LIST
══════════════════════════════════════════════ */
.recent-list { padding: 2px 0; }
.recent-item {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 18px;
  border-bottom: 1px solid var(--gray-100);
  transition: background .1s;
}
.recent-item:last-child { border-bottom: none; }
.recent-item:hover { background: var(--gray-50); }
.ri-ava {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: var(--black); border: 1.5px solid var(--gold-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 11.5px; font-weight: 700; color: var(--gold-lt); flex-shrink: 0;
}
.ri-name   { font-size: 12.5px; font-weight: 600; color: var(--black); }
.ri-detail { font-size: 11px; color: var(--gray-400); margin-top: 1px; }
.ri-amount { margin-left: auto; font-family: var(--ff-mono); font-size: 12px; font-weight: 500; color: var(--black); }
.ri-badge  { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-top: 2px; display: block; text-align: right; }
.ri-badge.out  { background: var(--gold-xs); color: var(--gold-dk); border: 1px solid var(--gold-md); }
.ri-badge.done { background: rgba(45,166,110,.08); color: #1a8050; }

/* ══════════════════════════════════════════════
   INVENTORY TABLE
══════════════════════════════════════════════ */
.inv-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.inv-search {
  display: flex; align-items: center; gap: 7px;
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--r2);
  padding: 0 12px; flex: 1; max-width: 340px;
  box-shadow: var(--sh-xs);
  transition: border-color .2s, box-shadow .2s;
}
.inv-search:focus-within { border-color: var(--gold-rim); box-shadow: 0 0 0 3px var(--gold-xs); }
.inv-search input { flex: 1; border: none; background: transparent; outline: none; padding: 8.5px 0; font-size: 12.5px; color: var(--black); }
.inv-search input::placeholder { color: var(--gray-400); }

.filter-chips { display: flex; gap: 6px; align-items: center; }
.chip {
  padding: 5px 12px; border-radius: 20px;
  font-size: 11.5px; font-weight: 500;
  border: 1px solid var(--gray-200);
  background: var(--white); color: var(--gray-500); cursor: pointer;
  transition: all .12s;
}
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

.baju-photo { width: 40px; height: 48px; border-radius: var(--r2); display: flex; align-items: center; justify-content: center; font-size: 22px; border: 1px solid var(--gray-200); background: var(--gray-50); flex-shrink: 0; }
.baju-cell  { display: flex; align-items: center; gap: 12px; }
.baju-name  { font-size: 13px; font-weight: 600; color: var(--black); }
.baju-code  { font-size: 10.5px; color: var(--gray-400); font-family: var(--ff-mono); margin-top: 1px; }

.color-dot-cell { display: flex; align-items: center; gap: 7px; }
.color-dot { width: 11px; height: 11px; border-radius: 50%; border: 1px solid rgba(0,0,0,.08); flex-shrink: 0; }

/* Status badges */
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

/* Row actions */
.row-acts { display: flex; gap: 5px; opacity: 0; transition: opacity .12s; }
.inv-tbl tbody tr:hover .row-acts { opacity: 1; }
.row-btn {
  width: 27px; height: 27px; border-radius: 5px;
  border: 1px solid var(--gray-200); background: var(--white);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; font-size: 12px; color: var(--gray-500);
  transition: all .12s;
}
.row-btn:hover { border-color: var(--gold-rim); color: var(--gold-dk); background: var(--gold-xs); }

.tbl-footer { padding: 11px 16px; border-top: 1px solid var(--gray-100); background: var(--gray-50); display: flex; align-items: center; justify-content: space-between; }
.pg-info { font-size: 11.5px; color: var(--gray-400); }
.pg-btns { display: flex; gap: 3px; }
.pg-btn {
  width: 28px; height: 28px; border-radius: 5px;
  border: 1px solid var(--gray-200); background: var(--white);
  font-size: 11.5px; font-family: var(--ff);
  color: var(--gray-500); cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all .12s;
}
.pg-btn:hover { border-color: var(--gold-rim); color: var(--gold-dk); }
.pg-btn.active { background: var(--black); border-color: var(--black); color: var(--gold-lt); font-weight: 700; }

/* ══════════════════════════════════════════════
   FORM STYLES
══════════════════════════════════════════════ */
.trx-layout   { display: grid; grid-template-columns: 1fr 300px; gap: 18px; }
.form-card    { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r3); overflow: hidden; box-shadow: var(--sh-xs); }
.form-sect    { padding: 18px 20px; border-bottom: 1px solid var(--gray-100); }
.form-sect:last-child { border-bottom: none; }
.form-sect-lbl {
  font-size: 10.5px; font-weight: 700;
  color: var(--gray-400); text-transform: uppercase; letter-spacing: 1.2px;
  margin-bottom: 14px;
  display: flex; align-items: center; gap: 8px;
}
.form-sect-lbl::after { content: ''; flex: 1; height: 1px; background: var(--gray-100); }

.fgrid  { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; }
.f-full { grid-column: 1 / -1; }
.field  { display: flex; flex-direction: column; gap: 5px; }
.flbl   { font-size: 11.5px; font-weight: 600; color: var(--gray-600); }

.finput, .fselect {
  background: var(--white);
  border: 1px solid var(--gray-200);
  border-radius: var(--r2);
  padding: 8.5px 12px;
  color: var(--black); font-size: 13.5px; font-family: var(--ff);
  outline: none;
  transition: border-color .18s, box-shadow .18s;
  box-shadow: var(--sh-xs);
  width: 100%;
}
.finput:focus, .fselect:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-xs); }
.finput::placeholder { color: var(--gray-300); font-size: 13px; }
.fselect { cursor: pointer; }
.finput[readonly] { background: var(--gray-50); color: var(--gray-500); cursor: default; }

/* ══════════════════════════════════════════════
   BAJU SELECTOR
══════════════════════════════════════════════ */
.baju-selector { display: grid; grid-template-columns: repeat(3, 1fr); gap: 9px; }
.bs-card {
  border: 1.5px solid var(--gray-200);
  border-radius: var(--r2);
  padding: 11px 13px;
  cursor: pointer; transition: all .15s;
  display: flex; align-items: center; gap: 10px;
}
.bs-card:hover { border-color: var(--gold-rim); background: var(--gold-xs); }
.bs-card.sel   { border-color: var(--gold); background: var(--gold-xs); box-shadow: 0 0 0 1px var(--gold-md) inset; }
.bs-check {
  width: 16px; height: 16px; border-radius: 50%;
  border: 1.5px solid var(--gray-300);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; font-size: 8px; transition: all .15s;
}
.bs-card.sel .bs-check { background: var(--gold); border-color: var(--gold); color: var(--black); font-weight: 800; }
.bs-name       { font-size: 12px; font-weight: 600; color: var(--black); }
.bs-color-line { display: flex; align-items: center; gap: 5px; font-size: 10.5px; color: var(--gray-400); margin-top: 2px; }

/* ══════════════════════════════════════════════
   E-NOTA PREVIEW PANEL
══════════════════════════════════════════════ */
.nota-panel {
  background: var(--white); border: 1px solid var(--gray-200);
  border-radius: var(--r3); overflow: hidden; box-shadow: var(--sh-xs);
  position: sticky; top: 0; align-self: start;
}
.nota-preview-hd { padding: 13px 16px; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; }
.nota-preview-title { font-size: 12.5px; font-weight: 700; color: var(--black); }

.nota-paper    { margin: 14px; border: 1px solid var(--gray-200); border-radius: var(--r2); overflow: hidden; }
.nota-top      { background: var(--black); padding: 14px 16px; }
.nota-brand    { font-family: var(--ff-serif); font-style: italic; font-size: 20px; color: var(--gold-lt); letter-spacing: .3px; text-shadow: 0 0 12px rgba(201,168,76,.4); }
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

.nota-gen-btn {
  margin: 0 14px 14px;
  width: calc(100% - 28px); padding: 11px;
  background: var(--black); border: 1px solid var(--gold-rim);
  border-radius: var(--r2); color: var(--gold-lt);
  font-size: 13px; font-weight: 700; font-family: var(--ff);
  cursor: pointer; letter-spacing: .3px;
  transition: all .18s; position: relative; overflow: hidden; box-shadow: var(--sh-xs);
}
.nota-gen-btn::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(201,168,76,.1) 0%, transparent 60%); }
.nota-gen-btn:hover { background: var(--black3); box-shadow: var(--sh-gold); }

/* ══════════════════════════════════════════════
   REPORTS
══════════════════════════════════════════════ */
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

.export-row {
  margin-top: 16px; background: var(--white);
  border: 1px solid var(--gray-200); border-radius: var(--r3);
  padding: 16px 20px;
  display: flex; align-items: center; justify-content: space-between;
  box-shadow: var(--sh-xs);
}
.export-title { font-size: 13.5px; font-weight: 700; color: var(--black); }
.export-sub   { font-size: 11.5px; color: var(--gray-400); margin-top: 3px; }
.export-btns  { display: flex; gap: 8px; }

/* ══════════════════════════════════════════════
   E-NOTA MODAL
══════════════════════════════════════════════ */
.overlay {
  position: fixed; inset: 0;
  background: rgba(10,10,10,.55);
  backdrop-filter: blur(6px);
  z-index: 500;
  display: flex; align-items: center; justify-content: center;
  opacity: 0; pointer-events: none;
  transition: opacity .2s;
}
.overlay.show { opacity: 1; pointer-events: all; }

.modal {
  background: var(--white); border: 1px solid var(--gray-200);
  border-radius: 14px; width: 420px; max-width: 95vw;
  max-height: 92vh; overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,.18), 0 4px 12px rgba(0,0,0,.08);
  transform: scale(.96) translateY(12px);
  transition: transform .22s cubic-bezier(.34,1.4,.64,1);
}
.overlay.show .modal { transform: scale(1) translateY(0); }

.modal-hd { padding: 18px 20px 14px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: space-between; background: var(--gray-50); }
.modal-hd-left { display: flex; align-items: center; gap: 10px; }
.modal-ico { width: 36px; height: 36px; border-radius: 8px; background: var(--black); display: flex; align-items: center; justify-content: center; font-family: var(--ff-serif); font-style: italic; font-size: 17px; color: var(--gold-lt); box-shadow: 0 2px 8px rgba(0,0,0,.2); }
.modal-title { font-size: 15px; font-weight: 700; color: var(--black); }
.modal-sub   { font-size: 11px; color: var(--gray-400); }
.modal-x { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--gray-200); background: var(--white); color: var(--gray-400); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all .15s; }
.modal-x:hover { border-color: var(--gray-400); color: var(--black); }

.modal-nota { margin: 16px; border: 1px solid var(--gray-200); border-radius: var(--r2); overflow: hidden; }
.modal-nota-top { background: var(--black); padding: 16px 18px; display: flex; justify-content: space-between; align-items: flex-start; }
.modal-nota-brand   { font-family: var(--ff-serif); font-style: italic; font-size: 22px; color: var(--gold-lt); text-shadow: 0 0 16px rgba(201,168,76,.4); }
.modal-nota-tagline { font-size: 8px; color: rgba(255,255,255,.25); letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
.modal-nota-addr    { font-size: 9.5px; color: rgba(255,255,255,.25); margin-top: 5px; line-height: 1.5; }
.modal-nota-trxid   { text-align: right; }
.modal-nota-trxid .label { font-size: 8.5px; color: rgba(255,255,255,.25); text-transform: uppercase; letter-spacing: 1px; }
.modal-nota-trxid .num   { font-family: var(--ff-mono); font-size: 13px; color: var(--gold-lt); margin-top: 2px; }
.modal-nota-trxid .date  { font-size: 9px; color: rgba(255,255,255,.25); margin-top: 3px; }

.modal-nota-body { padding: 14px 16px; background: var(--white); }
.nota-cust-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed var(--gray-200); }
.nota-field-k { font-size: 8.5px; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); font-weight: 700; margin-bottom: 2px; }
.nota-field-v { font-size: 12.5px; color: var(--black); font-weight: 500; }

.nota-items-tbl { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
.nota-items-tbl th { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: var(--gray-400); font-weight: 700; padding: 5px 0; border-bottom: 1px solid var(--gray-200); text-align: left; }
.nota-items-tbl th:last-child { text-align: right; }
.nota-items-tbl td { padding: 7px 0; font-size: 11.5px; color: var(--black); border-bottom: 1px solid var(--gray-100); }
.nota-items-tbl td:last-child { text-align: right; font-family: var(--ff-mono); font-size: 11px; color: var(--gold-dk); }
.nota-items-tbl tr:last-child td { border-bottom: none; }

.nota-totals { background: var(--gray-50); border: 1px solid var(--gold-md); border-radius: var(--r2); padding: 10px 12px; margin-bottom: 12px; }
.nota-tot-row { display: flex; justify-content: space-between; font-size: 11.5px; padding: 3px 0; }
.nota-tot-lbl { color: var(--gray-500); }
.nota-tot-val { font-family: var(--ff-mono); color: var(--black); }
.nota-tot-row.grand { border-top: 1px solid var(--gray-200); padding-top: 8px; margin-top: 4px; }
.nota-tot-row.grand .nota-tot-lbl { font-size: 13px; font-weight: 800; color: var(--black); }
.nota-tot-row.grand .nota-tot-val { font-size: 15px; font-weight: 800; color: var(--gold-dk); }

.nota-foot { font-size: 9.5px; color: var(--gray-400); text-align: center; border-top: 1px dashed var(--gray-200); padding-top: 10px; line-height: 1.6; }
.nota-foot b { color: var(--gold-dk); }

.modal-acts { padding: 0 16px 16px; display: flex; gap: 8px; }
.btn-print { flex: 1; padding: 10px; background: var(--black); border: 1px solid var(--gold-rim); border-radius: var(--r2); color: var(--gold-lt); font-size: 12.5px; font-weight: 700; font-family: var(--ff); cursor: pointer; transition: all .15s; }
.btn-print:hover { background: var(--black3); box-shadow: var(--sh-gold); }
.btn-share { flex: 1; padding: 10px; background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--r2); color: var(--gray-600); font-size: 12.5px; font-weight: 600; font-family: var(--ff); cursor: pointer; transition: all .15s; }
.btn-share:hover { border-color: var(--gold-rim); color: var(--gold-dk); }

/* ══════════════════════════════════════════════
   ACCESS DENIED MODAL
══════════════════════════════════════════════ */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,.65); backdrop-filter: blur(4px);
  z-index: 1000; display: flex; align-items: center; justify-content: center;
  opacity: 0; visibility: hidden; transition: all .25s ease;
}
.modal-overlay.show { opacity: 1; visibility: visible; }
.modal-popup {
  background: var(--white); border-radius: 16px;
  width: 90%; max-width: 360px; text-align: center;
  padding: 28px 24px;
  transform: scale(.9) translateY(8px);
  transition: transform .25s cubic-bezier(.34,1.4,.64,1);
  border-top: 3px solid var(--gold);
  box-shadow: 0 20px 50px rgba(0,0,0,.2);
}
.modal-overlay.show .modal-popup { transform: scale(1) translateY(0); }
.modal-popup-icon  { width: 56px; height: 56px; background: rgba(220,80,60,.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 26px; border: 1px solid rgba(220,80,60,.15); }
.modal-popup-title { font-size: 17px; font-weight: 700; color: #c0392b; margin-bottom: 8px; }
.modal-popup-msg   { font-size: 12.5px; color: var(--gray-500); margin-bottom: 20px; line-height: 1.6; }
.modal-popup-close { background: var(--black); color: var(--gold-lt); border: 1px solid var(--gold-rim); padding: 9px 28px; border-radius: var(--r2); font-weight: 700; font-size: 13px; cursor: pointer; transition: all .15s; }
.modal-popup-close:hover { background: var(--black3); box-shadow: var(--sh-gold); }

/* ══════════════════════════════════════════════
   UTILITIES
══════════════════════════════════════════════ */
.flex        { display: flex; }
.items-center{ align-items: center; }
.gap-2       { gap: 8px; }
.ml-auto     { margin-left: auto; }
.font-mono   { font-family: var(--ff-mono); }
.text-muted  { color: var(--gray-400); }
.text-gold   { color: var(--gold-dk); }
.mb-4        { margin-bottom: 16px; }
.mb-5        { margin-bottom: 20px; }

</style>
</head>
<body>

@php
    $userRole    = session('user')['role'] ?? null;
    $userName    = session('user')['nama_lengkap'] ?? 'User';
    $userInitial = strtoupper(substr($userName, 0, 1));
@endphp

<!-- ════════════════ SIDEBAR ════════════════ -->
<aside class="sidebar">
    <div class="s-logo">
        <div class="logo-icon">N</div>
        <div class="logo-words">
            <div class="logo-name">NM Gallery</div>
            <div class="logo-sub">SIM Baju Bodo</div>
        </div>
    </div>

    <div class="s-label">Menu</div>

    <!-- Dashboard (semua role) -->
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm7 0a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1V4zM3 13a1 1 0 011-1h5a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm7 0a1 1 0 011-1h5a1 1 0 011 1v3a1 1 0 01-1 1h-5a1 1 0 01-1-1v-3z"/></svg>
        Dashboard
    </a>

    <!-- Inventaris (SEMUA ROLE) -->
    <a href="{{ route('barang.index') }}" class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
        Inventaris & Stok
        <span class="nav-pill info">{{ $totalBarang ?? 0 }}</span>
    </a>

    <!-- Transaksi (Karyawan only) -->
    @if($userRole == 'Karyawan')
    <a href="{{ route('transaksi.index') }}" class="nav-item {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
        Transaksi & E-Nota
    </a>
    @else
    <div class="nav-item" onclick="showAccessDenied('Transaksi & E-Nota', 'Karyawan')">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
        Transaksi & E-Nota
    </div>
    @endif

    <!-- Laporan (Owner only) -->
    @if($userRole == 'Owner')
    <a href="{{ route('laporan') }}" class="nav-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
        Laporan Keuangan
    </a>
    @else
    <div class="nav-item" onclick="showAccessDenied('Laporan Keuangan', 'Owner')">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
        Laporan Keuangan
    </div>
    @endif

    <div class="s-label" style="margin-top:8px">Lainnya</div>

    <!-- Pelanggan (semua role) -->
    <a href="{{ route('pelanggan.index') }}" class="nav-item {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/></svg>
        Pelanggan
    </a>

    <!-- Pengaturan (Owner only) -->
    @if($userRole == 'Owner')
    <a href="{{ route('pengaturan') }}" class="nav-item {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
        Pengaturan
    </a>
    @else
    <div class="nav-item" onclick="showAccessDenied('Pengaturan', 'Owner')">
        <svg class="n-ico" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
        Pengaturan
    </div>
    @endif

    <div class="s-footer">
        <form action="{{ route('logout') }}" method="POST" id="logoutForm">@csrf</form>
        <div class="s-user" onclick="document.getElementById('logoutForm').submit()">
            <div class="s-ava">{{ $userInitial }}</div>
            <div>
                <div class="s-uname">{{ $userName }}</div>
                <div class="s-urole">{{ $userRole ?? '—' }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- ════════════════ MAIN ════════════════ -->
<div class="main">
    <header class="topbar">
        <nav class="tb-breadcrumb">
            <span>NM Gallery</span>
            <span class="sep"> / </span>
            <span class="cur">@yield('breadcrumb', 'Dashboard')</span>
        </nav>
        <div class="tb-search">
            <span style="font-size:13px;color:var(--gray-400)">🔍</span>
            <input placeholder="Cari baju, pelanggan, transaksi… ⌘K">
        </div>
        <div class="tb-right">
            @if($userRole == 'Karyawan')
            <a href="{{ route('transaksi.create') }}" class="btn-gold">+ Sewa Baru</a>
            @endif
        </div>
    </header>

    <div class="content">
        @yield('content')
    </div>
</div>

<!-- ════════════════ ACCESS DENIED MODAL ════════════════ -->
<div class="modal-overlay" id="accessModal">
    <div class="modal-popup">
        <div class="modal-popup-icon">🔒</div>
        <div class="modal-popup-title">Akses Ditolak</div>
        <div class="modal-popup-msg" id="accessModalMsg">Anda tidak memiliki akses ke halaman ini.</div>
        <button class="modal-popup-close" onclick="closeAccessModal()">OK, Mengerti</button>
    </div>
</div>

<script>
function showAccessDenied(pageName, requiredRole) {
    document.getElementById('accessModalMsg').innerHTML =
        `Anda tidak dapat mengakses <strong>${pageName}</strong>.<br><br>Halaman ini hanya untuk <strong>${requiredRole}</strong>.`;
    document.getElementById('accessModal').classList.add('show');
}
function closeAccessModal() {
    document.getElementById('accessModal').classList.remove('show');
}
document.getElementById('accessModal').addEventListener('click', function(e) {
    if (e.target === this) closeAccessModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAccessModal(); });
</script>

@stack('scripts')
</body>
</html>