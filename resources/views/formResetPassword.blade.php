<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
</head>
<body>
    @include('layouts.header')

    <div class="auth-wrapper">
        <section class="login-form">
            <h2>Nouveau mot de passe</h2>

            <form action="{{ route('password.update') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group email">
                    <label for="f-email">Adresse E-mail</label>
                    <input class="form-control" name="email" type="email" id="f-email" value="{{ $email ?? old('email') }}" required readonly style="background-color: #e2e8f0; cursor: not-allowed;">
                    @error('email')
                        <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group password">
                    <label for="f-password">Nouveau mot de passe</label>
                    <input class="form-control" name="password" id="f-password" type="password" required placeholder="••••••••">
                    @error('password')
                        <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group password">
                    <label for="f-password-confirm">Confirmer le mot de passe</label>
                    <input class="form-control" name="password_confirmation" id="f-password-confirm" type="password" required placeholder="••••••••">
                </div>

                <button class="btn btn-primary" id="submit-login" type="submit">
                    Modifier le mot de passe
                </button>
            </form>
        </section>
    </div>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>