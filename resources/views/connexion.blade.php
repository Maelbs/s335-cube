<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
</head>

<body>
    @include('layouts.header')

    <div class="auth-wrapper">
        
        <section class="login-form">
            <h2>Connexion</h2>

            <div class="invalid-feedback js-invalid-feedback-browser"></div>

            <form class="needs-validation" id="login-form" action="{{ route('login.submit') }}" method="post" novalidate autocomplete="off">
                @csrf

                <div class="form-group email">
                    <label for="f-email">Adresse E-mail</label>
                    <input class="form-control" name="email" type="email" id="f-email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ex: jean.dupont@mail.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group password">
                    <label for="f-password">Mot de passe</label>
                    <input class="form-control" name="password" id="f-password" type="password" required autocomplete="current-password" placeholder="••••••••">
                    
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="forgot-password">
                    <a href="https://www.cubebikes.fr/recuperation-mot-de-passe" rel="nofollow">Mot de passe oublié ?</a>
                </div>

                <footer class="form-footer">
                    <input type="hidden" name="submitLogin" value="1">
                    <button class="btn btn-primary" id="submit-login" type="submit">
                        Se connecter
                    </button>
                </footer>
            </form>

            <div class="no-account">
                <a href="{{ url('/inscription') }}">Pas encore de compte ? <strong>Inscrivez-vous</strong></a>
            </div>
        </section>
    </div>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>