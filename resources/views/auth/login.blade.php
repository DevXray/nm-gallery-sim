<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NM Gallery SIM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border-top: 3px solid #C9A84C;
        }
        .logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #e0c06e, #C9A84C, #a07830);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Instrument Serif', serif;
            font-size: 26px;
            font-style: italic;
            color: #0a0a0a;
            margin: 0 auto 12px;
        }
        .logo-name {
            font-size: 20px;
            font-weight: 700;
            color: #0a0a0a;
        }
        .logo-sub {
            font-size: 11px;
            color: #71717a;
            margin-top: 4px;
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 12px;
            color: #71717a;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #52525b;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e4e4e7;
            border-radius: 10px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #C9A84C;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #0a0a0a;
            color: #e0c06e;
            border: 1px solid rgba(201,168,76,0.3);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: #1a1a1a;
            box-shadow: 0 2px 12px rgba(201,168,76,0.18);
        }
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .info {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #a1a1aa;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <div class="logo-icon">N</div>
                <div class="logo-name">NM Gallery</div>
                <div class="logo-sub">SIM Baju Bodo</div>
            </div>
            <div class="title">Selamat Datang</div>
            <div class="subtitle">Login untuk mengakses sistem</div>

            @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            <div class="info">
                Demo: username <strong>owner</strong> / <strong>karyawan1</strong><br>
                password: <strong>owner123</strong> / <strong>karyawan123</strong>
            </div>
        </div>
    </div>
</body>
</html>