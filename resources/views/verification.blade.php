<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <style>
        @font-face {
            font-family: 'Damas Font';
            src: url('../font/font.woff2');
            font-display: swap;
        }

        body {
            font-family: 'Damas Font', sans-serif;
            background: linear-gradient(rgba(10, 20, 30, 0.5), rgba(10, 20, 30, 0.7)), url('../images/connexionBackground.jpg') no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 320px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        button {
            position: relative;
            width: 100%;
            padding: 16px;
            background-color: #0f172a;
            color: #fff;
            border: none;
            font-family: 'Damas Font', sans-serif;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            overflow: hidden;
            z-index: 1;
            transition: all 0.3s ease;
            margin-top: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            clip-path: polygon(15px 0, 100% 0, 100% 100%, 0% 100%);
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
            font-family: 'Damas Font', sans-serif;
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
            margin-top: -8px;
            margin-bottom: 10px;
            font-weight: 600;
            text-align: left;
        }
    </style>
</head>

<body>
    @include('layouts.header')
    <section>

        @if (session('success'))
            <div style="
                                    background:#d4edda;
                                    color:#155724;
                                    padding:12px;
                                    border-radius:5px;
                                    margin-bottom:15px;
                                    border:1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.check') }}">
            @csrf

            <input type="hidden" name="email" value="{{ session('reg_data.email') }}">

            <input type="text" name="verification_code" value="{{ old('verification_code') }}"
                class="@error('verification_code') is-invalid @enderror" required placeholder="Code de vérification"
                autocomplete="off" />

            @error('verification_code')
                <div class="error-message">
                    {{ $message }}
                </div>
            @enderror

            <button type="submit">Vérifier</button>
        </form>

    </section>

</body>

</html>