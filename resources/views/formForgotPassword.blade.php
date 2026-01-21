<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
</head>
<body>
    @include('layouts.header')

    <div class="auth-wrapper">
        <section class="login-form">
            <h2>Récupération</h2>
            
            @if (session('success'))
                <div style="background-color: #dcfce7; color: #166534; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; border: 1px solid #bbf7d0;">
                    {{ session('success') }}
                </div>
            @endif

            <p style="font-size: 14px; color: #64748b; margin-bottom: 20px; text-align: center;">
                Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>

            <form action="{{ route('password.email') }}" method="post">
                @csrf
                <div class="form-group email">
                    <label for="f-email">Adresse E-mail</label>
                    <input class="form-control" name="email" type="email" id="f-email" value="{{ old('email') }}" required placeholder="Ex: jean.dupont@mail.com">
                    @error('email')
                        <div class="invalid-feedback" style="display:block">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary" id="submit-login" type="submit">
                    Envoyer le lien
                </button>
            </form>

            <div class="no-account">
                <a href="{{ route('login') }}">Retour à la <strong>Connexion</strong></a>
            </div>
        </section>
    </div>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>