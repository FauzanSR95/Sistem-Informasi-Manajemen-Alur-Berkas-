<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Autentikasi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&family=montserrat:700,800&family=poppins:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('logo_bpn_welcome.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-main: #F5F0E6;
            --bg-cards: #FFFCF5;
            --accent-brown: #5C4033;
            --accent-gold: #D4AF37;
            --text-dark: #212529;
        }
        .auth-body {
            background: var(--bg-main);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
        }
        .auth-container {
            background-color: var(--bg-cards);
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 960px;
            max-width: 100%;
            min-height: 600px;
        }
        .form-container {
            position: absolute; top: 0; height: 100%;
            transition: all 0.6s ease-in-out;
        }
        .form-container-inner {
            background-color: var(--bg-cards);
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 50px; height: 100%; text-align: center;
        }
        .auth-input {
            background-color: var(--bg-main); border: none;
            padding: 12px 15px; margin: 8px 0; width: 100%; border-radius: 8px;
        }
        .auth-input:focus {
            outline: none; box-shadow: 0 0 0 2px var(--accent-gold);
        }
        .auth-button {
            border-radius: 20px; border: 1px solid var(--accent-gold);
            background-color: var(--accent-gold); color: var(--text-dark);
            font-size: 0.875rem; /* 14px */ font-weight: bold; padding: 0.75rem 2.5rem; /* 12px 40px */
            letter-spacing: 1px; text-transform: uppercase;
            transition: transform 80ms ease-in; cursor: pointer;
        }
        .auth-button:active { transform: scale(0.95); }
        .sign-in-container { left: 0; width: 50%; z-index: 2; }
        .sign-up-container { left: 0; width: 50%; opacity: 0; z-index: 1; }
        .auth-container.right-panel-active .sign-in-container { transform: translateX(100%); }
        .auth-container.right-panel-active .sign-up-container {
            transform: translateX(100%); opacity: 1; z-index: 5; animation: show 0.6s;
        }
        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }
        .overlay-container {
            position: absolute; top: 0; left: 50%; width: 50%; height: 100%;
            overflow: hidden; transition: transform 0.6s ease-in-out; z-index: 100;
        }
        .auth-container.right-panel-active .overlay-container{ transform: translateX(-100%); }
        .overlay {
            background: var(--accent-brown); color: #FFFFFF; position: relative;
            left: -100%; height: 100%; width: 200%; transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .auth-container.right-panel-active .overlay { transform: translateX(50%); }
        .overlay-panel {
            position: absolute; display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 40px; text-align: center;
            top: 0; height: 100%; width: 50%; transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .overlay-panel h1 { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 2.25rem; }
        .overlay-panel p { font-size: 1rem; line-height: 1.75rem; margin-top: 20px; margin-bottom: 30px; }
        .overlay-left { transform: translateX(-20%); }
        .auth-container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .auth-container.right-panel-active .overlay-right { transform: translateX(20%); }
        .ghost-button {
            background-color: transparent; border: 2px solid #FFFFFF;
            border-radius: 20px; color: #FFFFFF; font-size: 0.875rem; /* 14px */ font-weight: bold;
            padding: 0.75rem 2.5rem; /* 12px 40px */ letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.2s ease-in-out;
        }
        .ghost-button:hover { background-color: #FFFFFF; color: var(--accent-brown); }
    </style>
</head>
<body class="font-sans text-stone-900 antialiased">
    <div class="auth-body">
        <div class="auth-container" id="container">
            <div class="form-container sign-up-container">
                <div class="form-container-inner">
                    <img src="{{ asset('logo_bpn_welcome.png') }}" class="h-24 mx-auto" alt="Logo BPN">
                    <h1 class="font-bold text-2xl font-montserrat mt-4">Info Pembuatan Akun</h1>
                    <p class="mt-2 text-sm text-stone-600">
                        Pembuatan akun pengguna baru hanya dapat dilakukan oleh Administrator Sistem.
                    </p>
                    <p class="mt-4 text-sm text-stone-600">
                        Silakan hubungi Admin untuk mendapatkan akun Anda.
                    </p>
                </div>
            </div>
            <div class="form-container sign-in-container">
                <div class="form-container-inner">
                    {{ $slot }}
                </div>
            </div>
            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1 class="text-2xl">Selamat Datang!</h1>
                        <p class="mt-4">Silakan masuk dengan akun yang telah diberikan oleh Administrator.</p>
                        <button class="ghost-button mt-6" id="signIn">Log In</button>
                    </div>
                    <div class="overlay-panel overlay-right">
                         <a href="/">
                            <img src="{{ asset('logo_bpn_welcome.png') }}" class="h-24 mx-auto" alt="Logo BPN">
                        </a>
                        <h1 class="text-2xl mt-4">SIM A Berkas Internal</h1>
                        <p class="mt-4">Klik di sini untuk melihat informasi mengenai pembuatan akun.</p>
                        <button class="ghost-button mt-6" id="signUp">Info Akun</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');
        signUpButton.addEventListener('click', () => container.classList.add("right-panel-active"));
        signInButton.addEventListener('click', () => container.classList.remove("right-panel-active"));
    </script>
</body>
</html>