<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiscalTrack — @yield('title', 'Tableau de bord')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<style>
/* ============ TOKENS ============ */
:root{
  --navy-900:#101d47;
  --navy-800:#152761;
  --indigo-600:#3d54c9;
  --blue-600:#3b6fe0;
  --blue-500:#5089ec;
  --blue-400:#7db3f2;
  --ice-200:#cfe6fb;
  --ice-100:#e9f4fd;
  --bg:#f3f6fb;
  --bg-subtle:#fafbfd;
  --surface:#ffffff;
  --line:#e3e9f3;
  --text-900:#152036;
  --text-600:#5a6580;
  --text-400:#93a0b8;
  --amber:#c98a1c;
  --amber-bg:#fdf3e0;
  --green:#1f9d63;
  --green-bg:#e4f7ee;
  --red:#d94c4c;
  --red-bg:#fbe9e9;
  --title-navy:#1f3864;
  --dossier-orange:#cf6f3f;
  --dossier-orange-dark:#bb5f31;
  --radius:12px;
  --shadow-sm:0 1px 2px rgba(16,29,71,.06);
  --shadow-md:0 8px 24px rgba(16,29,71,.08);
}
/* ============ MODE SOMBRE ============
   Bascule via .dark sur <body> (voir bouton lune/soleil de l'appbar).
   Comme la plupart des composants utilisent déjà var(--bg), var(--surface),
   var(--line), var(--text-...), les redéfinir ici assombrit toute l'application. */
