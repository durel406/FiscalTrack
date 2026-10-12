
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FiscalTrack') }} — Connexion</title>
    <style>
        :root{
            --forest:#183b32;
            --forest-mid:#245746;
            --brand-blue:#1C7CF2;
            --brand-blue-dark:#0F5FD1;
            --text-dark:#202b25;
            --text-muted:#68766d;
            --border:#d9dfd9;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{
            font-family:'Segoe UI',Tahoma,sans-serif;
            color:var(--text-dark);
            background-color:#eef1ed;
            background-image:linear-gradient(135deg,rgba(36,87,70,.035) 25%,transparent 25%),linear-gradient(315deg,rgba(36,87,70,.025) 25%,transparent 25%);
            background-size:24px 24px;
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:30px 16px;
        }
        .auth-card{
            display:block;
            max-width:440px;width:100%;
            box-shadow:0 16px 38px rgba(24,59,50,.13);
            border:1px solid rgba(24,59,50,.12);
            border-radius:5px;overflow:hidden;
            background:#fff;
        }

        /* ===== Left panel ===== */
        .auth-left{
            background:var(--brand-blue);
            color:#fff;
            padding:24px 32px;
            display:flex;flex-direction:column;align-items:flex-start;justify-content:center;text-align:left;
            gap:8px;
        }
        .auth-left img{width:112px;height:auto;margin-bottom:8px;padding:4px 7px;background:#fff;border-radius:3px;}
        .auth-left h2{font-family:Georgia,'Times New Roman',serif;font-size:23px;font-weight:600;margin-top:2px;}
        .auth-left p{font-size:13px;line-height:1.55;color:#d4e0d7;max-width:340px;margin:0;}

        /* ===== Right panel ===== */
        .auth-right{padding:24px 0 30px;}
        .auth-header{
            background:#fff;color:var(--brand-blue-dark);text-align:left;
            font-family:Georgia,'Times New Roman',serif;font-weight:700;letter-spacing:0;font-size:21px;
            padding:0 32px 14px;
            border-bottom:1px solid var(--border);
        }
        .auth-form{padding:22px 32px 0;}
        .field{margin-bottom:16px;}
        .field label{
            display:flex;align-items:center;gap:8px;
            font-weight:600;font-size:13px;color:var(--text-dark);margin-bottom:6px;
        }
        .field input, .field select{
            width:100%;padding:11px 12px;border:1px solid var(--border);border-radius:4px;
            font-family:inherit;font-size:14px;color:var(--text-dark);background:#fff;
        }
        .field input::placeholder{color:#9CA8B8;}
        .field input:focus, .field select:focus{outline:2px solid rgba(28,124,242,.18);outline-offset:1px;border-color:var(--brand-blue);}
        .password-wrap{position:relative;}
        .password-wrap .toggle-eye{
            position:absolute;right:12px;top:50%;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;color:var(--brand-blue);padding:2px;
        }
        .error-text{color:#D0333A;font-size:12px;margin-top:5px;}
        .status-text{color:#1B7F4D;font-size:13px;margin:0 0 14px;}

        .btn-submit{
            width:100%;padding:12px;margin-top:8px;
            background:var(--brand-blue);color:#fff;border:1px solid var(--brand-blue);border-radius:4px;
            font-family:inherit;font-weight:600;font-size:15px;cursor:pointer;
            transition:background .15s ease;
           

        }
        .btn-submit:hover{background:var(--brand-blue-dark);}

        a{
    text-decoration: none;
    text-decoration-color: none;
}
        @media (max-width:720px){
            .auth-card{max-width:400px;}
            .auth-left{padding:22px 24px;}
            .auth-header,.auth-form{padding-left:24px;padding-right:24px;}
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

                <form method="POST" action="{{ route('login') }}" novalidate>
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

</html>