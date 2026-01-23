<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code</title>
    <style>
        body {
            font-family: 'Damas Font', sans-serif;
            background: linear-gradient(rgba(10, 20, 30, 0.5), rgba(10, 20, 30, 0.7)), url("{{ asset('images/connexionBackground.webp') }}") no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: block; 
            min-height: 100vh;
        }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px); 
            padding: 20px;
        }

        section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 350px;
            text-align: center; 
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #0f172a;
            font-size: 24px;
        }

        p.info-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        button {
            position: relative;
            width: 100%;
            padding: 16px;
            background-color: #0f172a;
            color: #fff;
            border: none;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            overflow: hidden;
            z-index: 1;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -15px;
            width: 0%;
            height: 100%;
            background-color: #0071e3;
            z-index: -1;
            transform: skewX(-20deg);
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        button:hover::before {
            width: 160%;
        }

        button:hover {
            box-shadow: 0 8px 25px rgba(0, 113, 227, 0.35);
            transform: translateY(-2px);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 15px;
            color: #333;
            box-sizing: border-box;
            transition: all 0.4s ease;
            text-align: center; 
            letter-spacing: 2px; 
            font-weight: bold;
        }

        input:focus {
            background-color: #fff;
            border-color: #0071e3;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
        }

        input.is-invalid {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1) !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: -5px;
            margin-bottom: 5px;
            font-weight: 600;
            text-align: left;
        }
    </style>
</head>

<body>
    
    @include('layouts.header')

    <div class="main-container">
        <section>

            <h2>Vérification</h2>
            <p class="info-text">Entrez le code reçu par email</p>

            @if (session('success'))
                <div style="background:#d4edda; color:#155724; padding:12px; border-radius:5px; margin-bottom:15px; border:1px solid #c3e6cb; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.2fa.verify') }}">
                @csrf
                @if(session()->has('reg_data.email'))
                    <input type="hidden" name="email" value="{{ session('reg_data.email') }}">
                @endif

                <input type="text" 
                       name="code" 
                       value="{{ old('code') }}"
                       class="@error('code') is-invalid @enderror" 
                       required 
                       placeholder="123456"
                       maxlength="6" 
                       autocomplete="one-time-code" />

                @error('code')
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror

                <button type="submit">Valider le code</button>
            </form>

            <div style="margin-top: 15px;">
                <a href="{{ route('login') }}" style="color: #666; font-size: 13px; text-decoration: none;">Retour à la connexion</a>
            </div>

        </section>
    </div>

</body>
</html>