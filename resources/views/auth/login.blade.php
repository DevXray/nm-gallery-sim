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
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrapper {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            margin: 20px;
        }
        /* LEFT SIDE - Form */
        .login-left {
            flex: 1;
            padding: 48px 40px;
            background: white;
        }
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #e0c06e, #C9A84C, #a07830);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Instrument Serif', serif;
            font-size: 28px;
            font-style: italic;
            color: #0a0a0a;
            margin: 0 auto 12px;
            box-shadow: 0 8px 20px rgba(201,168,76,0.3);
        }
        .logo-name {
            font-size: 22px;
            font-weight: 700;
            color: #0a0a0a;
            letter-spacing: -0.3px;
        }
        .logo-sub {
            font-size: 11px;
            color: #71717a;
            margin-top: 4px;
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }
        .subtitle {
            font-size: 12px;
            color: #a1a1aa;
            margin-bottom: 28px;
            text-align: center;
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
            margin-top: 8px;
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
        /* RIGHT SIDE - Image */
        .login-right {
            flex: 1;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-right img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        .company-info {
            position: absolute;
            bottom: 30px;
            text-align: center;
            color: white;
        }
        .company-name {
            font-size: 14px;
            font-weight: 600;
            color: #e0c06e;
        }
        .company-tagline {
            font-size: 10px;
            color: rgba(255,255,255,0.4);
            margin-top: 4px;
        }
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            .login-right {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- LEFT SIDE - Form Login -->
        <div class="login-left">
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
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>

        <!-- RIGHT SIDE - Foto Perusahaan -->
        <div class="login-right">
            <img src="{{ asset('images/company-photo.png') }}" alt="NM Gallery" onerror="this.src='https://placehold.co/500x600/1a1a1a/e0c06e?text=NM+Gallery'">
            <div class="company-info">
                <div class="company-name">NM Gallery</div>
                <div class="company-tagline">Baju Bodo Collection · Makassar</div>
            </div>
        </div>
    </div>
</body>
</html>