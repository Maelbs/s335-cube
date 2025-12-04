<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion</title>
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>
    @include('layouts.header')

    <h2>Connexion / Inscription</h2>

    <section class="login-form">
        <div class="invalid-feedback js-invalid-feedback-browser"></div>

        <form class="needs-validation cube-validate-form" id="login-form" action="{{ route('login.submit') }}" method="post" novalidate autocomplete="off">
            @csrf

            <section class="form-fields">

                <div class="form-group email">
                    <label class="required" for="f-email_60320">E-mail</label>
                    <input class="form-control" name="email" type="email" id="f-email_60320" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group password">
                    <label class="required" for="f-password_56397">Mot de passe</label>

                    <div class="input-group js-parent-focus">
                        <input class="form-control js-child-focus js-visible-password" name="password" id="f-password_56397" type="password" pattern=".{5,}" required autocomplete="current-password">

                    </div>
                    <small class="form-text text-muted">Au moins 5 caractères</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-group_forgot-password">
                    <div class="forgot-password">
                        <a href="https://www.cubebikes.fr/recuperation-mot-de-passe" rel="nofollow" class="small">Mot de passe oublié ?</a>
                    </div>
                </div>

            </section>

            <footer class="form-footer">
                <input type="hidden" name="submitLogin" value="1">
                <button class="btn btn-primary btn-primary--red form-control-submit" id="submit-login" type="submit" data-link-action="sign-in">
                    <span class="double-arrows double-arrows--white">Connexion</span>
                </button>
            </footer>
        </form>

        <div class="no-account">
            <a href="{{ url('/inscription') }}" data-link-action="display-register-form">Pas de compte ? Créez-en un</a>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>

</html>
