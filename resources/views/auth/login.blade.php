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
            position: relative;
            overflow: hidden;
        }
        /* Decorative background elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(201,168,76,0.06) 0%, transparent 60%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(0deg, rgba(201,168,76,0.03) 0%, transparent 100%);
            pointer-events: none;
        }
        .login-wrapper {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: rgba(255,255,255,0.98);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            margin: 20px;
            position: relative;
            z-index: 10;
        }
        /* LEFT SIDE - Form */
        .login-left {
            flex: 1;
            padding: 52px 44px;
            background: white;
        }
        .title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            text-align: center;
            color: #0a0a0a;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 14px;
            color: #a1a1aa;
            margin-bottom: 32px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 20px;
        }
        .form-group {
            margin-bottom: 24px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #52525b;
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #a1a1aa;
        }
        input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 1.5px solid #e4e4e7;
            border-radius: 14px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.25s;
            background: #fafafa;
        }
        input:focus {
            outline: none;
            border-color: #C9A84C;
            background: white;
            box-shadow: 0 0 0 4px rgba(201,168,76,0.1);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #0a0a0a;
            color: #e0c06e;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            margin-top: 12px;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .btn-login:hover::before {
            left: 100%;
        }
        .btn-login:hover {
            background: #1a1a1a;
            box-shadow: 0 5px 20px rgba(201,168,76,0.25);
            transform: translateY(-2px);
        }
        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            margin-bottom: 24px;
            border-left: 4px solid #dc2626;
        }
        /* RIGHT SIDE - Image (polos) */
        .login-right {
            flex: 1;
            background: #0d0d0d;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-right img {
            max-width: 100%;
            max-height: 80%;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .company-info {
            position: absolute;
            bottom: 30px;
            text-align: center;
            color: white;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 40px;
            left: 40px;
            right: 40px;
        }
        .company-name {
            font-size: 14px;
            font-weight: 700;
            color: #e0c06e;
            letter-spacing: 1px;
        }
        .company-tagline {
            font-size: 10px;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
        }
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                margin: 16px;
                border-radius: 24px;
            }
            .login-right {
                display: none;
            }
            .login-left {
                padding: 36px 28px;
            }
            .title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- LEFT SIDE - Form Login -->
        <div class="login-left">
            <div class="title">Welcome Back</div>
            <div class="subtitle">Login to access your account</div>

            @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" name="username" placeholder="Enter your username" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>

        <!-- RIGHT SIDE - Foto Perusahaan -->
<div class="login-right">
    @php
        $profil = App\Models\ProfilToko::first();
    @endphp
    @if($profil && $profil->logo && file_exists(public_path($profil->logo)))
        <img src="{{ asset($profil->logo) }}" alt="{{ $profil->nama_toko }}" style="max-width: 100%; max-height: 80%; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
    @else
        <img src="{{ asset('images/company-photo.png') }}" alt="NM Gallery" onerror="this.src='https://placehold.co/500x600/1a1a1a/e0c06e?text=NM+Gallery'">
    @endif
    <div class="company-info">
        <div class="company-name">{{ $profil->nama_toko ?? 'NM Gallery' }}</div>
        <div class="company-tagline">Baju Bodo Collection · Makassar</div>
    </div>
</div>
</body>
</html>