body.dark{
  --bg:#0f1626;
  --bg-subtle:#161f33;
  --surface:#182238;
  --line:#2a3550;
  --text-900:#eef2fb;
  --text-600:#a7b2cc;
  --text-400:#78849f;
  --amber-bg:#3a2f16;
  --green-bg:#123527;
  --red-bg:#3a1c1c;
  --ice-100:#132745;
  --title-navy:#16294f;
  --dossier-orange:#8a4a2b;
  --dossier-orange-dark:#733d22;
  --shadow-sm:0 1px 2px rgba(0,0,0,.35);
  --shadow-md:0 8px 24px rgba(0,0,0,.45);
}
body.dark ::-webkit-scrollbar-thumb{background:#324061;}
body.dark input, body.dark select, body.dark textarea{color:var(--text-900);}
/* Les <input type="date"> gardent un rendu natif clair (texte noir sur fond clair) même en
   mode sombre : le calendrier natif du navigateur ne se restylise pas proprement en sombre. */
body.dark input[type="date"]{color-scheme:light;background:#f4f6fa;color:#111827;border-color:#c7cfdd;}
body.dark .filter-select, body.dark .search-box, body.dark .field input, body.dark .field select,
body.dark #f-trackeddoc-nom{background:var(--bg);}
body.dark .btn-primary{filter:brightness(.95);}
body.dark .avatar{filter:brightness(.95);}
body.dark .lock-overlay{background:linear-gradient(190deg,#080d1c,var(--indigo-600));}
body.dark .badge.b-inactive{background:#232c42;color:var(--text-600);}
body.compact-ui table td, body.compact-ui table th{padding-top:7px!important;padding-bottom:7px!important;}
body.compact-ui .panel-head{padding-top:10px;padding-bottom:10px;}

body{transition:background-color .2s ease, color .2s ease;}
.panel,.modal,.appbar,.dropdown,.kpi{transition:background-color .2s ease, border-color .2s ease;}
*{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;}
body{
  font-family:'Inter',sans-serif;
  background:var(--bg);
  color:var(--text-900);
  -webkit-font-smoothing:antialiased;
  overflow:hidden;
}
h1,h2,h3,.display{font-family:'Sora',sans-serif;}
.num{font-family:'JetBrains Mono',monospace;}
button{font-family:inherit;cursor:pointer;border:none;background:none;color:inherit;}
input,select{font-family:inherit;}
::-webkit-scrollbar{width:8px;height:8px;}
::-webkit-scrollbar-thumb{background:#c7d3e6;border-radius:8px;}
::-webkit-scrollbar-track{background:transparent;}

/* ============ SHELL ============ */
.shell{display:grid;grid-template-columns:264px 1fr;grid-template-rows:64px 1fr;height:100vh;}

/* ============ SIDEBAR ============ */
.sidebar{
  grid-row:1 / 3;
  background:linear-gradient(190deg,var(--navy-900) 0%,var(--navy-800) 55%,var(--indigo-600) 130%);
  color:#fff;
  display:flex;flex-direction:column;
  padding:22px 14px;
  position:relative;
  z-index:20;
  transition:transform .25s ease;
}
/* .brand{display:flex;align-items:center;gap:10px;padding:0 8px 22px 8px;}
.brand-mark{width:34px;height:34px;position:relative;flex:none;}
.brand-mark svg{width:100%;height:100%;display:block;}
.brand-word{font-family:'Sora',sans-serif;font-weight:700;font-size:17px;letter-spacing:-.02em;line-height:1;}
.brand-word span{font-weight:400;color:var(--ice-200);} */
.role-pill{
  margin:0 8px 18px 8px;
  padding:9px 12px;
  background:rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.12);
  border-radius:10px;
  display:flex;align-items:center;justify-content:space-between;
  gap:8px;
}
.role-pill .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:var(--ice-200);opacity:.75;}
.role-pill .role-fixed{color:#fff;font-size:13px;font-weight:600;}

.nav-group{margin-bottom:4px;}
.nav-label{font-size:10.5px;text-transform:uppercase;letter-spacing:.09em;color:rgba(255,255,255,.4);padding:14px 12px 6px;}
.nav-item{
  display:flex;align-items:center;gap:11px;
  padding:10px 12px;margin:1px 0;border-radius:9px;
  font-size:13.5px;font-weight:500;color:rgba(255,255,255,.8);
  position:relative;transition:background .15s;
  text-decoration:none;cursor:pointer;
}
.nav-item svg{width:18px;height:18px;flex:none;opacity:.85;}
.nav-item .badge{
  margin-left:auto;background:var(--blue-500);color:#fff;font-size:10.5px;font-weight:700;
  min-width:18px;height:18px;border-radius:20px;display:flex;align-items:center;justify-content:center;padding:0 5px;
}
.nav-item:hover{background:rgba(255,255,255,.07);color:#fff;}
.nav-item.active{background:rgba(255,255,255,.14);color:#fff;}
.nav-item.active::before{
  content:'';position:absolute;left:-14px;top:8px;bottom:8px;width:3px;border-radius:3px;
  background:linear-gradient(180deg,var(--ice-200),var(--blue-400));
}
.nav-item.hidden{display:none;}

.sidebar-foot{margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.1);}
.mini-card{
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
  border-radius:10px;padding:12px;font-size:12px;color:rgba(255,255,255,.75);line-height:1.5;
}
.mini-card b{color:#fff;display:block;font-size:12.5px;margin-bottom:2px;}

/* ============ APPBAR ============ */
.appbar{
  grid-column:2;grid-row:1;
  background:var(--surface);border-bottom:1px solid var(--line);
  display:flex;align-items:center;gap:16px;
  padding:0 24px;position:relative;z-index:15;
}
.menu-toggle{display:none;width:34px;height:34px;border-radius:8px;align-items:center;justify-content:center;color:var(--text-600);}
.menu-toggle:hover{background:var(--bg);}
.appbar-title{display:flex;flex-direction:column;gap:1px;}
.appbar-title h1{font-size:16.5px;font-weight:700;}
.appbar-title .crumb{font-size:11.5px;color:var(--text-400);}
.icon-btn{
  width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
  color:var(--text-600);position:relative;flex:none;
}
.icon-btn:hover{background:var(--bg);}
.icon-btn svg{width:19px;height:19px;}
.dot{position:absolute;top:7px;right:7px;width:8px;height:8px;border-radius:50%;background:var(--red);border:2px solid #fff;}
.user-chip{display:flex;align-items:center;gap:9px;padding:6px 10px 6px 6px;border-radius:11px;}
.user-chip:hover{background:var(--bg);}
.avatar{
  width:32px;height:32px;border-radius:9px;flex:none;
  background:linear-gradient(135deg,var(--blue-500),var(--indigo-600));
  color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12.5px;
}
.user-chip .who{text-align:left;line-height:1.25;}
.user-chip .who .name{font-size:12.5px;font-weight:600;}
.user-chip .who .role{font-size:10.5px;color:var(--text-400);}

/* dropdowns */
.dropdown{
  position:absolute;top:56px;right:24px;width:340px;background:var(--surface);border:1px solid var(--line);
  border-radius:14px;box-shadow:var(--shadow-md);display:none;overflow:hidden;z-index:50;
}
.dropdown.open{display:block;}
.dropdown-head{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--line);}
.dropdown-head h3{font-size:13.5px;}
.link-btn{font-size:11.5px;color:var(--blue-600);font-weight:600;}
.notif-list{max-height:340px;overflow-y:auto;}
.notif-item{display:flex;gap:10px;padding:12px 16px;border-bottom:1px solid var(--line);}
.notif-item:last-child{border-bottom:none;}
.notif-item .ic{width:30px;height:30px;border-radius:9px;flex:none;display:flex;align-items:center;justify-content:center;}
.notif-item .ic svg{width:15px;height:15px;}
.notif-item.warn .ic{background:var(--amber-bg);color:var(--amber);}
.notif-item.late .ic{background:var(--red-bg);color:var(--red);}
.notif-item.ok .ic{background:var(--green-bg);color:var(--green);}
.notif-item .txt{font-size:12.5px;line-height:1.45;}
.notif-item .txt b{font-weight:600;}
.notif-item .txt .when{color:var(--text-400);font-size:11px;margin-top:2px;}
.notif-item.unread{background:var(--bg-subtle);}
.user-dropdown{width:200px;padding:6px;}
.user-dropdown button{
  width:100%;text-align:left;padding:9px 10px;border-radius:8px;font-size:13px;font-weight:500;
  display:flex;align-items:center;gap:9px;color:var(--text-900);
}
.user-dropdown svg{width:16px;height:16px;color:var(--text-400);}
.user-dropdown button:hover{background:var(--bg);}
.user-dropdown button.danger{color:var(--red);}
.user-dropdown button.danger svg{color:var(--red);}
.user-dropdown hr{border:none;border-top:1px solid var(--line);margin:6px 2px;}

/* ============ MAIN ============ */
.main{grid-column:2;grid-row:2;overflow-y:auto;overflow-x:auto;padding:24px;}
.section{display:block;animation:fade .25s ease;min-width:920px;}
@keyframes fade{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);}}

.section-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:18px;gap:12px;flex-wrap:wrap;}
.section-head h2{font-size:21px;font-weight:700;}
.section-head p{font-size:12.5px;color:var(--text-600);margin-top:3px;}
.btn{
  display:inline-flex;align-items:center;gap:7px;padding:10px 15px;border-radius:10px;
  font-size:13px;font-weight:600;white-space:nowrap;
}
.btn svg{width:15px;height:15px;}
.btn-primary{background:linear-gradient(135deg,var(--blue-500),var(--indigo-600));color:#fff;box-shadow:0 6px 16px rgba(59,111,224,.28);}
.btn-primary:hover{filter:brightness(1.06);}
.btn-ghost{background:var(--surface);border:1px solid var(--line);color:var(--text-700,#33405c);}
.btn-ghost:hover{background:var(--bg);}

/* KPI cards */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
.kpi{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);padding:17px 18px;box-shadow:var(--shadow-sm);}
.kpi .top{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.kpi .ic{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;}
.kpi .ic svg{width:17px;height:17px;}
.kpi .trend{font-size:11px;font-weight:700;padding:3px 7px;border-radius:20px;}
.kpi .trend.up{color:var(--green);background:var(--green-bg);}
.kpi .trend.warn{color:var(--amber);background:var(--amber-bg);}
.kpi .value{font-family:'JetBrains Mono',monospace;font-size:25px;font-weight:600;letter-spacing:-.02em;}
.kpi .label{font-size:12px;color:var(--text-600);margin-top:3px;}

.tone-blue{background:var(--ice-100);color:var(--blue-600);}
.tone-navy{background:#eaeefb;color:var(--navy-800);}
.tone-amber{background:var(--amber-bg);color:var(--amber);}
.tone-green{background:var(--green-bg);color:var(--green);}

.grid-2{display:grid;grid-template-columns:1.5fr 1fr;gap:14px;}

/* panel / table card */
.panel{background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;}
.panel-head{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--line);gap:10px;flex-wrap:wrap;}
.panel-head h3{font-size:14.5px;font-weight:700;}
.panel-head .sub{font-size:11.5px;color:var(--text-400);margin-top:1px;}
.toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.search-box{display:flex;align-items:center;gap:7px;background:var(--bg);border:1px solid var(--line);border-radius:9px;padding:7px 10px;min-width:190px;}
.search-box svg{width:14px;height:14px;color:var(--text-400);flex:none;}
.search-box input{border:none;background:none;outline:none;font-size:12.5px;width:100%;}
.filter-select{background:var(--bg);border:1px solid var(--line);border-radius:9px;padding:7px 26px 7px 10px;font-size:12.5px;color:var(--text-700,#33405c);appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%235a6580' stroke-width='3'><path d='M6 9l6 6 6-6'/></svg>");background-repeat:no-repeat;background-position:right 9px center;}
.reset-btn{
  display:flex;align-items:center;justify-content:center;gap:6px;
  background:var(--bg);border:1px solid var(--line);border-radius:9px;
  padding:7px 11px;font-size:12.5px;font-weight:600;color:var(--text-600);flex:none;
}
.reset-btn svg{width:14px;height:14px;transition:transform .35s ease;}
.reset-btn:hover{background:#eef1f7;color:var(--blue-600);border-color:var(--blue-400);}
.reset-btn:hover svg{transform:rotate(-140deg);}

/* toolbar autonome au-dessus d'un tableau */
.table-toolbar{
  background:var(--surface);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow-sm);
  padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
}
.export-group{display:flex;gap:8px;flex:none;}
.export-btn{
  display:flex;align-items:center;gap:7px;padding:8px 13px;border-radius:9px;font-size:12.5px;font-weight:600;
  background:var(--surface);border:1px solid var(--line);white-space:nowrap;
}
.export-btn svg{width:14.5px;height:14.5px;}
.export-btn.pdf{color:var(--blue-600);}
.export-btn.pdf:hover{background:var(--ice-100);border-color:var(--blue-400);}
.export-btn.excel{color:#1f9d63;}
.export-btn.excel:hover{background:var(--green-bg);border-color:#a8e2c4;}
.count-below{margin-top:10px;font-size:11.5px;color:var(--text-400);text-align:right;}

/* ---- export PDF via impression native du navigateur ---- */
@media print{
  body.print-contribuables .sidebar,
  body.print-contribuables .appbar,
  body.print-contribuables #sec-contribuables .section-head,
  body.print-contribuables #sec-contribuables .table-toolbar,
  body.print-contribuables .print-hide{ display:none !important; }
  body.print-contribuables .shell{ display:block; height:auto; }
  body.print-contribuables .main{ overflow:visible; padding:0; }
  body.print-contribuables .section{ display:none !important; }
  body.print-contribuables #sec-contribuables{ display:block !important; }
  body.print-contribuables .dossier-panel{ box-shadow:none; border:none; }
  body.print-contribuables .dossier-scroll{ overflow:visible !important; max-height:none !important; }
  body.print-contribuables .sticky-col{ position:static !important; box-shadow:none !important; }
  body.print-contribuables .dossier-table{
    font-size:6.5px; width:100% !important; min-width:0 !important; max-width:100%;
    table-layout:fixed; border-collapse:collapse;
  }
  body.print-contribuables .dossier-table th,
  body.print-contribuables .dossier-table td{
    padding:3px 3px; white-space:normal !important; word-break:break-word; overflow-wrap:anywhere;
  }
  body.print-contribuables .dossier-titlebar{ font-size:13px; padding:10px 14px; }
  @page{ size:A3 landscape; margin:8mm; }
}

/* mise en évidence d'une ligne fraîchement ajoutée */
@keyframes rowFlash{0%{background:var(--ice-100);}100%{background:transparent;}}
tbody tr.row-new td{animation:rowFlash 2.4s ease-out;}

table{width:100%;border-collapse:collapse;}
thead th{
  text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-400);
  padding:10px 18px;border-bottom:1px solid var(--line);background:var(--bg-subtle);font-weight:700;
}
tbody td{padding:12px 18px;font-size:13px;border-bottom:1px solid var(--line);color:var(--text-900);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:var(--bg-subtle);}
.cell-strong{font-weight:600;}
.cell-sub{font-size:11px;color:var(--text-400);margin-top:1px;}
.mono{font-family:'JetBrains Mono',monospace;font-size:12.5px;}

.badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 9px;border-radius:20px;}
.badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;}
.badge.b-todo{background:var(--amber-bg);color:var(--amber);}
.badge.b-progress{background:var(--ice-100);color:var(--blue-600);}
.badge.b-done{background:var(--green-bg);color:var(--green);}
.badge.b-late{background:var(--red-bg);color:var(--red);}
.badge.b-active{background:var(--green-bg);color:var(--green);}
.badge.b-inactive{background:#eef0f4;color:var(--text-600);}

.row-actions{display:flex;gap:5px;justify-content:flex-end;}
.mini-btn{width:29px;height:29px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-400);}
.mini-btn-active{color:var(--blue-600);background:var(--ice-100);}
.mini-btn:hover{background:var(--bg);color:var(--blue-600);}
.mini-btn svg{width:14.5px;height:14.5px;}

.tag{display:inline-flex;align-items:center;gap:5px;background:var(--bg);border:1px solid var(--line);padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;color:var(--text-600);}

.empty{padding:50px 20px;text-align:center;color:var(--text-400);font-size:12.5px;}

/* setup form (configuration du dossier contribuables) */
.setup-wrap{display:flex;align-items:center;justify-content:center;min-height:calc(100vh - 160px);}
.setup-card{
  background:var(--surface);border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow-md);
  width:400px;max-width:92vw;padding:30px 28px;text-align:center;
}
.setup-mark{width:52px;height:52px;margin:0 auto 16px;}
.setup-mark svg{width:100%;height:100%;}
.setup-card h2{font-size:18px;margin-bottom:8px;}
.setup-card p{font-size:12.5px;color:var(--text-600);line-height:1.6;margin-bottom:20px;}
.setup-card .field{text-align:left;margin-bottom:14px;}

/* dossier (tableau excel) */
.dossier-panel{padding:0;overflow:hidden;}
.dossier-titlebar{
  background:linear-gradient(135deg,var(--title-navy),var(--navy-900));
  color:#fff;padding:20px 24px;font-family:'Sora',sans-serif;font-weight:800;
  font-size:19px;letter-spacing:.01em;text-align:center;
}
.panel-toolbar{
  display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;
  padding:12px 18px;border-bottom:1px solid var(--line);background:var(--bg-subtle);
}
.dossier-scroll{overflow-x:auto;overflow-y:auto;max-height:62vh;}
table.dossier-table{width:max-content;min-width:100%;border-collapse:separate;border-spacing:0;}
.dossier-table thead th{
  background:var(--dossier-orange);color:#fff;border:1px solid rgba(255,255,255,.55);
  font-size:10.5px;text-transform:uppercase;letter-spacing:.03em;font-weight:700;
  padding:9px 12px;white-space:nowrap;text-align:center;position:sticky;top:0;z-index:3;
}
.dossier-table thead tr:first-child th{top:0;}
.dossier-table-2row thead tr:last-child th{top:32px;background:var(--dossier-orange-dark);}
.dossier-table tbody td{
  padding:10px 12px;font-size:12.5px;white-space:nowrap;border:1px solid var(--line);color:var(--text-900);text-align:center;
}
.dossier-table tbody tr:hover td{background:var(--bg-subtle);}
.dossier-table td.name-cell{text-align:left;font-weight:600;}
.sticky-col{position:sticky;left:0;z-index:4;text-align:left !important;}
th.sticky-col{z-index:5;}
.dossier-table tbody td.sticky-col{background:var(--surface);box-shadow:2px 0 4px rgba(16,29,71,.06);}
.dossier-table tbody tr:hover td.sticky-col{background:var(--bg-subtle);}

/* échéances timeline (dashboard signature widget) */
.timeline{padding:6px 18px 14px;}
.tl-row{display:grid;grid-template-columns:70px 1fr auto;align-items:center;gap:12px;padding:11px 0;border-bottom:1px dashed var(--line);}
.tl-row:last-child{border-bottom:none;}
.tl-date{text-align:center;}
.tl-date .d{font-family:'JetBrains Mono',monospace;font-weight:700;font-size:16px;line-height:1;}
.tl-date .m{font-size:9.5px;text-transform:uppercase;color:var(--text-400);letter-spacing:.05em;}
.tl-bar{position:relative;height:6px;background:var(--bg);border-radius:6px;overflow:hidden;}
.tl-bar i{position:absolute;left:0;top:0;bottom:0;border-radius:6px;background:linear-gradient(90deg,var(--blue-500),var(--indigo-600));}
.tl-info b{font-size:12.5px;font-weight:600;display:block;}
.tl-info span{font-size:11px;color:var(--text-400);}

/* activity feed */
.feed{padding:4px 18px 14px;}
.feed-item{display:flex;gap:11px;padding:11px 0;border-bottom:1px solid var(--line);}
.feed-item:last-child{border-bottom:none;}
.feed-dot{width:8px;height:8px;border-radius:50%;margin-top:5px;flex:none;background:var(--blue-500);}
.feed-item p{font-size:12.5px;line-height:1.5;}
.feed-item p b{font-weight:600;}
.feed-item time{font-size:10.5px;color:var(--text-400);}

/* modal */
.overlay{position:fixed;inset:0;background:rgba(16,24,50,.45);display:none;align-items:center;justify-content:center;z-index:100;backdrop-filter:blur(2px);}
.overlay.open{display:flex;}
.modal{background:var(--surface);border-radius:16px;width:460px;max-width:92vw;box-shadow:var(--shadow-md);overflow:hidden;}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--line);}
.modal-head h3{font-size:15px;font-weight:700;}
.modal-body{padding:18px 20px;display:flex;flex-direction:column;gap:13px;max-height:60vh;overflow-y:auto;}
.field label{font-size:11.5px;font-weight:600;color:var(--text-600);display:block;margin-bottom:5px;}
.field input,.field select{width:100%;padding:9px 11px;border:1px solid var(--line);border-radius:9px;font-size:13px;outline:none;background:var(--bg);}
.field input:focus,.field select:focus{border-color:var(--blue-500);background:var(--surface);}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.field-row-3{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
.field-row-5{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;}
.field-row-5 .field label,.field-row-3 .field label{font-size:10px;}
.field-row-5 .field input,.field-row-3 .field input{padding:8px 8px;font-size:12px;}
.multi-select-list{max-height:170px;overflow-y:auto;border:1px solid var(--line);border-radius:9px;background:var(--bg);padding:6px;display:flex;flex-direction:column;gap:2px;}
.multi-select-item{display:flex;align-items:center;gap:8px;padding:6px 7px;border-radius:7px;font-size:12.5px;color:var(--text-900);cursor:pointer;}
.multi-select-item:hover{background:var(--surface);}
.multi-select-item input{width:14px;height:14px;accent-color:var(--blue-600);cursor:pointer;flex:none;}
.org-links{display:flex;gap:10px;margin-top:14px;}
.org-link{
  display:flex;align-items:center;gap:9px;background:var(--surface);border:1px solid var(--line);
  border-radius:12px;padding:12px 16px;font-size:12.5px;font-weight:600;color:var(--text-900);
  box-shadow:var(--shadow-sm);transition:border-color .15s,box-shadow .15s;
}
.org-link:hover{border-color:var(--blue-500);box-shadow:var(--shadow-md);}
.org-link .ic{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none;background:var(--ice-100);color:var(--blue-600);}
.org-link .ic svg{width:17px;height:17px;}
.org-link .meta{display:flex;flex-direction:column;gap:1px;}
.org-link .meta .sub{font-size:10.5px;font-weight:500;color:var(--text-400);}
.inline-select{border:1px solid var(--line);border-radius:8px;padding:5px 8px;font-size:12px;background:var(--bg);color:var(--text-900);outline:none;cursor:pointer;}
.inline-select:focus{border-color:var(--blue-500);background:var(--surface);}
.cal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
.cal-head .label{font-size:13px;font-weight:700;color:var(--text-900);text-transform:capitalize;}
.cal-nav{display:flex;gap:6px;}
.cal-dow{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:4px;}
.cal-dow span{text-align:center;font-size:10.5px;font-weight:700;color:var(--text-400);text-transform:uppercase;}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;}
.cal-cell{position:relative;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:12px;color:var(--text-600);background:var(--bg);}
.cal-cell.empty{background:transparent;}
.cal-cell.has-event{cursor:pointer;background:var(--amber-bg);color:var(--text-900);font-weight:700;}
.cal-cell.has-event:hover{filter:brightness(0.96);}
.cal-cell.is-today{outline:2px solid var(--blue-500);outline-offset:-2px;}
.cal-cell .dot{position:absolute;bottom:4px;width:5px;height:5px;border-radius:50%;background:var(--amber);}
.cal-legend{display:flex;gap:14px;margin-top:10px;font-size:11px;color:var(--text-400);}
.cal-legend span{display:inline-flex;align-items:center;gap:5px;}
.cal-legend i{width:8px;height:8px;border-radius:50%;display:inline-block;}
.cal-details{margin-top:12px;border-top:1px solid var(--line);padding-top:10px;}
.cal-details-head{font-size:11.5px;font-weight:700;color:var(--text-600);margin-bottom:6px;}
.cal-details-item{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:12.5px;padding:6px 0;border-bottom:1px solid var(--line);}
.cal-details-item:last-child{border-bottom:none;}
.modal-subtitle{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--blue-600);padding-top:12px;margin-top:2px;border-top:1px solid var(--line);}
.modal-subtitle:first-child{border-top:none;padding-top:0;margin-top:0;}
.modal-foot{display:flex;justify-content:flex-end;gap:9px;padding:16px 20px;border-top:1px solid var(--line);}

/* logout overlay */
.lock-overlay{position:fixed;inset:0;background:linear-gradient(190deg,var(--navy-900),var(--indigo-600));display:none;align-items:center;justify-content:center;z-index:200;color:#fff;text-align:center;}
.lock-overlay.open{display:flex;}
.lock-card{max-width:340px;}
.lock-card .brand-mark{width:56px;height:56px;margin:0 auto 18px;}
.lock-card h2{font-size:19px;margin-bottom:8px;}
.lock-card p{font-size:13px;color:rgba(255,255,255,.7);margin-bottom:22px;line-height:1.6;}

@media (max-width:980px){
  .kpi-grid{grid-template-columns:repeat(2,1fr);}
  .grid-2{grid-template-columns:1fr;}
}
@media (max-width:760px){
  .shell{grid-template-columns:1fr;}
  .sidebar{position:fixed;top:0;bottom:0;left:0;width:264px;transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}
  .menu-toggle{display:flex;}
  .appbar{grid-column:1;}
  .main{grid-column:1;}
}
</style>
</head>
<body>

<!-- SVG ICON SPRITE -->
<svg width="0" height="0" style="position:absolute">
<symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
<symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
<symbol id="i-folder" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></symbol>
<symbol id="i-file" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h6M9 9h1"/></symbol>
<symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
<symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
<symbol id="i-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></symbol>
<symbol id="i-chevron-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></symbol>
<symbol id="i-chevron-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></symbol>
<symbol id="i-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></symbol>
<symbol id="i-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></symbol>
<symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></symbol>
<symbol id="i-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></symbol>
<symbol id="i-trash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
<symbol id="i-archive" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></symbol>
<symbol id="i-restore" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></symbol>
<symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
<symbol id="i-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></symbol>
<symbol id="i-settings" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></symbol>
<symbol id="i-alert" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/></symbol>
<symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></symbol>
<symbol id="i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
<symbol id="i-link" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></symbol>
<symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></symbol>
<symbol id="i-refresh" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 15.3-6.4L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.3 6.4L3 16"/><path d="M3 21v-5h5"/></symbol>
<symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
<symbol id="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></symbol>
<symbol id="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></symbol>
<symbol id="i-expand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M21 16v3a2 2 0 0 1-2 2h-3M3 16v3a2 2 0 0 0 2 2h3"/></symbol>
<symbol id="i-compress" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3v3a2 2 0 0 1-2 2H4M21 8h-3a2 2 0 0 1-2-2V3M3 16h3a2 2 0 0 1 2 2v3M16 21v-3a2 2 0 0 1 2-2h3"/></symbol>
<symbol id="i-logo" viewBox="0 0 40 40"><defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#dff0fd"/><stop offset="1" stop-color="#7fb4f2"/></linearGradient><linearGradient id="g2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#6f89e8"/><stop offset="1" stop-color="#4a5fd6"/></linearGradient></defs><path d="M14 3h18c2 0 2.6 2.4 1 3.6L21 15h9c2.3 0 2.8 3 .9 4L14 24V3Z" fill="url(#g1)"/><rect x="14" y="17" width="15" height="12" rx="6" fill="url(#g2)"/></symbol>
</svg>

<body>

<div class="shell">

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="sidebar">
    
  <img src="images/fiscaltrack-logo.png" alt="FiscalTrack">
    <!-- <div class="brand">
      <div class="brand-mark"><svg><use href="#i-logo"/></svg></div>
      <div class="brand-word">Fiscal<span>track</span></div>
    </div> -->

    <div class="role-pill">
      <span class="lbl">Connecté comme</span>
      <span class="role-fixed">{{ $authUser['role_label'] }}</span>
    </div>
    <fieldset style="margin-top: 10px; margin-bottom: 20px;"></fieldset>
    <nav>
      <div class="nav-group">
        <div class="nav-label">Général</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-roles="admin,comptable,fiscal">
          <svg><use href="#i-grid"/></svg><span>Tableau de bord</span>
        </a>
      </div>
      <div class="nav-group">
        <div class="nav-label">Gestion</div>
        <a href="{{ route('contribuables.index') }}" class="nav-item {{ request()->routeIs('contribuables.*') ? 'active' : '' }}" data-roles="admin,comptable">
          <svg><use href="#i-users"/></svg><span>Contribuables</span>
        </a>
        <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}" data-roles="admin,comptable">
          <svg><use href="#i-folder"/></svg><span>Documents (GED)</span>
        </a>
        <a href="{{ route('archives.index') }}" class="nav-item {{ request()->routeIs('archives.*') ? 'active' : '' }}" data-roles="admin,comptable">
          <svg><use href="#i-archive"/></svg><span>Archives</span>
        </a>
        <a href="{{ route('declarations.index') }}" class="nav-item {{ request()->routeIs('declarations.*') ? 'active' : '' }}" data-roles="admin,fiscal">
          <svg><use href="#i-file"/></svg><span>Déclaration</span>
        </a>
      </div>
      <div class="nav-group">
        <div class="nav-label">Suivi</div>
        <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}" data-roles="admin,comptable,fiscal">
          <svg><use href="#i-bell"/></svg><span>Notifications</span>
          <!-- <span class="badge" id="navNotifBadge">3</span> -->
        </a>
        <a href="{{ route('comptes.index') }}" class="nav-item {{ request()->routeIs('comptes.*') ? 'active' : '' }}" data-roles="admin">
          <svg><use href="#i-settings"/></svg><span>Comptes utilisateurs</span>
        </a>
      </div>
    </nav>

    <div class="sidebar-foot">
      <div class="mini-card">
        <b>TIA International Ltd</b>
        Suivi des déclarations &amp; mini-GED — cabinet comptable.
      </div>
    </div>
  </aside>

  <!-- ============ APPBAR ============ -->
  <header class="appbar">
    <button class="menu-toggle" id="menuToggle"><svg><use href="#i-menu"/></svg></button>
    <div class="appbar-title">
      <h1 id="pageTitle">@yield('title', 'Tableau de bord')</h1>
      <div class="crumb" id="pageCrumb">@yield('crumb', 'FiscalTrack / Accueil')</div>
    </div>

    <button class="icon-btn" id="darkModeBtn" title="Mode sombre" style="margin-left:auto;">
      <svg id="darkModeIcon"><use href="#i-moon"/></svg>
    </button>

    <button class="icon-btn" id="fullscreenBtn" title="Plein écran (F11)">
      <svg id="fullscreenIcon"><use href="#i-expand"/></svg>
    </button>

    <button class="icon-btn" id="notifBtn">
      <svg><use href="#i-bell"/></svg>
      <span class="dot" id="notifDot"></span>
    </button>

    <button class="user-chip" id="userBtn">
      <div class="avatar" id="userAvatar">{{ strtoupper(\Illuminate\Support\Str::substr($authUser['name'], 0, 2)) }}</div>
      <div class="who">
        <div class="name" id="userNameLabel">{{ $authUser['name'] }}</div>
        <div class="role" id="userRoleLabel">{{ $authUser['role_label'] }}</div>
      </div>
      <svg style="width:14px;height:14px;color:var(--text-400)"><use href="#i-chevron"/></svg>
    </button>

    <!-- notifications dropdown -->
    <div class="dropdown" id="notifDropdown">
      <div class="dropdown-head">
        <h3>Notifications</h3>
        <button class="link-btn" id="markAllRead">Tout marquer comme lu</button>
      </div>
      <div class="notif-list" id="notifList"></div>
    </div>

    <!-- user dropdown -->
    <div class="dropdown user-dropdown" id="userDropdown" style="right:24px;width:200px;">
      <button type="button" id="profileMenuBtn"><svg><use href="#i-user"/></svg>Mon profil</button>
      <button type="button" id="settingsMenuBtn"><svg><use href="#i-settings"/></svg>Paramètres</button>
      <hr>
      <button class="danger" id="logoutBtn" type="button"><svg><use href="#i-logout"/></svg>Se déconnecter</button>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
      @csrf
    </form>
  </header>

  <!-- ============ MAIN ============ -->

  <main class="main">
    @yield('content')
  </main>
</div>

<!-- déconnexion -->
<div class="lock-overlay" id="lockOverlay">
  <div class="lock-card">
    <div class="brand-mark"><svg><use href="#i-logo"/></svg></div>
    <h2>Vous êtes déconnecté</h2>
    <p>Votre session FiscalTrack a été fermée en toute sécurité. Reconnectez-vous pour accéder à votre espace de travail.</p>
    <a class="btn btn-primary" style="justify-content:center;width:100%;text-decoration:none;" href="{{ route('login') }}">Se reconnecter</a>
  </div>
</div>


<script>
/* ================= DATA ================= */
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const authUser = @json($authUser ?? ['name'=>'Utilisateur','role'=>'admin','role_label'=>'Administrateur']);
let users = @json($initialUsers ?? []);

/* Toutes ces listes sont vides par défaut : dans l'application réelle (Laravel + MySQL),
   elles seront alimentées depuis la base de données. Les exemples ont été retirés. */
   let contribuables = (@json($initialContribuables ?? [])).map(normalizeContrib);
let dossier = { nom:'TIA INTERNATIONNAL LTD', annee:2026, configured:false };

let documents = @json($initialDocuments ?? []);

let archivedDocuments = @json($initialArchives ?? []);

/* notifications : chargées depuis app_notifications (serveur) puis resynchronisées */
let notifications = (@json($initialNotifications ?? [])).slice();
let notifReadState = {};

const activity = [];

/* Index de l'élément en cours de modification pour chaque tableau.
   null = mode "ajout" ; un nombre = mode "modification" (index réel dans le tableau source). */
let editContribIndex = null;
let editDocIndex = null;
let editUserId = null;

/* ---- Catalogue des types d'obligations (documents à suivre) ---- */
let trackedDocTypes = (@json($initialTrackedDocTypes ?? [])).map(function(t){
  return {
    id: t.id,
    nom: t.nom,
    dateLimite: t.date_limite ? new Date(t.date_limite + 'T00:00:00') : null,
    periodicite: t.periodicite || 'libre',
    organisme_defaut: t.organisme_defaut || null
  };
});

/* Matrice héritée (miroir) — clé = contribuable_id||tracked_doc_type_id */
let docStatusMatrix = {};
(@json($initialDeclarationStatuts ?? [])).forEach(function(d){
  docStatusMatrix[d.contribuable_id + '||' + d.tracked_doc_type_id] = { statut: d.statut };
});

/* Source de vérité du suivi fiscal */
let obligations = (@json($initialObligations ?? [])).slice();
let suiviKpis = @json($suiviKpis ?? null);
let contribVerifLink = {};

/* ================= MOTEUR D'ÉCHÉANCES & NOTIFICATIONS ==================
   Règles métier (Cameroun / cabinet) :
   - Mensuelle      : échéance = le 15 de chaque mois.
   - Trimestrielle  : échéance = 15 jours après la fin du trimestre civil.
   - Annuelle       : 15 mars (défaut cabinet).
   Notifications serveur : J-7, J, retard — obligation non clôturée uniquement.
   Une obligation ne peut être marquée déclarée / justificatif déposé SANS pièce GED.
========================================================================= */
function pad2(n){ return String(n).padStart(2,'0'); }
function fmtFR(d){ return d ? `${pad2(d.getDate())}/${pad2(d.getMonth()+1)}/${d.getFullYear()}` : '—'; }
function toISOInput(d){ return d ? `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}` : ''; }
function startOfDay(d){ const x=new Date(d); x.setHours(0,0,0,0); return x; }
function parseISODate(s){ return s ? new Date(s + 'T00:00:00') : null; }

function findContrib(id){ return contribuables.find(c=>Number(c.id)===Number(id)); }
function findTrackedDocType(id){ return trackedDocTypes.find(t=>Number(t.id)===Number(id)); }
function findObligation(id){ return obligations.find(o=>Number(o.id)===Number(id)); }

function syncMatrixFromObligations(){
  docStatusMatrix = {};
  obligations.forEach(o=>{
    const key = o.contribuable_id + '||' + o.tracked_doc_type_id;
    const closed = o.statut === 'declare' || o.statut === 'justificatif_depose';
    docStatusMatrix[key] = { statut: closed ? 'declare' : 'non_declare' };
  });
}
syncMatrixFromObligations();

function applySuiviKpis(kpis){
  if(kpis) suiviKpis = kpis;
}

async function refreshNotifications(){
  try{
    const res = await fetch('/api/notifications', { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }});
    if(res.ok){
      const data = await res.json();
      notifications = data.notifications || [];
    }
  }catch(e){ /* garde le snapshot serveur initial */ }
  renderNotifications();
}

function buildNotificationsFromObligations(){
  /* Fallback client si l'API n'est pas dispo */
  const today = startOfDay(new Date());
  const list = [];
  obligations.forEach(o=>{
    if(o.statut === 'declare' || o.statut === 'justificatif_depose') return;
    if(!o.date_limite) return;
    const d = parseISODate(o.date_limite);
    const diff = Math.round((startOfDay(d) - today)/86400000);
    if(diff > 7) return;
    let tone, title, text;
    if(diff >= 1){
      tone='warn'; title='Échéance proche';
      text = `${o.contribuable_nom} — ${o.type_nom} (${o.periode} ${o.annee}) dans ${diff} j.`;
    } else if(diff === 0){
      tone='warn'; title="Échéance aujourd'hui";
      text = `${o.contribuable_nom} — ${o.type_nom} (${o.periode} ${o.annee}) aujourd'hui.`;
    } else {
      tone='late'; title='Échéance dépassée';
      text = `${o.contribuable_nom} — ${o.type_nom} en retard depuis ${-diff} j.`;
    }
    list.push({ key:`obl:${o.id}`, tone, title, text, when: fmtFR(d), unread:true, obligation_id:o.id });
  });
  return list;
}

/* ================= NAV / SECTIONS ================= */
const roleLabels = {admin:'Administrateur', comptable:'Comptable', fiscal:'Responsable fiscal'};
const roleInitials = {admin:'ND', comptable:'AB', fiscal:'CE'};

/* Chaque écran est désormais une vraie page Laravel : goTo() effectue une navigation
   réelle (au lieu de basculer des <div> en JS comme à l'époque de la page unique). */
const sectionRoutes = {
  dashboard:     @json(route('dashboard')),
  contribuables: @json(route('contribuables.index')),
  documents:     @json(route('documents.index')),
  archives:      @json(route('archives.index')),
  declarations:  @json(route('declarations.index')),
  notifications: @json(route('notifications.index')),
  comptes:       @json(route('comptes.index')),
  profile:       @json(route('profile.index')),
  settings:      @json(route('settings.index')),
};
function goTo(section){
  if(sectionRoutes[section]) window.location.href = sectionRoutes[section];
}

/* ================= CONTRIBUABLES : configuration du dossier ================= */
function showContribView(){
  const setupOn = !dossier.configured;
  document.getElementById('contribSetupView').style.display = setupOn ? 'block' : 'none';
  document.getElementById('contribDossierView').style.display = setupOn ? 'none' : 'block';
  if(setupOn){
    document.getElementById('setupNom').value = dossier.nom || '';
    document.getElementById('setupAnnee').value = dossier.annee || new Date().getFullYear();
  } else {
    renderDossierHeader();
    renderContribuables();
  }
}
function renderDossierHeader(){
  document.getElementById('dossierTitle').textContent = `SUIVI DES DOSSIERS : ${dossier.nom.toUpperCase()} ${dossier.annee}`;
  document.getElementById('igsYearHeader').textContent = `IGS ${dossier.annee}`;
}
function submitSetup(){
  const nom = document.getElementById('setupNom').value.trim();
  const annee = document.getElementById('setupAnnee').value.trim();
  if(!nom || !annee){ alert("Le nom de l'entreprise et l'année sont obligatoires."); return; }
  dossier = { nom, annee, configured:true };
  showContribView();
}
function backToSetup(){
  dossier.configured = false;
  showContribView();
}
document.querySelectorAll('.nav-item[data-section]').forEach(el=>{
  el.addEventListener('click', ()=>goTo(el.dataset.section));
});

function applyRole(role){
  // Le nom, l'avatar et le libellé du rôle sont désormais rendus côté serveur
  // (voir $authUser dans le layout) ; cette fonction ne fait plus que masquer
  // les liens du menu latéral non autorisés pour le rôle de l'utilisateur connecté.
  document.querySelectorAll('.nav-item[data-roles]').forEach(el=>{
    const allowed = el.dataset.roles.split(',');
    el.classList.toggle('hidden', !allowed.includes(role));
  });
  const active = document.querySelector('.nav-item.active');
  // Filet de sécurité côté client uniquement : la vraie protection doit être faite
  // par un middleware Laravel sur chaque route (voir explication en fin de réponse).
  if(active && active.classList.contains('hidden')) goTo('dashboard');
  const roleFixed = document.querySelector('.role-fixed');
  if(roleFixed) roleFixed.textContent = roleLabels[role] || role;
}
// Rôle issu de la session Laravel
applyRole(authUser.role || 'admin');
document.getElementById('userNameLabel').textContent = authUser.name;
document.getElementById('userAvatar').textContent = initials(authUser.name);

const menuToggleBtn = document.getElementById('menuToggle');
if(menuToggleBtn) menuToggleBtn.addEventListener('click', ()=>{
  const sidebar = document.getElementById('sidebar');
  if(sidebar) sidebar.classList.toggle('open');
});

/* ================= MODE SOMBRE ================= */
function applyDarkMode(on){
  document.body.classList.toggle('dark', on);
  const darkModeIconUse = document.querySelector('#darkModeIcon use');
  if(darkModeIconUse) darkModeIconUse.setAttribute('href', on ? '#i-sun' : '#i-moon');
  const darkModeBtn = document.getElementById('darkModeBtn');
  if(darkModeBtn) darkModeBtn.title = on ? 'Passer en mode clair' : 'Passer en mode sombre';
  try{ localStorage.setItem('fiscaltrack-dark', on ? '1' : '0'); }catch(e){}
}
const darkModeBtnEl = document.getElementById('darkModeBtn');
if(darkModeBtnEl) darkModeBtnEl.addEventListener('click', ()=>{
  applyDarkMode(!document.body.classList.contains('dark'));
});
(function(){
  let saved = false;
  try{ saved = localStorage.getItem('fiscaltrack-dark')==='1'; }catch(e){}
  applyDarkMode(saved);
  try{
    if(localStorage.getItem('fiscaltrack-compact')==='1') document.body.classList.add('compact-ui');
  }catch(e){}
})();

const profileMenuBtn = document.getElementById('profileMenuBtn');
if(profileMenuBtn) profileMenuBtn.addEventListener('click', e=>{
  e.stopPropagation();
  goTo('profile');
});
const settingsMenuBtn = document.getElementById('settingsMenuBtn');
if(settingsMenuBtn) settingsMenuBtn.addEventListener('click', e=>{
  e.stopPropagation();
  goTo('settings');
});

/* ================= PLEIN ÉCRAN (F11) ================= */
function updateFullscreenIcon(){
  const use = document.querySelector('#fullscreenIcon use');
  const on = !!document.fullscreenElement;
  use.setAttribute('href', on ? '#i-compress' : '#i-expand');
  document.getElementById('fullscreenBtn').title = on ? 'Quitter le plein écran' : 'Plein écran (F11)';
}
const fullscreenBtn = document.getElementById('fullscreenBtn');
if(fullscreenBtn) fullscreenBtn.addEventListener('click', ()=>{
  if(!document.fullscreenElement){
    document.documentElement.requestFullscreen().catch(()=>{});
  } else {
    document.exitFullscreen().catch(()=>{});
  }
});
document.addEventListener('fullscreenchange', updateFullscreenIcon);

/* ================= DROPDOWNS ================= */
function toggleDropdown(id, others){
  const el = document.getElementById(id);
  const willOpen = !el.classList.contains('open');
  others.forEach(o=>document.getElementById(o).classList.remove('open'));
  el.classList.toggle('open', willOpen);
}
const notifBtn = document.getElementById('notifBtn');
if(notifBtn) notifBtn.addEventListener('click', e=>{e.stopPropagation();toggleDropdown('notifDropdown',['userDropdown']);});
const userBtn = document.getElementById('userBtn');
if(userBtn) userBtn.addEventListener('click', e=>{e.stopPropagation();toggleDropdown('userDropdown',['notifDropdown']);});
document.addEventListener('click', ()=>{
  const notifDropdown = document.getElementById('notifDropdown');
  const userDropdown = document.getElementById('userDropdown');
  if(notifDropdown) notifDropdown.classList.remove('open');
  if(userDropdown) userDropdown.classList.remove('open');
});

const logoutBtn = document.getElementById('logoutBtn');
if(logoutBtn) logoutBtn.addEventListener('click', ()=>{
  const logoutForm = document.getElementById('logout-form');
  if(confirm('Voulez-vous vraiment vous déconnecter ?')){
    if(logoutForm) logoutForm.submit();
  }
});

/* ================= RENDER: NOTIFICATIONS ================= */
const notifIcons = {warn:'i-alert', late:'i-alert', ok:'i-check'};
function renderNotifications(){
  if(!notifications.length && obligations.length){
    notifications = buildNotificationsFromObligations();
  }
  let list = notifications.slice();
  try{
    if(localStorage.getItem('fiscaltrack-notif-late')==='0'){
      list = list.filter(n=>n.tone!=='late');
    }
  }catch(e){}
  const showBadge = (function(){
    try{ return localStorage.getItem('fiscaltrack-notif-badge')!=='0'; }catch(e){ return true; }
  })();
  const unreadCount = list.filter(n=>n.unread).length;
  const notifDot = document.getElementById('notifDot');
  if(notifDot) notifDot.style.display = (showBadge && unreadCount) ? 'block' : 'none';

  const navBadge = document.getElementById('navNotifBadge');
  if(navBadge){
    navBadge.textContent = unreadCount;
    navBadge.style.display = (showBadge && unreadCount) ? 'flex' : 'none';
  }

  const build = () => list.length ? list.map(n=>`
    <div class="notif-item ${n.tone} ${n.unread?'unread':''}" ${n.obligation_id?`onclick="goTo('declarations')" style="cursor:pointer"`:''}>
      <div class="ic"><svg><use href="#${notifIcons[n.tone]||'i-alert'}"/></svg></div>
      <div class="txt"><b>${n.title}</b><br>${n.text}<div class="when">${n.when||''}</div></div>
    </div>`).join('') : `<div class="empty" style="padding:16px;text-align:center;color:var(--text-400);font-size:12.5px;">Aucune alerte d'échéance pour le moment.</div>`;
  const notifList = document.getElementById('notifList');
  if(notifList) notifList.innerHTML = build();
  const full = document.getElementById('notifListFull');
  if(full) full.innerHTML = build();
}
async function markAllRead(){
  try{
    const res = await fetch('/api/notifications/read-all', {
      method:'POST',
      headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With':'XMLHttpRequest' }
    });
    if(res.ok){
      const data = await res.json();
      notifications = data.notifications || [];
    } else {
      notifications.forEach(n=>{ n.unread=false; });
    }
  }catch(e){
    notifications.forEach(n=>{ n.unread=false; });
  }
  renderNotifications();
}
const markAllReadBtn = document.getElementById('markAllRead');
if(markAllReadBtn) markAllReadBtn.addEventListener('click', markAllRead);
const markAllReadSectionBtn = document.getElementById('markAllReadSection');
if(markAllReadSectionBtn) markAllReadSectionBtn.addEventListener('click', markAllRead);

function renderTimeline(){
  if(!document.getElementById('timelineList')) return;
  const open = obligations
    .filter(o=> o.date_limite && o.statut !== 'declare' && o.statut !== 'justificatif_depose')
    .slice()
    .sort((a,b)=> parseISODate(a.date_limite) - parseISODate(b.date_limite))
    .slice(0,6);
  const months=['','JAN','FÉV','MAR','AVR','MAI','JUIN','JUIL','AOÛT','SEP','OCT','NOV','DÉC'];
  document.getElementById('timelineList').innerHTML = open.length ? open.map(o=>{
    const echeance = parseISODate(o.date_limite);
    const eff = o.statut_effectif || o.statut;
    const badge = eff === 'penalite' ? 'penalite' : (eff === 'a_declarer' ? 'non_declare' : eff);
    const piece = o.has_justificatif ? 'Pièce OK' : 'Pièce manquante';
    return `<div class="tl-row">
      <div class="tl-date"><div class="d">${pad2(echeance.getDate())}</div><div class="m">${months[echeance.getMonth()+1]}</div></div>
      <div>
        <div class="tl-info"><b>${o.contribuable_nom}</b><span>${o.type_nom} · ${o.periode_label || o.periode} ${o.annee} · ${piece}</span></div>
        <div class="tl-bar" style="margin-top:6px;"><i style="width:${eff==='penalite'?100:40}%;${eff==='penalite'?'background:linear-gradient(90deg,#e07a7a,#d94c4c)':''}"></i></div>
      </div>
      <span class="badge ${badgeClass(badge === 'justificatif_depose' ? 'declare' : badge)}">${statutLabel(badge)}</span>
    </div>`;
  }).join('') : `<div class="empty" style="padding:24px 0;text-align:center;color:var(--text-400);font-size:12.5px;">Aucune échéance ouverte. Créez des obligations depuis l'écran Déclaration.</div>`;
}
function renderFeed(){
  if(!document.getElementById('activityFeed')) return;
  const alerts = (notifications.length ? notifications : buildNotificationsFromObligations()).slice(0,8);
  document.getElementById('activityFeed').innerHTML = alerts.length ? alerts.map(a=>`
    <div class="feed-item"><div class="feed-dot"></div>
      <div><p><b>${a.title}</b> — ${a.text}</p><time>${a.when||''}</time></div>
    </div>`).join('') : `<div class="empty" style="padding:24px;text-align:center;color:var(--text-400);font-size:12.5px;">Aucune alerte prioritaire.</div>`;
}
function renderKPIs(){
  if(!document.getElementById('kpiContribuables')) return;
  const k = suiviKpis || {};
  const open = obligations.filter(o=>o.statut!=='declare' && o.statut!=='justificatif_depose');
  const retards = open.filter(o=>o.echeance_bucket==='retard' || o.statut_effectif==='penalite').length;
  const proches = open.filter(o=>o.echeance_bucket==='proche' || o.echeance_bucket==='aujourdhui').length;
  const sansPiece = open.filter(o=>!o.has_justificatif).length;
  const cloturees = obligations.filter(o=>o.statut==='declare' || o.statut==='justificatif_depose').length;
  const total = obligations.length;
  const conf = total ? Math.round((cloturees/total)*100) : null;

  setText('kpiContribuables', k.contribuables_actifs != null ? k.contribuables_actifs : contribuables.filter(c=>c.statut==='active').length);
  setText('kpiDeclarationsEnAttente', k.obligations_en_attente != null ? k.obligations_en_attente : open.length);
  setText('kpiRetards', k.obligations_en_retard != null ? k.obligations_en_retard : retards);
  setText('kpiProches', k.obligations_proches != null ? (k.obligations_proches + (k.obligations_aujourdhui||0)) : proches);
  setText('kpiSansPiece', k.sans_justificatif != null ? k.sans_justificatif : sansPiece);
  setText('kpiDocuments', k.documents_ged != null ? k.documents_ged : documents.length);
  setText('kpiConformite', (k.taux_conformite != null ? k.taux_conformite : conf) != null ? ((k.taux_conformite != null ? k.taux_conformite : conf)+'%') : '—');
  setText('kpiCloturees', k.obligations_cloturees != null ? k.obligations_cloturees : cloturees);
}
function setText(id, val){
  const el = document.getElementById(id);
  if(el) el.textContent = val;
}
function renderObligationKpis(){
  const open = obligations.filter(o=>o.statut!=='declare' && o.statut!=='justificatif_depose');
  setText('declKpiAttente', open.length);
  setText('declKpiRetard', open.filter(o=>o.echeance_bucket==='retard' || o.statut_effectif==='penalite').length);
  setText('declKpiProches', open.filter(o=>o.echeance_bucket==='proche' || o.echeance_bucket==='aujourdhui').length);
  setText('declKpiSansPiece', open.filter(o=>!o.has_justificatif).length);
}

/* ================= CALENDRIER DES ÉCHÉANCES (dashboard) ================= */
let calendarViewDate = new Date();
function getMonthMatrix(year, month){
  const first = new Date(year, month, 1);
  const startDow = (first.getDay()+6)%7; // lundi = 0
  const daysInMonth = new Date(year, month+1, 0).getDate();
  const cells = [];
  for(let i=0;i<startDow;i++) cells.push(null);
  for(let d=1; d<=daysInMonth; d++) cells.push(d);
  while(cells.length%7!==0) cells.push(null);
  return cells;
}
function getEcheancesForMonth(year, month){
  const map = {};
  obligations.forEach(t=>{
    if(!t.date_limite) return;
    if(t.statut === 'declare' || t.statut === 'justificatif_depose') return;
    const echeance = parseISODate(t.date_limite);
    if(echeance.getFullYear()===year && echeance.getMonth()===month){
      (map[echeance.getDate()] = map[echeance.getDate()]||[]).push(t);
    }
  });
  return map;
}
function renderCalendar(){
  if(!document.getElementById('calendarGrid')) return;
  const year = calendarViewDate.getFullYear();
  const month = calendarViewDate.getMonth();
  const monthNames=['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
  document.getElementById('calMonthLabel').textContent = `${monthNames[month]} ${year}`;
  document.getElementById('calDow').innerHTML = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'].map(l=>`<span>${l}</span>`).join('');
  const cells = getMonthMatrix(year, month);
  const echMap = getEcheancesForMonth(year, month);
  const today = new Date();
  document.getElementById('calendarGrid').innerHTML = cells.map(day=>{
    if(day===null) return `<div class="cal-cell empty"></div>`;
    const has = echMap[day];
    const isToday = day===today.getDate() && month===today.getMonth() && year===today.getFullYear();
    return `<div class="cal-cell ${has?'has-event':''} ${isToday?'is-today':''}" ${has?`onclick="showCalendarDay(${day})"`:''}>
      ${day}${has?'<span class="dot"></span>':''}
    </div>`;
  }).join('');
  document.getElementById('calendarDetails').innerHTML = '';
}
function calNav(delta){
  calendarViewDate = new Date(calendarViewDate.getFullYear(), calendarViewDate.getMonth()+delta, 1);
  renderCalendar();
}
function showCalendarDay(day){
  const year = calendarViewDate.getFullYear();
  const month = calendarViewDate.getMonth();
  const items = getEcheancesForMonth(year, month)[day] || [];
  document.getElementById('calendarDetails').innerHTML = items.length ? `
    <div class="cal-details-head">Échéances du ${pad2(day)}/${pad2(month+1)}/${year}</div>
    ${items.map(o=>{
      const badge = o.statut_effectif === 'penalite' ? 'penalite' : (o.statut === 'a_declarer' ? 'non_declare' : o.statut);
      return `<div class="cal-details-item"><span><b>${o.contribuable_nom}</b> — ${o.type_nom} (${o.periode} ${o.annee})</span><span class="badge ${badgeClass(badge === 'justificatif_depose' ? 'declare' : badge)}">${statutLabel(badge)}</span></div>`;
    }).join('')}
  ` : '';
}

/* ================= HELPERS ================= */
function statutLabel(s){
  return {
    non_declare:'À déclarer', a_declarer:'À déclarer', declare:'Déclaré',
    justificatif_depose:'Justificatif déposé', penalite:'En retard', aucun:'Aucun'
  }[s] || s;
}
function badgeClass(s){
  return {
    non_declare:'b-todo', a_declarer:'b-todo', declare:'b-done',
    justificatif_depose:'b-done', penalite:'b-late', aucun:'b-inactive'
  }[s] || 'b-todo';
}
function computeCellStatut(contribId, docTypeId){
  const year = new Date().getFullYear();
  const matches = obligations.filter(o =>
    Number(o.contribuable_id)===Number(contribId) &&
    Number(o.tracked_doc_type_id)===Number(docTypeId) &&
    Number(o.annee)===year
  );
  if(!matches.length){
    const entry = docStatusMatrix[contribId+'||'+docTypeId];
    if(!entry) return 'aucun';
    if(entry.statut === 'declare') return 'declare';
    const type = trackedDocTypes.find(t=>Number(t.id)===Number(docTypeId));
    if(type && type.dateLimite && startOfDay(type.dateLimite) < startOfDay(new Date())) return 'penalite';
    return 'non_declare';
  }
  const open = matches.filter(o=>o.statut!=='declare' && o.statut!=='justificatif_depose');
  if(!open.length){
    return matches.some(o=>o.statut==='justificatif_depose') ? 'justificatif_depose' : 'declare';
  }
  if(open.some(o=>o.statut_effectif==='penalite' || o.echeance_bucket==='retard')) return 'penalite';
  return 'non_declare';
}
function fmtFCFA(n){return n.toLocaleString('fr-FR')+' FCFA';}
function initials(name){return name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();}

/* ================= RENDER: CONTRIBUABLES (tableau du dossier) ================= */
function money(n){ return (n||0).toLocaleString('fr-FR'); }
function d(v){ return v || '—'; }
function renderContribuables(){
  if(!document.getElementById('contribTbody')) return;
  const q = (document.getElementById('contribSearch').value||'').toLowerCase();
  const cat = document.getElementById('contribFilterCat').value;
  const regime = document.getElementById('contribFilterRegime').value;
  const rows = contribuables.filter(c=>
  ((c.nom||'').toLowerCase().includes(q) || (c.niu||'').toLowerCase().includes(q)) &&
    (!cat || c.cat===cat) && (!regime || c.regime===regime)
  );
  document.getElementById('contribCount').textContent = rows.length+' résultat(s) sur '+contribuables.length;
  document.getElementById('contribTbody').innerHTML = rows.length ? rows.map(c=>{
    const idx = contribuables.indexOf(c);
    return `
    <tr>
      <td class="name-cell sticky-col">${c.nom}</td>
      <td class="mono">${c.niu}</td>
      <td>${c.regime} · ${c.cat}</td>
      <td class="mono">${c.pass || '—'}</td>
      <td class="mono">${money(c.montant)}</td>
      <td class="mono">${money(c.t1)}</td>
      <td class="mono">${money(c.t2)}</td>
      <td class="mono">${money(c.t3)}</td>
      <td class="mono">${money(c.t4)}</td>
      <td class="mono">${money(c.tdl)}</td>
      <td class="mono">${money(c.impots)}</td>
      <td class="mono">${money(c.loyer)}</td>
      <td class="mono">${money(c.bail)}</td>
      <td class="mono">${money(c.precompte)}</td>
      <td class="mono">${money(c.timbre)}</td>
      <td class="mono">${money(c.fraisPaiement)}</td>
      <td class="mono">${money(c.fsPaye)}</td>
      <td class="mono">${money(c.fsNonPaye)}</td>
      <td>${d(c.aiIgs)}</td>
      <td>${d(c.aiBail)}</td>
      <td>${d(c.aiPrecompte)}</td>
      <td>${d(c.qIgs)}</td>
      <td>${d(c.qBail)}</td>
      <td>${d(c.qPrecompte)}</td>
      <td>${d(c.acfIgs)}</td>
      <td>${d(c.acfBail)}</td>
      <td>${d(c.acfPrecompte)}</td>
      <td>${d(c.lieu)}</td>
      <td class="mono">${d(c.tel)}</td>
      <td class="sticky-col print-hide" style="left:auto;right:0;box-shadow:-2px 0 4px rgba(16,29,71,.06);">
        <div class="row-actions">
          <button class="mini-btn" title="Consulter" onclick="consultContribuable(${idx})"><svg><use href="#i-eye"/></svg></button>
          <button class="mini-btn" title="Modifier" onclick="editContribuable(${idx})"><svg><use href="#i-edit"/></svg></button>
          <button class="mini-btn" title="Supprimer" onclick="deleteContribuable(${idx})"><svg><use href="#i-trash"/></svg></button>
        </div>
      </td>
    </tr>`;
  }).join('') : `<tr><td colspan="30" class="empty">Aucun contribuable ne correspond à votre recherche. Cliquez sur « Ajouter un contribuable » pour commencer.</td></tr>`;
}
async function deleteContribuable(i){
  if(!confirm('Supprimer ce contribuable du dossier ?')) return;
  try{
    await apiUsers(`/contribuables/${contribuables[i].id}`, 'DELETE');
    contribuables.splice(i,1);
    renderContribuables();
    renderKPIs();
  }catch(e){ alert(e.message); }
}
function resetContribuables(){
  document.getElementById('contribSearch').value='';
  document.getElementById('contribFilterCat').selectedIndex=0;
  document.getElementById('contribFilterRegime').selectedIndex=0;
  renderContribuables();
}

/* ---- export du dossier (PDF / Excel) ---- */
function getVisibleContribuables(){
  const q = (document.getElementById('contribSearch').value||'').toLowerCase();
  const cat = document.getElementById('contribFilterCat').value;
  const regime = document.getElementById('contribFilterRegime').value;
  return contribuables.filter(c=>
    (c.nom.toLowerCase().includes(q) || c.niu.toLowerCase().includes(q)) &&
    (!cat || c.cat===cat) && (!regime || c.regime===regime)
  );
}
function contribExportHeader(){
  return ['Contribuable','NIU','Régime et classe','Mot de passe','Montant payé',
    `IGS ${dossier.annee} T1`,`IGS ${dossier.annee} T2`,`IGS ${dossier.annee} T3`,`IGS ${dossier.annee} T4`,`IGS ${dossier.annee} TDL`,
    'Impôts payé','Loyer','Bail','Précompte','Timbre','Frais de paiement',
    'Frais de suivi - Payé','Frais de suivi - Non payé',
    "Avis d'imposition - IGS","Avis d'imposition - Bail","Avis d'imposition - Précompte",
    'Quittance - IGS','Quittance - Bail','Quittance - Précompte',
    'ACF - IGS','ACF - Bail','ACF - Précompte','Lieu','Téléphone'];
}
function contribExportRows(){
  return contribuables.map(c=>[
    c.nom, c.niu, `${c.regime} · ${c.cat}`, c.pass||'••••••', c.montant||0,
    c.t1||0, c.t2||0, c.t3||0, c.t4||0, c.tdl||0,
    c.impots||0, c.loyer||0, c.bail||0, c.precompte||0, c.timbre||0, c.fraisPaiement||0,
    c.fsPaye||0, c.fsNonPaye||0,
    c.aiIgs||'—', c.aiBail||'—', c.aiPrecompte||'—',
    c.qIgs||'—', c.qBail||'—', c.qPrecompte||'—',
    c.acfIgs||'—', c.acfBail||'—', c.acfPrecompte||'—',
    c.lieu||'—', c.tel||'—'
  ]);
}
function exportContribuablesExcel(){
  if(typeof XLSX==='undefined'){ alert("La bibliothèque Excel n'a pas pu se charger (vérifiez votre connexion internet)."); return; }
  const header = contribExportHeader();
  const rows = contribExportRows();
  if(!rows.length){ alert('Aucun contribuable à exporter.'); return; }
  const titleRow = [`SUIVI DES DOSSIERS : ${dossier.nom.toUpperCase()} ${dossier.annee}`];
  const ws = XLSX.utils.aoa_to_sheet([titleRow, [], header, ...rows]);
  ws['!merges'] = [{ s:{r:0,c:0}, e:{r:0,c:header.length-1} }];
  ws['!cols'] = header.map(h=>({ wch: Math.max(10, Math.min(24, h.length+4)) }));
  ws['!rows'] = [{ hpt:22 }];
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Contribuables');
  XLSX.writeFile(wb, `Suivi_${dossier.nom.replace(/\s+/g,'_')}_${dossier.annee}.xlsx`);
}
function exportContribuablesPDF(){
  if(!contribuables.length){ alert('Aucun contribuable à exporter.'); return; }
  const prevSearch = document.getElementById('contribSearch').value;
  const prevCat = document.getElementById('contribFilterCat').value;
  const prevRegime = document.getElementById('contribFilterRegime').value;
  resetContribuables(); // affiche temporairement la totalité du tableau pour l'impression
  document.body.classList.add('print-contribuables');
  const restore = ()=>{
    document.body.classList.remove('print-contribuables');
    document.getElementById('contribSearch').value = prevSearch;
    document.getElementById('contribFilterCat').value = prevCat;
    document.getElementById('contribFilterRegime').value = prevRegime;
    renderContribuables();
  };
  window.addEventListener('afterprint', restore, { once:true });
  setTimeout(()=>{ window.print(); }, 50);
  setTimeout(restore, 4000); // filet de sécurité si 'afterprint' ne se déclenche pas
}
['contribSearch'].forEach(id=>{
  const el = document.getElementById(id);
  if(el) el.addEventListener('input', renderContribuables);
});
['contribFilterCat','contribFilterRegime'].forEach(id=>{
  const el = document.getElementById(id);
  if(el) el.addEventListener('change', renderContribuables);
});

/* ================= RENDER: DOCUMENTS ================= */
function renderDocuments(){
  if(!document.getElementById('docTbody')) return;
  const q = (document.getElementById('docSearch').value||'').toLowerCase();
  const type = document.getElementById('docFilterType').value;
  const rows = documents.filter(d=> (d.nom.toLowerCase().includes(q)||d.contrib.toLowerCase().includes(q)) && (!type || d.type===type));
  document.getElementById('docCount').textContent = rows.length+' résultat(s) sur '+documents.length;
  document.getElementById('docTbody').innerHTML = rows.length ? rows.map(d=>{
    const idx = documents.indexOf(d);
    return `
    <tr>
      <td><div class="cell-strong">${d.nom}</div><div class="cell-sub">${d.type}</div></td>
      <td>${d.contrib}</td>
      <td>${d.fournisseur}</td>
      <td class="mono">${d.montant?fmtFCFA(d.montant):'—'}</td>
      <td>${d.date}</td>
      <td>${d.dateModif || d.date}</td>
      <td><div class="row-actions">
        <button class="mini-btn" title="Aperçu" onclick="viewDocument(${idx})"><svg><use href="#i-eye"/></svg></button>
        <button class="mini-btn" title="Modifier" onclick="editDocument(${idx})"><svg><use href="#i-edit"/></svg></button>
        <button class="mini-btn" title="Archiver" onclick="archiveDocument(${idx})"><svg><use href="#i-archive"/></svg></button>
      </div></td>
    </tr>`;
  }).join('') : `<tr><td colspan="7" class="empty">Aucun document ne correspond à votre recherche.</td></tr>`;
}
async function archiveDocument(i){
  if(!confirm('Archiver ce document ?')) return;
  try{
    const data = await apiUsers(`/documents/${documents[i].id}/archive`, 'PATCH');
    documents.splice(i,1);
    archivedDocuments.unshift(data.document);
    renderDocuments();
    renderArchives();
    renderKPIs();
  }catch(e){ alert(e.message); }
}
let ficheDocIndex = null;
function viewDocument(i){
  const d = documents[i];
  ficheDocIndex = i;
  document.getElementById('fiche-doc-nom').textContent = d.nom;
  document.getElementById('fiche-doc-type').textContent = d.type;
  document.getElementById('fiche-doc-contrib').textContent = d.contrib;
  document.getElementById('fiche-doc-fournisseur').textContent = d.fournisseur;
  document.getElementById('fiche-doc-montant').textContent = d.montant ? fmtFCFA(d.montant) : '—';
  document.getElementById('fiche-doc-date').textContent = d.date;
  const previewWrap = document.getElementById('fiche-doc-preview-wrap');
  const preview = document.getElementById('fiche-doc-preview');
  const noFile = document.getElementById('fiche-doc-nofile');
  if(d.fileUrl && (d.fileMime||'').startsWith('image')){
    previewWrap.style.display=''; noFile.style.display='none';
    preview.innerHTML = `<img src="${d.fileUrl}" style="max-width:100%;border-radius:8px;border:1px solid var(--line);">`;
  } else if(d.fileUrl){
    previewWrap.style.display=''; noFile.style.display='none';
    preview.innerHTML = `<embed src="${d.fileUrl}" type="${d.fileMime||'application/pdf'}" style="width:100%;height:420px;border-radius:8px;border:1px solid var(--line);">`;
  } else {
    previewWrap.style.display='none'; noFile.style.display='';
  }
  openModal('modalDocumentView');
}
const ficheDocDownloadBtn = document.getElementById('fiche-doc-download-btn');
if(ficheDocDownloadBtn) ficheDocDownloadBtn.addEventListener('click', ()=>{
  if(ficheDocIndex===null) return;
  const d = documents[ficheDocIndex];
  if(d.fileUrl){
    const a = document.createElement('a');
    a.href = d.fileUrl;
    a.download = d.fileName || d.nom;
    document.body.appendChild(a); a.click(); a.remove();
  } else {
    alert("Aucun fichier numérique n'a été associé à ce document.");
  }
});
const docSearchInput = document.getElementById('docSearch');
if(docSearchInput) docSearchInput.addEventListener('input', renderDocuments);
const docFilterType = document.getElementById('docFilterType');
if(docFilterType) docFilterType.addEventListener('change', renderDocuments);
function resetDocuments(){
  const search = document.getElementById('docSearch');
  const filter = document.getElementById('docFilterType');
  if(search) search.value='';
  if(filter) filter.selectedIndex=0;
  renderDocuments();
}

/* ================= RENDER: ARCHIVES ================= */
function renderArchives(){
  if(!document.getElementById('archiveTbody')) return;
  const q = (document.getElementById('archiveSearch').value||'').toLowerCase();
  const rows = archivedDocuments.filter(d=> d.nom.toLowerCase().includes(q)||d.contrib.toLowerCase().includes(q));
  document.getElementById('archiveCount').textContent = rows.length+' résultat(s) sur '+archivedDocuments.length;
  document.getElementById('archiveTbody').innerHTML = rows.length ? rows.map(d=>{
    const idx = archivedDocuments.indexOf(d);
    return `
    <tr>
      <td><div class="cell-strong">${d.nom}</div><div class="cell-sub">${d.type}</div></td>
      <td>${d.contrib}</td>
      <td>${d.fournisseur}</td>
      <td class="mono">${d.montant?fmtFCFA(d.montant):'—'}</td>
      <td>${d.archivedDate||'—'}</td>
      <td><div class="row-actions">
        <button class="mini-btn" title="Restaurer" onclick="restoreDocument(${idx})"><svg><use href="#i-restore"/></svg></button>
        <button class="mini-btn" title="Supprimer définitivement" onclick="deleteArchivedDocument(${idx})"><svg><use href="#i-trash"/></svg></button>
      </div></td>
    </tr>`;
  }).join('') : `<tr><td colspan="6" class="empty">Aucun document archivé.</td></tr>`;
}
async function restoreDocument(i){
  if(!confirm('Restaurer ce document dans la liste des documents ?')) return;
  try{
    const data = await apiUsers(`/documents/${archivedDocuments[i].id}/restore`, 'PATCH');
    archivedDocuments.splice(i,1);
    documents.unshift(data.document);
    renderArchives();
    renderDocuments();
    renderKPIs();
  }catch(e){ alert(e.message); }
}
 
async function deleteArchivedDocument(i){
  if(!confirm('Supprimer définitivement ce document ? Cette action est irréversible.')) return;
  try{
    await apiUsers(`/documents/${archivedDocuments[i].id}`, 'DELETE');
    archivedDocuments.splice(i,1);
    renderArchives();
  }catch(e){ alert(e.message); }
}
const archiveSearchInput = document.getElementById('archiveSearch');
if(archiveSearchInput) archiveSearchInput.addEventListener('input', renderArchives);
function resetArchives(){
  const archiveSearch = document.getElementById('archiveSearch');
  if(archiveSearch) archiveSearch.value='';
  renderArchives();
}

/* ================= RENDER: OBLIGATIONS + MATRICE ================= */
function openObligationModal(){
  const selC = document.getElementById('f-obl-contrib');
  const selT = document.getElementById('f-obl-type');
  if(!selC || !selT){ alert('Formulaire obligation indisponible.'); return; }
  selC.innerHTML = contribuables.map(c=>`<option value="${c.id}">${c.nom}</option>`).join('') || '<option value="">—</option>';
  selT.innerHTML = trackedDocTypes.map(t=>`<option value="${t.id}">${t.nom}</option>`).join('') || '<option value="">—</option>';
  document.getElementById('f-obl-annee').value = new Date().getFullYear();
  document.getElementById('f-obl-periode').value = 'T1';
  document.getElementById('f-obl-deadline').value = '';
  document.getElementById('f-obl-org').value = '';
  openModal('modalObligation');
}
async function submitObligation(){
  const payload = {
    contribuable_id: Number(document.getElementById('f-obl-contrib').value),
    tracked_doc_type_id: Number(document.getElementById('f-obl-type').value),
    annee: Number(document.getElementById('f-obl-annee').value),
    periode: document.getElementById('f-obl-periode').value,
    date_limite: document.getElementById('f-obl-deadline').value || null,
    organisme: document.getElementById('f-obl-org').value || null,
  };
  if(!payload.contribuable_id || !payload.tracked_doc_type_id){
    alert('Contribuable et type obligatoires.'); return;
  }
  try{
    const data = await apiUsers('/obligations', 'POST', payload);
    obligations.push(data.obligation);
    applySuiviKpis(data.kpis);
    syncMatrixFromObligations();
    closeModal('modalObligation');
    refreshSuiviUI();
  }catch(e){ alert(e.message); }
}
function filteredObligations(){
  const q = ((document.getElementById('oblSearch')||{}).value||'').toLowerCase();
  const annee = (document.getElementById('oblFilterAnnee')||{}).value || '';
  const periode = (document.getElementById('oblFilterPeriode')||{}).value || '';
  const typeId = (document.getElementById('oblFilterType')||{}).value || '';
  const org = (document.getElementById('oblFilterOrg')||{}).value || '';
  const statut = (document.getElementById('oblFilterStatut')||{}).value || '';
  const ech = (document.getElementById('oblFilterEcheance')||{}).value || '';
  const piece = (document.getElementById('oblFilterPiece')||{}).value || '';
  return obligations.filter(o=>{
    if(q && !(String(o.contribuable_nom||'').toLowerCase().includes(q) || String(o.contribuable_niu||'').toLowerCase().includes(q))) return false;
    if(annee && String(o.annee)!==String(annee)) return false;
    if(periode && o.periode!==periode) return false;
    if(typeId && Number(o.tracked_doc_type_id)!==Number(typeId)) return false;
    if(org && (o.organisme||'')!==org) return false;
    if(statut){
      const eff = o.statut_effectif || o.statut;
      if(statut === 'penalite'){ if(eff !== 'penalite') return false; }
      else if(statut === 'a_declarer'){ if(o.statut !== 'a_declarer') return false; }
      else if(o.statut !== statut) return false;
    }
    if(ech && (o.echeance_bucket||'')!==ech) return false;
    if(piece==='manquante' && o.has_justificatif) return false;
    if(piece==='ok' && !o.has_justificatif) return false;
    return true;
  }).slice().sort((a,b)=>{
    const da = a.date_limite ? parseISODate(a.date_limite).getTime() : Infinity;
    const db = b.date_limite ? parseISODate(b.date_limite).getTime() : Infinity;
    return da - db;
  });
}
function populateObligationFilters(){
  const ySel = document.getElementById('oblFilterAnnee');
  const tSel = document.getElementById('oblFilterType');
  if(ySel && ySel.options.length<=1){
    const years = [...new Set(obligations.map(o=>o.annee))].sort((a,b)=>b-a);
    if(!years.includes(new Date().getFullYear())) years.unshift(new Date().getFullYear());
    years.forEach(y=>{ const opt=document.createElement('option'); opt.value=y; opt.textContent=y; ySel.appendChild(opt); });
  }
  if(tSel && tSel.options.length<=1){
    trackedDocTypes.forEach(t=>{ const opt=document.createElement('option'); opt.value=t.id; opt.textContent=t.nom; tSel.appendChild(opt); });
  }
}
function renderObligationsList(){
  const tbody = document.getElementById('oblTbody');
  if(!tbody){ renderComplianceList(); return; }
  populateObligationFilters();
  const rows = filteredObligations();
  const countEl = document.getElementById('oblCount');
  if(countEl) countEl.textContent = rows.length+' résultat(s) sur '+obligations.length;
  if(!obligations.length){
    tbody.innerHTML = `<tr><td colspan="8" class="empty">Aucune obligation. Cliquez sur « Nouvelle obligation » pour démarrer le suivi.</td></tr>`;
    renderObligationKpis(); renderComplianceList(); return;
  }
  if(!rows.length){
    tbody.innerHTML = `<tr><td colspan="8" class="empty">Aucun résultat pour ces filtres.</td></tr>`;
    renderObligationKpis(); return;
  }
  tbody.innerHTML = rows.map(o=>{
    const eff = o.statut_effectif || o.statut;
    const badge = eff === 'a_declarer' ? 'non_declare' : eff;
    const deadline = o.date_limite ? fmtFR(parseISODate(o.date_limite)) : '—';
    const jours = o.jours_restants;
    const joursTxt = jours==null ? '' : (jours<0 ? ` · retard ${-jours}j` : (jours===0 ? ' · aujourd\'hui' : ` · J-${jours}`));
    const piece = o.has_justificatif
      ? `<span class="badge b-done">Joint</span>`
      : `<span class="badge b-late">Manquant</span>`;
    return `<tr>
      <td class="cell-strong sticky-col">${o.contribuable_nom}<div style="font-size:11px;color:var(--text-400);">${o.contribuable_niu||''}</div></td>
      <td>${o.type_nom}</td>
      <td>${o.periode_label||o.periode} ${o.annee}</td>
      <td>${deadline}<div style="font-size:11px;color:var(--text-400);">${joursTxt}</div></td>
      <td>${o.organisme||'—'}</td>
      <td>
        <select class="inline-select statut-select statut-${badge}" onchange="changeObligationStatut(${o.id}, this.value)">
          <option value="a_declarer" ${o.statut==='a_declarer'?'selected':''}>À déclarer</option>
          <option value="declare" ${o.statut==='declare'?'selected':''}>Déclaré</option>
          <option value="justificatif_depose" ${o.statut==='justificatif_depose'?'selected':''}>Justificatif déposé</option>
        </select>
        ${eff==='penalite'?'<div style="margin-top:4px;"><span class="badge b-late">En retard</span></div>':''}
      </td>
      <td>${piece}</td>
      <td>
        <div class="row-actions">
          <button class="mini-btn" title="Joindre justificatif" onclick="openJustificatifModal(${o.id})"><svg><use href="#i-folder"/></svg></button>
          <button class="mini-btn" title="Supprimer" onclick="deleteObligation(${o.id})"><svg><use href="#i-x"/></svg></button>
        </div>
      </td>
    </tr>`;
  }).join('');
  renderObligationKpis();
  renderComplianceList();
}
function resetObligationFilters(){
  ['oblSearch','oblFilterAnnee','oblFilterPeriode','oblFilterType','oblFilterOrg','oblFilterStatut','oblFilterEcheance','oblFilterPiece'].forEach(id=>{
    const el = document.getElementById(id);
    if(!el) return;
    if(el.tagName==='INPUT') el.value=''; else el.selectedIndex=0;
  });
  renderObligationsList();
}
async function changeObligationStatut(id, statut){
  const o = findObligation(id);
  if(!o) return;
  if((statut==='declare' || statut==='justificatif_depose') && !o.has_justificatif){
    alert("Impossible de clôturer sans justificatif GED. Déposez d'abord une pièce.");
    openJustificatifModal(id);
    renderObligationsList();
    return;
  }
  try{
    const data = await apiUsers(`/obligations/${id}`, 'PATCH', { statut });
    const idx = obligations.findIndex(x=>Number(x.id)===Number(id));
    if(idx>=0) obligations[idx] = data.obligation;
    applySuiviKpis(data.kpis);
    syncMatrixFromObligations();
    refreshSuiviUI();
  }catch(e){ alert(e.message); renderObligationsList(); }
}
function openJustificatifModal(id){
  const o = findObligation(id);
  if(!o) return;
  document.getElementById('f-obl-justif-id').value = id;
  document.getElementById('f-obl-justif-nom').value = `Justificatif — ${o.type_nom} ${o.periode} ${o.annee}`;
  document.getElementById('f-obl-justif-file').value = '';
  document.getElementById('oblJustifHint').textContent =
    `Obligation : ${o.contribuable_nom} — ${o.type_nom} (${o.periode} ${o.annee}). Le fichier est obligatoire pour clôturer.`;
  openModal('modalOblJustificatif');
}
async function submitObligationJustificatif(){
  const id = document.getElementById('f-obl-justif-id').value;
  const fileInput = document.getElementById('f-obl-justif-file');
  if(!fileInput.files || !fileInput.files[0]){ alert('Sélectionnez un fichier.'); return; }
  const fd = new FormData();
  fd.append('nom', document.getElementById('f-obl-justif-nom').value || '');
  fd.append('fichier', fileInput.files[0]);
  try{
    const res = await fetch(`/obligations/${id}/justificatif`, {
      method:'POST',
      headers:{ 'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
      body: fd
    });
    const data = await res.json();
    if(!res.ok) throw new Error(data.message || (data.errors && Object.values(data.errors).flat().join('\n')) || 'Erreur dépôt');
    const idx = obligations.findIndex(x=>Number(x.id)===Number(id));
    if(idx>=0) obligations[idx] = data.obligation;
    if(data.document) documents.unshift(data.document);
    applySuiviKpis(data.kpis);
    syncMatrixFromObligations();
    closeModal('modalOblJustificatif');
    refreshSuiviUI();
  }catch(e){ alert(e.message); }
}
async function deleteObligation(id){
  if(!confirm('Supprimer cette obligation de suivi ?')) return;
  try{
    const data = await apiUsers(`/obligations/${id}`, 'DELETE');
    obligations = obligations.filter(o=>Number(o.id)!==Number(id));
    applySuiviKpis(data.kpis);
    syncMatrixFromObligations();
    refreshSuiviUI();
  }catch(e){ alert(e.message); }
}
function refreshSuiviUI(){
  renderObligationsList();
  renderDeclarations();
  renderObligationKpis();
  renderKPIs();
  renderTimeline();
  renderFeed();
  renderCalendar();
  refreshNotifications();
}

function renderDeclarations(){
  if(!document.getElementById('declTbody')){ renderObligationsList(); renderComplianceList(); return; }
  const q = ((document.getElementById('declSearch')||{}).value||'').toLowerCase();
  const org = ((document.getElementById('declFilterOrg')||{}).value||'');
  const conformite = ((document.getElementById('declFilterStatut')||{}).value||'');
  const year = new Date().getFullYear();

  const isConforme = (c)=>{
    const linked = obligations.filter(o=>Number(o.contribuable_id)===Number(c.id) && Number(o.annee)===year);
    if(!linked.length) return true;
    return linked.every(o=>o.statut==='declare' || o.statut==='justificatif_depose');
  };

  let rows = contribuables.filter(c=>{
    if(!(c.nom||'').toLowerCase().includes(q)) return false;
    if(org && (c.organisme||'') !== org) return false;
    if(conformite==='conforme' && !isConforme(c)) return false;
    if(conformite==='non_conforme' && isConforme(c)) return false;
    return true;
  });

  const orgOptions = ['DGI','CNPS','Autres'];
  document.getElementById('declThead').innerHTML = `<tr>
    <th class="sticky-col">Contribuable</th>
    <th>Organisme</th>
    <th>Vérification</th>
    ${trackedDocTypes.map(t=>`<th>${t.nom}</th>`).join('')}
  </tr>`;

  if(!rows.length){
    document.getElementById('declTbody').innerHTML = `<tr><td colspan="${3+trackedDocTypes.length}" class="empty">Aucun contribuable.</td></tr>`;
    renderComplianceList();
    return;
  }

  document.getElementById('declTbody').innerHTML = rows.map(c=>{
    const orgVal = c.organisme || '';
    const orgSelect = `<select class="inline-select" onchange="updateContribOrganisme(${c.id}, this.value)">
      <option value="" ${!orgVal?'selected':''}>—</option>
      ${orgOptions.map(o=>`<option value="${o}" ${orgVal===o?'selected':(orgVal && !orgOptions.includes(orgVal) && o==='Autres'?'selected':'')}>${o}</option>`).join('')}
    </select>`;
    const cellsHtml = trackedDocTypes.map(t=>{
      const statut = computeCellStatut(c.id, t.id);
      const ui = statut==='justificatif_depose' ? 'declare' : statut;
      return `<td>
        <select class="inline-select statut-select statut-${ui}" onchange="updateCellStatut(${c.id}, ${t.id}, this.value)">
          <option value="aucun" ${statut==='aucun'?'selected':''}>Aucun</option>
          <option value="non_declare" ${statut==='non_declare'||statut==='penalite'?'selected':''}>À déclarer</option>
          <option value="declare" ${statut==='declare'||statut==='justificatif_depose'?'selected':''}>Déclaré</option>
        </select>
      </td>`;
    }).join('');
    const linked = hasTrackedDocLinked(c.id);
    const verifUrl = c.lien_verification || '';
    const verifCell = linked ? `
      <div class="row-actions" style="justify-content:flex-start;">
        <button class="mini-btn ${verifUrl?'mini-btn-active':''}" title="Ouvrir vérification" onclick="openVerifLink(${c.id})"><svg><use href="#i-link"/></svg></button>
        <button class="mini-btn" title="Éditer le lien" onclick="editVerifLink(${c.id})"><svg><use href="#i-edit"/></svg></button>
      </div>` : `<span style="font-size:11.5px;color:var(--text-400);">—</span>`;
    return `<tr>
      <td class="cell-strong sticky-col">${c.nom}</td>
      <td>${orgSelect}</td>
      <td>${verifCell}</td>
      ${cellsHtml}
    </tr>`;
  }).join('');

  renderComplianceList();
}

function hasTrackedDocLinked(contribId){
  return obligations.some(o=>Number(o.contribuable_id)===Number(contribId))
    || trackedDocTypes.some(t=>computeCellStatut(contribId, t.id)!=='aucun');
}
function suggestedVerifUrl(contribId){
  const c = findContrib(contribId) || {};
  if(c.organisme==='DGI') return 'https://www.impots.cm';
  if(c.organisme==='CNPS') return 'https://www.cnps.cm';
  return 'https://';
}
async function editVerifLink(contribId){
  if(!hasTrackedDocLinked(contribId)){
    alert("Créez d'abord une obligation pour ce contribuable.");
    return;
  }
  const c = findContrib(contribId);
  const current = c.lien_verification || suggestedVerifUrl(contribId);
  const url = prompt(`Lien de vérification pour ${c.nom} (site DGI ou CNPS) :`, current);
  if(url === null) return;
  try{
    const data = await apiUsers(`/contribuables/${contribId}/lien-verification`, 'PATCH',
      { lien_verification: url.trim() || null });
    c.lien_verification = data.contribuable.lien_verification;
    renderDeclarations();
  }catch(e){ alert(e.message); }
}
function openVerifLink(contribId){
  const c = findContrib(contribId);
  const url = c && c.lien_verification;
  if(!url){ alert("Aucun lien de vérification enregistré."); return; }
  window.open(url, '_blank', 'noopener');
}
async function updateContribOrganisme(contribId, value){
  const c = findContrib(contribId);
  let finalValue = value;
  if(value==='Autres'){
    const custom = prompt("Précisez l'organisme :", (c.organisme && !['DGI','CNPS'].includes(c.organisme)) ? c.organisme : '');
    finalValue = (custom && custom.trim()) ? custom.trim() : 'Autres';
  }
  try{
    const data = await apiUsers(`/contribuables/${contribId}/organisme`, 'PATCH', { organisme: finalValue || null });
    c.organisme = data.contribuable.organisme;
    renderDeclarations();
  }catch(e){ alert(e.message); renderDeclarations(); }
}
async function updateCellStatut(contribId, docTypeId, value){
  try{
    const data = await apiUsers('/declaration-statuts', 'POST',
      { contribuable_id: contribId, tracked_doc_type_id: docTypeId, statut: value, annee: new Date().getFullYear(), periode: 'AUTRE' });
    if(data.obligation){
      const idx = obligations.findIndex(o=>Number(o.id)===Number(data.obligation.id));
      if(idx>=0) obligations[idx] = data.obligation; else if(value!=='aucun') obligations.push(data.obligation);
    }
    if(value==='aucun'){
      obligations = obligations.filter(o=>!(Number(o.contribuable_id)===Number(contribId) && Number(o.tracked_doc_type_id)===Number(docTypeId) && o.periode==='AUTRE' && Number(o.annee)===new Date().getFullYear()));
    }
    syncMatrixFromObligations();
    refreshSuiviUI();
  }catch(e){ alert(e.message); renderDeclarations(); }
}
async function addTrackedDocType(){ openTrackedTypeModal(); }
let editTrackedTypeId = null;
const periodiciteLabels = { libre:'Libre', mensuelle:'Mensuelle', trimestrielle:'Trimestrielle', annuelle:'Annuelle' };

function openTrackedTypeModal(id){
  editTrackedTypeId = id ? Number(id) : null;
  const title = document.getElementById('modalTrackedTypeTitle');
  if(title) title.textContent = editTrackedTypeId ? 'Modifier le type d\'obligation' : 'Ajouter un type d\'obligation';
  const t = editTrackedTypeId ? findTrackedDocType(editTrackedTypeId) : null;
  const idEl = document.getElementById('f-tracked-id');
  if(idEl) idEl.value = editTrackedTypeId || '';
  document.getElementById('f-tracked-nom').value = t ? t.nom : '';
  document.getElementById('f-tracked-periodicite').value = t ? (t.periodicite || 'libre') : 'trimestrielle';
  document.getElementById('f-tracked-organisme').value = t ? (t.organisme_defaut || '') : 'DGI';
  document.getElementById('f-tracked-deadline').value = t && t.dateLimite ? toISOInput(t.dateLimite) : '';
  openModal('modalTrackedType');
}
async function submitTrackedType(){
  const nom = (document.getElementById('f-tracked-nom').value || '').trim();
  if(!nom){ alert('Le libellé est obligatoire.'); return; }
  const payload = {
    nom,
    periodicite: document.getElementById('f-tracked-periodicite').value || 'libre',
    organisme_defaut: document.getElementById('f-tracked-organisme').value || null,
    date_limite: document.getElementById('f-tracked-deadline').value || null,
  };
  try{
    let data;
    if(editTrackedTypeId){
      data = await apiUsers(`/tracked-doc-types/${editTrackedTypeId}`, 'PUT', payload);
      const idx = trackedDocTypes.findIndex(t=>Number(t.id)===Number(editTrackedTypeId));
      const front = {
        id: data.trackedDocType.id,
        nom: data.trackedDocType.nom,
        dateLimite: data.trackedDocType.date_limite ? new Date(data.trackedDocType.date_limite + 'T00:00:00') : null,
        periodicite: data.trackedDocType.periodicite || 'libre',
        organisme_defaut: data.trackedDocType.organisme_defaut || null,
      };
      if(idx>=0) trackedDocTypes[idx] = front; else trackedDocTypes.push(front);
    } else {
      data = await apiUsers('/tracked-doc-types', 'POST', payload);
      trackedDocTypes.push({
        id: data.trackedDocType.id,
        nom: data.trackedDocType.nom,
        dateLimite: data.trackedDocType.date_limite ? new Date(data.trackedDocType.date_limite + 'T00:00:00') : null,
        periodicite: data.trackedDocType.periodicite || 'libre',
        organisme_defaut: data.trackedDocType.organisme_defaut || null,
      });
    }
    trackedDocTypes.sort((a,b)=> a.nom.localeCompare(b.nom, 'fr'));
    closeModal('modalTrackedType');
    editTrackedTypeId = null;
    renderTrackedDocList(); renderTrackedDeadlineList(); renderDeclarations(); renderObligationsList();
  }catch(e){ alert(e.message); }
}
async function removeTrackedDocType(id){
  const type = findTrackedDocType(id);
  if(!type) return;
  if(!confirm(`Retirer « ${type.nom} » des types d'obligations ?`)) return;
  try{
    await apiUsers(`/tracked-doc-types/${id}`, 'DELETE');
    trackedDocTypes = trackedDocTypes.filter(t=>Number(t.id)!==Number(id));
    obligations = obligations.filter(o=>Number(o.tracked_doc_type_id)!==Number(id));
    syncMatrixFromObligations();
    refreshSuiviUI();
    renderTrackedDocList(); renderTrackedDeadlineList();
  }catch(e){ alert(e.message); }
}
function renderTrackedDocList(){
  const el = document.getElementById('trackedDocList');
  if(!el) return;
  if(el.tagName === 'TBODY'){
    el.innerHTML = trackedDocTypes.length ? trackedDocTypes.map(t => `
      <tr>
        <td class="cell-strong">${t.nom}</td>
        <td>${periodiciteLabels[t.periodicite] || t.periodicite || 'Libre'}</td>
        <td>${t.organisme_defaut || '—'}</td>
        <td>${t.dateLimite ? fmtFR(t.dateLimite) : '—'}</td>
        <td>
          <div class="row-actions">
            <button class="mini-btn" title="Modifier" onclick="openTrackedTypeModal(${t.id})"><svg><use href="#i-edit"/></svg></button>
            <button class="mini-btn" title="Supprimer" onclick="removeTrackedDocType(${t.id})"><svg><use href="#i-x"/></svg></button>
          </div>
        </td>
      </tr>`).join('') : `<tr><td colspan="5" class="empty">Aucun type. Lancez le seeder Cameroun ou ajoutez un type.</td></tr>`;
    return;
  }
  el.innerHTML = trackedDocTypes.length ? trackedDocTypes.map(t => `
    <span class="tag" style="padding:7px 10px;">
      ${t.nom}
      <button class="mini-btn" style="width:18px;height:18px;margin-left:2px;" title="Modifier" onclick="openTrackedTypeModal(${t.id})"><svg style="width:11px;height:11px"><use href="#i-edit"/></svg></button>
      <button class="mini-btn" style="width:18px;height:18px;margin-left:2px;" title="Retirer" onclick="removeTrackedDocType(${t.id})"><svg style="width:11px;height:11px"><use href="#i-x"/></svg></button>
    </span>
  `).join('') : `<span style="font-size:12px;color:var(--text-400);">Aucun type d'obligation configuré.</span>`;
}
function renderTrackedDeadlineList(){
  const el = document.getElementById('trackedDeadlineList');
  if(!el) return;
  el.innerHTML = trackedDocTypes.length ? trackedDocTypes.map(t => `
    <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:var(--bg);border:1px solid var(--line);border-radius:9px;flex-wrap:wrap;">
      <span class="cell-strong" style="flex:1;">${t.nom}</span>
      <span style="font-size:11.5px;color:var(--text-400);">${periodiciteLabels[t.periodicite]||'Libre'} · ${t.organisme_defaut||'—'}</span>
      <input type="date" value="${toISOInput(t.dateLimite)}" onchange="setTrackedDeadline(${t.id}, this.value)">
      <button class="mini-btn" title="Modifier" onclick="openTrackedTypeModal(${t.id})"><svg><use href="#i-edit"/></svg></button>
    </div>
  `).join('') : `<span style="font-size:12px;color:var(--text-400);">Configurez les types depuis Documents (ou seed Cameroun).</span>`;
}
async function setTrackedDeadline(id, value){
  const type = findTrackedDocType(id);
  try{
    const data = await apiUsers(`/tracked-doc-types/${id}/deadline`, 'PATCH', { date_limite: value || null });
    type.dateLimite = data.trackedDocType.date_limite
      ? new Date(data.trackedDocType.date_limite + 'T00:00:00') : null;
    refreshSuiviUI();
  }catch(e){ alert(e.message); renderTrackedDeadlineList(); }
}
function autoLinkTrackedDoc(doc){
  if(doc.obligation_id){
    const o = findObligation(doc.obligation_id);
    if(o){ o.has_justificatif = true; o.documents_count = (o.documents_count||0)+1; }
  }
}
function renderComplianceList(){
  const el = document.getElementById('complianceList');
  if(!el) return;
  const byContrib = {};
  obligations.forEach(o=>{
    if(o.statut==='declare' || o.statut==='justificatif_depose') return;
    if(!byContrib[o.contribuable_id]) byContrib[o.contribuable_id] = { contrib: o.contribuable_nom, pending: [] };
    byContrib[o.contribuable_id].pending.push({
      nom: `${o.type_nom} ${o.periode} ${o.annee}`,
      statut: o.statut_effectif === 'penalite' ? 'penalite' : 'non_declare'
    });
  });
  const problems = Object.values(byContrib);
  el.innerHTML = problems.length ? problems.map(p=>`
    <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--red-bg);border:1px solid #f2c9c9;border-radius:9px;flex-wrap:wrap;">
      <svg style="width:15px;height:15px;color:var(--red);flex:none;"><use href="#i-alert"/></svg>
      <span class="cell-strong" style="color:var(--red);">${p.contrib}</span>
      <span style="font-size:12px;color:var(--text-600);">n'est pas en règle :</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        ${p.pending.map(e=>`<span class="badge ${badgeClass(e.statut)}">${e.nom} · ${statutLabel(e.statut)}</span>`).join('')}
      </div>
    </div>`).join('') : `<div style="font-size:12.5px;color:var(--green);display:flex;align-items:center;gap:8px;"><svg style="width:15px;height:15px;"><use href="#i-check"/></svg>Tous les contribuables suivis sont en règle.</div>`;
}

['oblSearch','oblFilterAnnee','oblFilterPeriode','oblFilterType','oblFilterOrg','oblFilterStatut','oblFilterEcheance','oblFilterPiece'].forEach(id=>{
  const el = document.getElementById(id);
  if(el) el.addEventListener(el.tagName==='INPUT'?'input':'change', renderObligationsList);
});
const declSearchInput = document.getElementById('declSearch');
if(declSearchInput) declSearchInput.addEventListener('input', renderDeclarations);
['declFilterOrg','declFilterStatut'].forEach(id=>{
  const el = document.getElementById(id);
  if(el) el.addEventListener('change', renderDeclarations);
});
function resetDeclarations(){
  const declSearch = document.getElementById('declSearch');
  const declFilterOrg = document.getElementById('declFilterOrg');
  const declFilterStatut = document.getElementById('declFilterStatut');
  if(declSearch) declSearch.value='';
  if(declFilterOrg) declFilterOrg.selectedIndex=0;
  if(declFilterStatut) declFilterStatut.selectedIndex=0;
  renderDeclarations();
}

/* ================= RENDER: USERS ================= */
async function apiUsers(url, method, body){
  const opts = {
    method,
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  };
  if(body !== undefined){
    opts.headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(body);
  }
  const res = await fetch(url, opts);
  const data = await res.json().catch(()=>({}));
  if(!res.ok){
    const msg = data.message
      || (data.errors && Object.values(data.errors).flat().join('\n'))
      || 'Une erreur est survenue.';
    throw new Error(msg);
  }
  return data;
}

function renderUsers(){
  if(!document.getElementById('userTbody')) return;
  const q=(document.getElementById('userSearch').value||'').toLowerCase();
  const rows = users.filter(u=>String(u.nom||'').toLowerCase().includes(q)||String(u.email||'').toLowerCase().includes(q));
  document.getElementById('userCount').textContent = rows.length+' compte(s) sur '+users.length;
  document.getElementById('userTbody').innerHTML = rows.length ? rows.map(u=>`
    <tr>
      <td><div style="display:flex;align-items:center;gap:10px;">
        <div class="avatar" style="width:30px;height:30px;font-size:11px;">${initials(u.nom)}</div>
        <span class="cell-strong">${u.nom}</span>
      </div></td>
      <td>${u.email}</td>
      <td>${u.role}</td>
      <td><span class="badge ${u.statut==='active'?'b-active':'b-inactive'}">${u.statut==='active'?'Actif':'Suspendu'}</span></td>
      <td><div class="row-actions">
        <button class="mini-btn" title="Modifier" onclick="editUser(${u.id})"><svg><use href="#i-edit"/></svg></button>
        <button class="mini-btn" title="${u.statut==='active'?'Suspendre':'Réactiver'}" onclick="toggleUserStatut(${u.id})"><svg><use href="#i-trash"/></svg></button>
      </div></td>
    </tr>`).join('') : `<tr><td colspan="5" class="empty">Aucun compte ne correspond à votre recherche.</td></tr>`;
}
async function toggleUserStatut(id){
  const u = users.find(x=>Number(x.id)===Number(id));
  if(!u) return;
  const verb = u.statut==='active' ? 'suspendre' : 'réactiver';
  if(!confirm(`Voulez-vous vraiment ${verb} le compte de ${u.nom} ?`)) return;
  try{
    const data = await apiUsers(`/users/${id}/toggle-status`, 'PATCH');
    const idx = users.findIndex(x=>Number(x.id)===Number(id));
    if(idx >= 0) users[idx] = data.user;
    renderUsers();
  }catch(e){ alert(e.message); }
}
const userSearchInput = document.getElementById('userSearch');
if(userSearchInput) userSearchInput.addEventListener('input', renderUsers);
function resetUsers(){
  const userSearch = document.getElementById('userSearch');
  if(userSearch) userSearch.value='';
  renderUsers();
}
async function reloadUsers(){
  try{
    const data = await apiUsers('/users', 'GET');
    users = data.users || [];
    renderUsers();
  }catch(e){ console.error(e); }
}

/* ================= MODALS ================= */

/* ---- Contribuables : ajout / modification / consultation ---- */
function fillContribForm(c){
  const fraisPaiement = c.fraisPaiement ?? c.frais_paiement ?? '';
  const fsPaye = c.fsPaye ?? c.fs_paye ?? '';
  const fsNonPaye = c.fsNonPaye ?? c.fs_non_paye ?? '';
  const aiIgs = c.aiIgs ?? c.ai_igs ?? '';
  const aiBail = c.aiBail ?? c.ai_bail ?? '';
  const aiPrecompte = c.aiPrecompte ?? c.ai_precompte ?? '';
  const qIgs = c.qIgs ?? c.q_igs ?? '';
  const qBail = c.qBail ?? c.q_bail ?? '';
  const qPrecompte = c.qPrecompte ?? c.q_precompte ?? '';
  const acfIgs = c.acfIgs ?? c.acf_igs ?? '';
  const acfBail = c.acfBail ?? c.acf_bail ?? '';
  const acfPrecompte = c.acfPrecompte ?? c.acf_precompte ?? '';

  document.getElementById('f-contrib-nom').value = c.nom||'';
  document.getElementById('f-contrib-niu').value = c.niu==='—'?'':(c.niu||'');
  document.getElementById('f-contrib-pass').value = c.pass==='—'?'':(c.pass||'');
  setSelectValueOrAutre('f-contrib-regime', c.regime||'Réel');
  setSelectValueOrAutre('f-contrib-cat', c.cat||'Petite entreprise');
  document.getElementById('f-contrib-lieu').value = c.lieu==='—'?'':(c.lieu||'');
  document.getElementById('f-contrib-tel').value = c.tel==='—'?'':(c.tel||'');
  document.getElementById('f-contrib-montant').value = c.montant||'';
  document.getElementById('f-contrib-impots').value = c.impots||'';
  document.getElementById('f-contrib-loyer').value = c.loyer||'';
  document.getElementById('f-contrib-bail').value = c.bail||'';
  document.getElementById('f-contrib-precompte').value = c.precompte||'';
  document.getElementById('f-contrib-timbre').value = c.timbre||'';
  document.getElementById('f-contrib-fraispaiement').value = fraisPaiement;
  document.getElementById('f-contrib-t1').value = c.t1||'';
  document.getElementById('f-contrib-t2').value = c.t2||'';
  document.getElementById('f-contrib-t3').value = c.t3||'';
  document.getElementById('f-contrib-t4').value = c.t4||'';
  document.getElementById('f-contrib-tdl').value = c.tdl||'';
  document.getElementById('f-contrib-fspaye').value = fsPaye;
  document.getElementById('f-contrib-fsnonpaye').value = fsNonPaye;
  document.getElementById('f-contrib-aiigs').value = aiIgs;
  document.getElementById('f-contrib-aibail').value = aiBail;
  document.getElementById('f-contrib-aiprecompte').value = aiPrecompte;
  document.getElementById('f-contrib-qigs').value = qIgs;
  document.getElementById('f-contrib-qbail').value = qBail;
  document.getElementById('f-contrib-qprecompte').value = qPrecompte;
  document.getElementById('f-contrib-acfigs').value = acfIgs;
  document.getElementById('f-contrib-acfbail').value = acfBail;
  document.getElementById('f-contrib-acfprecompte').value = acfPrecompte;
}
function clearContribForm(){
  document.querySelectorAll('#ov-modalContribuable input').forEach(inp=>inp.value='');
  document.getElementById('f-contrib-regime').selectedIndex=0;
  document.getElementById('f-contrib-cat').selectedIndex=0;
  document.getElementById('f-contrib-regime-autre').style.display='none';
  document.getElementById('f-contrib-cat-autre').style.display='none';
}
function setContribFormDisabled(disabled){
  document.querySelectorAll('#ov-modalContribuable input, #ov-modalContribuable select').forEach(el=>el.disabled=disabled);
  document.getElementById('modalContribuableSaveBtn').style.display = disabled ? 'none' : '';
  document.getElementById('modalContribuableCancelBtn').textContent = disabled ? 'Fermer' : 'Annuler';
}
function openAddContribuable(){
  editContribIndex = null;
  clearContribForm();
  setContribFormDisabled(false);
  document.getElementById('modalContribuableTitle').textContent = 'Ajouter un contribuable';
  document.getElementById('modalContribuableSaveBtn').textContent = 'Enregistrer';
  openModal('modalContribuable');
}
function editContribuable(i){
  editContribIndex = i;
  fillContribForm(contribuables[i]);
  setContribFormDisabled(false);
  document.getElementById('modalContribuableTitle').textContent = 'Modifier le contribuable';
  document.getElementById('modalContribuableSaveBtn').textContent = 'Enregistrer les modifications';
  openModal('modalContribuable');
}
function consultContribuable(i){
  editContribIndex = null;
  fillContribForm(contribuables[i]);
  setContribFormDisabled(true);
  document.getElementById('modalContribuableTitle').textContent = 'Fiche du contribuable';
  openModal('modalContribuable');
}

/* ---- Documents : ajout / modification ---- */
function openAddDocument(){
  editDocIndex = null;
  openModal('modalDocument');
  document.getElementById('f-doc-nom').value='';
  document.getElementById('f-doc-type').selectedIndex=0;
  document.getElementById('f-doc-type-autre').value=''; document.getElementById('f-doc-type-autre').style.display='none';
  onDocTypeChange();
  document.getElementById('f-doc-contrib').selectedIndex=0;
  document.getElementById('f-doc-fournisseur').value='';
  document.getElementById('f-doc-montant').value='';
  document.getElementById('f-doc-file').value='';
  document.getElementById('modalDocumentTitle').textContent = 'Ajouter un document';
  document.getElementById('modalDocumentSaveBtn').textContent = 'Enregistrer';
  document.getElementById('doc-file-hint').style.display = 'none';
}
function editDocument(i){
  editDocIndex = i;
  const doc = documents[i];
  openModal('modalDocument'); // repeuple la liste des contribuables
  document.getElementById('f-doc-nom').value = doc.nom;
  setSelectValueOrAutre('f-doc-type', doc.type);
  onDocTypeChange();
  document.getElementById('f-doc-contrib').value = doc.contribuable_id || '';
  document.getElementById('f-doc-fournisseur').value = doc.fournisseur==='—' ? '' : doc.fournisseur;
  document.getElementById('f-doc-montant').value = doc.montant || '';
  document.getElementById('f-doc-file').value = '';
  document.getElementById('modalDocumentTitle').textContent = 'Modifier le document';
  document.getElementById('modalDocumentSaveBtn').textContent = 'Enregistrer les modifications';
  document.getElementById('doc-file-hint').style.display = 'block';
}

/* ---- Comptes utilisateurs : ajout / modification ---- */
function openAddUser(){
  editUserId = null;
  document.getElementById('f-user-nom').value='';
  document.getElementById('f-user-prenom').value='';
  document.getElementById('f-user-email').value='';
  document.getElementById('f-user-role').selectedIndex=0;
  document.getElementById('f-user-role-autre').value=''; document.getElementById('f-user-role-autre').style.display='none';
  document.getElementById('f-user-statut').selectedIndex=0;
  document.getElementById('f-user-pass').value='';
  document.getElementById('f-user-pass').placeholder='••••••••';
  document.getElementById('f-user-pass').required = true;
  document.getElementById('modalUserTitle').textContent = 'Créer un compte utilisateur';
  document.getElementById('modalUserSaveBtn').textContent = 'Créer le compte';
  openModal('modalUser');
}
function editUser(id){
  const u = users.find(x=>Number(x.id)===Number(id));
  if(!u) return;
  editUserId = u.id;
  openModal('modalUser');
  const parts = String(u.nom||'').trim().split(/\s+/);
  document.getElementById('f-user-prenom').value = parts.shift() || '';
  document.getElementById('f-user-nom').value = parts.join(' ');
  document.getElementById('f-user-email').value = u.email;
  setSelectValueOrAutre('f-user-role', u.role);
  document.getElementById('f-user-statut').value = u.statut==='active' ? 'Actif' : 'Suspendu';
  document.getElementById('f-user-pass').value='';
  document.getElementById('f-user-pass').placeholder='Laisser vide pour ne pas changer';
  document.getElementById('f-user-pass').required = false;
  document.getElementById('modalUserTitle').textContent = 'Modifier le compte';
  document.getElementById('modalUserSaveBtn').textContent = 'Enregistrer les modifications';
}

function openModal(id){
  if(id==='modalDocument'){
    const sel = document.getElementById('f-doc-contrib');
    sel.innerHTML = contribuables.length
      ? contribuables.map(c=>`<option value="${c.id}">${c.nom}</option>`).join('')
      : `<option value="" disabled selected>— Aucun contribuable enregistré —</option>`;
  }
  if(id==='modalContribuable'){
    document.getElementById('f-igs-subtitle').textContent = `IGS ${dossier.annee || ''} — Trimestres & TDL`;
  }
  document.getElementById('ov-'+id).classList.add('open');
}
function closeModal(id){ document.getElementById('ov-'+id).classList.remove('open'); }

function fv(id){ const el=document.getElementById(id); return el ? el.value : ''; }
function fn(id){ return Number(fv(id))||0; }

/* ---- Gestion de l'option "Autres" dans les listes déroulantes des formulaires ---- */
function setupAutresOption(selectId){
  const sel = document.getElementById(selectId);
  const autreInput = document.getElementById(selectId+'-autre');
  if(!sel || !autreInput) return;
  sel.addEventListener('change', ()=>{
    autreInput.style.display = sel.value==='Autres' ? 'block' : 'none';
    if(sel.value==='Autres') autreInput.focus();
  });
}
function getSelectValue(selectId){
  const sel = document.getElementById(selectId);
  if(!sel) return '';
  if(sel.value === 'Autres'){
    const autreInput = document.getElementById(selectId+'-autre');
    return (autreInput && autreInput.value.trim()) || 'Autres';
  }
  return sel.value;
}
/* Pré-remplit un select + son champ "Autres" à partir d'une valeur enregistrée,
   même si cette valeur ne correspond à aucune option prédéfinie. */
function setSelectValueOrAutre(selectId, value){
  const sel = document.getElementById(selectId);
  const autreInput = document.getElementById(selectId+'-autre');
  if(!sel) return;
  const known = Array.from(sel.options).some(o=>o.value===value);
  if(known){
    sel.value = value;
    if(autreInput) autreInput.style.display='none';
  } else {
    sel.value = 'Autres';
    if(autreInput){ autreInput.value = value||''; autreInput.style.display='block'; }
  }
}
['f-contrib-regime','f-contrib-cat','f-user-role'].forEach(setupAutresOption);

/* ---- Le libellé du montant s'adapte au type de document choisi ---- */
function onDocTypeChange(){
  const sel = document.getElementById('f-doc-type');
  document.getElementById('f-doc-type-autre').style.display = sel.value==='Autres' ? 'block' : 'none';
  const type = getSelectValue('f-doc-type');
  document.getElementById('f-doc-montant-label').textContent = type ? `Montant — ${type} (FCFA)` : 'Montant (FCFA)';
}

function normalizeContrib(d){
  if(d && d.contribuable) d = d.contribuable;   // ✅ déballe si la réponse est enveloppée
  return {
    id: d.id, nom: d.nom, niu: d.niu, regime: d.regime, cat: d.cat, statut: d.statut || 'active', pass: d.pass,
    montant: d.montant, t1: d.t1, t2: d.t2, t3: d.t3, t4: d.t4, tdl: d.tdl,
    impots: d.impots, loyer: d.loyer, bail: d.bail, precompte: d.precompte, timbre: d.timbre,
    fraisPaiement: d.frais_paiement, fsPaye: d.fs_paye, fsNonPaye: d.fs_non_paye,
    aiIgs: d.ai_igs, aiBail: d.ai_bail, aiPrecompte: d.ai_precompte,
    qIgs: d.q_igs, qBail: d.q_bail, qPrecompte: d.q_precompte,
    acfIgs: d.acf_igs, acfBail: d.acf_bail, acfPrecompte: d.acf_precompte,
    lieu: d.lieu, tel: d.tel,
    organisme: d.organisme || null,
    lien_verification: d.lien_verification || null,
  };
}
async function submitContribuable(){
  const nom = fv('f-contrib-nom').trim();
  if(!nom){ alert('Le nom du contribuable est obligatoire.'); return; }
  const record = {
    nom, niu: fv('f-contrib-niu')||'—',
    regime: getSelectValue('f-contrib-regime'), cat: getSelectValue('f-contrib-cat'),
    statut: (editContribIndex!==null ? contribuables[editContribIndex].statut : 'active') || 'active',
    pass: fv('f-contrib-pass') || '—',
    montant: fn('f-contrib-montant'),
    t1: fn('f-contrib-t1'), t2: fn('f-contrib-t2'), t3: fn('f-contrib-t3'), t4: fn('f-contrib-t4'), tdl: fn('f-contrib-tdl'),
    impots: fn('f-contrib-impots'), loyer: fn('f-contrib-loyer'), bail: fn('f-contrib-bail'),
    precompte: fn('f-contrib-precompte'), timbre: fn('f-contrib-timbre'),
    frais_paiement: fn('f-contrib-fraispaiement'),
    fs_paye: fn('f-contrib-fspaye'), fs_non_paye: fn('f-contrib-fsnonpaye'),
    ai_igs: fv('f-contrib-aiigs')||null, ai_bail: fv('f-contrib-aibail')||null, ai_precompte: fv('f-contrib-aiprecompte')||null,
    q_igs: fv('f-contrib-qigs')||null, q_bail: fv('f-contrib-qbail')||null, q_precompte: fv('f-contrib-qprecompte')||null,
    acf_igs: fv('f-contrib-acfigs')||null, acf_bail: fv('f-contrib-acfbail')||null, acf_precompte: fv('f-contrib-acfprecompte')||null,
    lieu: fv('f-contrib-lieu')||'—', tel: fv('f-contrib-tel')||'—'
  };
  const isEdit = editContribIndex !== null;
  try{
    let saved;
    if(isEdit){
  saved = await apiUsers(`/contribuables/${contribuables[editContribIndex].id}`, 'PUT', record);
  contribuables[editContribIndex] = normalizeContrib(saved);   // ✅ au lieu de "= saved"
} else {
  saved = await apiUsers('/contribuables', 'POST', record);
  contribuables.unshift(normalizeContrib(saved));              // ✅ au lieu de "unshift(saved)"
}
    closeModal('modalContribuable');
    resetContribuables();
    renderKPIs();
    if(!isEdit){
      const firstRow = document.querySelector('#contribTbody tr');
      if(firstRow) firstRow.classList.add('row-new');
      document.querySelector('.dossier-scroll').scrollTo({top:0,left:0});
    }
    editContribIndex = null;
    clearContribForm();
  }catch(e){ alert(e.message); }
}
async function submitDocument(){
  const nom = document.getElementById('f-doc-nom').value.trim();
  if(!nom){ alert('Le nom du document est obligatoire.'); return; }
  const fileInput = document.getElementById('f-doc-file');
  const file = fileInput.files[0];
  const isEdit = editDocIndex !== null;
 
  // FormData est obligatoire pour transmettre un fichier (pas de JSON possible ici)
  const fd = new FormData();
  fd.append('nom', nom);
  fd.append('type', getSelectValue('f-doc-type'));
  fd.append('fournisseur', document.getElementById('f-doc-fournisseur').value || '');
  fd.append('montant', Number(document.getElementById('f-doc-montant').value) || 0);
  fd.append('contribuable_id', document.getElementById('f-doc-contrib').value || '');
  if(file) fd.append('fichier', file);
  if(isEdit) fd.append('_method', 'PUT'); // Laravel a besoin de ce champ avec FormData
 
  const url = isEdit ? `/documents/${documents[editDocIndex].id}` : '/documents';
 
  try{
    const res = await fetch(url, {
      method: 'POST',                       // toujours POST : _method gère le PUT
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: fd,                             // ⚠️ ne PAS définir Content-Type : le navigateur s'en charge
    });
    const data = await res.json().catch(()=>({}));
    if(!res.ok){
      throw new Error(data.message
        || (data.errors && Object.values(data.errors).flat().join('\n'))
        || 'Une erreur est survenue.');
    }
 
    if(isEdit){ documents[editDocIndex] = data.document; }
    else { documents.unshift(data.document); }
 
    autoLinkTrackedDoc(data.document);
    closeModal('modalDocument');
    resetDocuments();
    renderKPIs();
    renderDeclarations();
    renderTimeline();
    renderCalendar();
    refreshNotifications();
    if(!isEdit){
      const firstRow = document.querySelector('#docTbody tr');
      if(firstRow) firstRow.classList.add('row-new');
    }
    editDocIndex = null;
    document.getElementById('f-doc-nom').value='';
    document.getElementById('f-doc-fournisseur').value='';
    document.getElementById('f-doc-montant').value='';
    fileInput.value='';
    document.getElementById('doc-file-hint').style.display='none';
  }catch(e){ alert(e.message); }
}
async function submitUser(){
  const nom = document.getElementById('f-user-nom').value.trim();
  const prenom = document.getElementById('f-user-prenom').value.trim();
  const email = document.getElementById('f-user-email').value.trim();
  const password = document.getElementById('f-user-pass').value;
  if(!nom || !prenom || !email){ alert('Nom, prénom et email sont obligatoires.'); return; }
  if(editUserId === null && !password){ alert('Le mot de passe est obligatoire pour créer un compte.'); return; }

  const payload = {
    nom, prenom, email,
    role: getSelectValue('f-user-role'),
    statut: document.getElementById('f-user-statut').value==='Actif'?'active':'inactive',
    password: password || null,
  };

  try{
    let data;
    if(editUserId !== null){
      data = await apiUsers(`/users/${editUserId}`, 'PUT', payload);
      const idx = users.findIndex(x=>Number(x.id)===Number(editUserId));
      if(idx >= 0) users[idx] = data.user;
    } else {
      data = await apiUsers('/users', 'POST', payload);
      users.unshift(data.user);
    }
    closeModal('modalUser');
    resetUsers();
    editUserId = null;
    document.getElementById('f-user-nom').value='';
    document.getElementById('f-user-prenom').value='';
    document.getElementById('f-user-email').value='';
    document.getElementById('f-user-pass').value='';
  }catch(e){ alert(e.message); }
}



</script>

@stack('scripts')
</body>
</html>