
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FiscalTrack') }} — Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --blue:#1C7CF2;
            --blue-dark:#0F5FD1;
            --text-dark:#1F2430;
            --text-muted:#6B7280;
            --border:#BFDBFE;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{
            font-family:'Poppins', sans-serif;
            background:#F3F6FB;
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:30px 16px;
        }
        .auth-card{
            display:grid;grid-template-columns:1fr 1fr;
            max-width:920px;width:100%;
            box-shadow:0 20px 50px rgba(15,95,209,.12);
            border-radius:18px;overflow:hidden;
            background:#fff;
        }

        /* ===== Left panel ===== */
        .auth-left{
            background:var(--blue);
            color:#fff;
            padding:48px 40px;
            display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;
            gap:18px;
        }
        .auth-left img{width:150px;height:auto;}
        .auth-left h2{font-size:26px;font-weight:700;margin-top:6px;}
        .auth-left p{font-size:14px;line-height:1.6;color:#EAF3FF;max-width:280px;}

        /* ===== Right panel ===== */
        .auth-right{padding:0 0 36px;}
        .auth-header{
            background:var(--blue);color:#fff;text-align:center;
            font-weight:700;letter-spacing:.04em;font-size:18px;
            padding:22px 20px;
            border-radius:0 0 22px 0;
        }
        .auth-form{padding:26px 40px 0;}
        .field{margin-bottom:16px;}
        .field label{
            display:flex;align-items:center;gap:8px;
            font-weight:600;font-size:14px;color:var(--blue-dark);margin-bottom:6px;
        }
        .field input, .field select{
            width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;
            font-family:inherit;font-size:14px;color:var(--text-dark);background:#fff;
        }
        .field input::placeholder{color:#9CA8B8;}
        .field input:focus, .field select:focus{outline:none;border-color:var(--blue);}
        .password-wrap{position:relative;}
        .password-wrap .toggle-eye{
            position:absolute;right:12px;top:50%;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;color:var(--blue);padding:2px;
        }
        .error-text{color:#D0333A;font-size:12px;margin-top:5px;}
        .status-text{color:#1B7F4D;font-size:13px;margin:0 0 14px;}

        .btn-submit{
            width:100%;padding:12px;margin-top:8px;
            background:var(--blue);color:#fff;border:none;border-radius:10px;
            font-family:inherit;font-weight:600;font-size:15px;cursor:pointer;
            transition:background .15s ease;
           

        }
        .btn-submit:hover{background:var(--blue-dark);}

        a{
    text-decoration: none;
    text-decoration-color: none;
}
        @media (max-width:720px){
            .auth-card{grid-template-columns:1fr;}
            .auth-left{padding:36px 30px;}
        }
        
    </style>
</head>
<body>

    <div class="auth-card">

        <div class="auth-left">
            <img src="images/fiscaltrack-logo.png" alt="FiscalTrack">
            <h2>Bienvenue!</h2>
            <p>Authentifiez vous pour accéder aux fonctionnalités.</p>
        </div>

        <div class="auth-right">
            <div class="auth-header">CONNEXION</div>

            <div class="auth-form">

                @if (session('status'))
                    <p class="status-text">{{ session('status') }}</p>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field">
                        <label for="email">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/></svg>
                            Email
                        </label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            Mot de passe
                        </label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                            <button type="button" class="toggle-eye" onclick="toggleVisibility('password')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    <center>
                        <button type="submit" class="btn-submit">Se connecter</button>
                    </center>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleVisibility(inputId){
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>