<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FiscalTrack') }} — Bienvenue</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --blue:#1C7CF2;
            --blue-dark:#0F5FD1;
            --indigo:#3B2FD1;
            --gray-hero:#D9D9D9;
            --gray-box:rgba(70,70,70,.55);
            --text-dark:#1F2430;
            --text-body:#3B3F46;
            --card-shadow: 0 12px 28px rgba(20,30,60,.10);
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{
            font-family:'Poppins', sans-serif;
            color:var(--text-dark);
            background:#fff;
        }

        /* ===== Navbar ===== */
        .navbar{
            display:flex;align-items:center;justify-content:space-between;
            padding:14px 40px;background:#fff;
            box-shadow:0 1px 0 rgba(0,0,0,.06);
            position:relative;z-index:5;
        }
        .brand{display:flex;align-items:center;}
        .brand-logo{height:34px;width:auto;display:block;}

        .navbar-title{
            font-size:22px;font-weight:700;color:var(--blue);
            position:absolute;left:50%;transform:translateX(-50%);
        }

        .btn-connexion{
            display:inline-flex;align-items:center;gap:8px;
            background:var(--indigo);color:#fff;text-decoration:none;
            font-weight:600;font-size:14px;
            padding:10px 22px;border-radius:999px;
            transition:background .15s ease, transform .1s ease;
        }
        .btn-connexion:hover{background:#2f24ad;}
        .btn-connexion:active{transform:scale(.97);}

        /* ===== Hero ===== */
        .hero{
            background:var(--gray-hero);
            padding:48px 60px 60px;
            display:grid;grid-template-columns:1.1fr .9fr;
            gap:40px;align-items:center;
            position:relative;overflow:hidden;
        }
        .hero-box{
            background:var(--gray-box);
            backdrop-filter:blur(2px);
            border-radius:14px;
            padding:24px 28px;
            max-width:440px;
        }
        .hero-box h1{
            color:#fff;font-size:26px;line-height:1.3;font-weight:700;
        }
        .hero-text{
            margin-top:20px;max-width:460px;
            color:var(--text-body);font-size:15px;line-height:1.65;
        }

        .hero-emblem{display:flex;justify-content:center;align-items:center;position:relative;}
        .emblem-circle{
            width:320px;height:320px;border-radius:50%;
            background:radial-gradient(circle at 35% 30%, #BFD9F7, #8FBBEC 55%, #6FA6E6 100%);
            display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
            box-shadow:inset 0 0 40px rgba(255,255,255,.35);
        }
        .emblem-logo{width:220px;height:auto;display:block;}

        /* ===== Feature cards ===== */
        .features{
            padding:56px 60px 46px;
            display:grid;grid-template-columns:repeat(3, 1fr);gap:26px;
            background:#fff;
            margin-top:-18px;
        }
        .card{
            position:relative;background:#fff;border-radius:16px;
            box-shadow:var(--card-shadow);
            padding:26px 24px 26px 34px;overflow:hidden;
        }
        .card::before{
            content:"";position:absolute;left:0;top:10px;bottom:10px;width:10px;
            background:var(--blue);border-radius:0 8px 8px 0;
        }
        .card-icon{
            width:46px;height:46px;border-radius:12px;
            background:rgba(28,124,242,.10);
            display:flex;align-items:center;justify-content:center;
            color:var(--blue);margin-bottom:14px;
        }
        .card h3{color:var(--blue);font-size:17px;font-weight:700;margin-bottom:8px;}
        .card p{color:var(--text-body);font-size:14px;line-height:1.55;}

        /* ===== Bottom bar ===== */
        .bottom-bar{
            background:var(--blue);color:#fff;
            padding:20px 40px;
            display:flex;justify-content:center;gap:70px;flex-wrap:wrap;
        }
        .bottom-item{display:flex;align-items:center;gap:10px;font-weight:600;font-size:15px;}

        /* ===== Responsive ===== */
        @media (max-width:900px){
            .navbar{flex-direction:column;gap:10px;padding:16px 20px;}
            .navbar-title{position:static;transform:none;}
            .hero{grid-template-columns:1fr;padding:36px 24px;}
            .hero-emblem{margin-top:20px;}
            .features{grid-template-columns:1fr;padding:36px 24px;}
            .bottom-bar{gap:28px;padding:20px 24px;}
        }
    </style>
</head>
<body>

    <!-- ===== Navbar ===== -->
    <header class="navbar">
        <div class="brand">
            <img src="{{ asset('images/fiscaltrack-logo.png') }}" alt="FiscalTrack" class="brand-logo">
        </div>

        <div class="navbar-title">Bienvenue sur FiscalTrack</div>

        <a href="{{ route('login') }}" class="btn-connexion">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
            connexion
        </a>
    </header>

    <!-- ===== Hero ===== -->
    <section class="hero">
        <div>
            <div class="hero-box">
                <h1>Simplifiez le suivi de toutes vos déclarations sur FiscalTrack</h1>
            </div>
            <p class="hero-text">
                FiscalTrack est une application web conçue pour aider les cabinets comptables et
                les entreprises à gérer toutes leurs déclarations, les documents et les échéances.
            </p>
        </div>

        <div class="hero-emblem">
            <div class="emblem-circle">
                <img src="{{ asset('images/fiscaltrack-logo.png') }}" alt="FiscalTrack" class="emblem-logo">
            </div>
        </div>
    </section>

    <!-- ===== Feature cards ===== -->
    <section class="features">
        <div class="card">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/></svg>
            </div>
            <h3>Gestion de documents</h3>
            <p>Stockez, organisez et retrouvez facilement tous vos documents.</p>
        </div>

        <div class="card">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
            </div>
            <h3>Suivi des échéances</h3>
            <p>Ne manquez plus aucune date limite grâce aux rappels et notifications.</p>
        </div>

        <div class="card">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>Gestion des contribuables</h3>
            <p>Centralisez les informations de vos clients et suivez leurs obligations fiscales.</p>
        </div>
    </section>

    <!-- ===== Bottom bar ===== -->
    <footer class="bottom-bar">
        <div class="bottom-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
            Sécurité
        </div>
        <div class="bottom-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Notifications
        </div>
        <div class="bottom-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 21h16"/></svg>
            Centralisation
        </div>
    </footer>

</body>
</html>