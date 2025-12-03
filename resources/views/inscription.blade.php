<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inscription</title>
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>
    @include('layouts.header')
    <section id="wrapper" class="container" style="margin-top: 10rem">
        <div class="row">
            <div id="content-wrapper" class="col-12 px-0">
                <section id="main" class="page-wrapper page-wrapper--authentication">
                    <section id="content" class="page-content page-content--authentication">
                        <section class="register-form text-center">
                            <h1 class="h3 register-form__title mt-5">Créez votre compte</h1>
                            <p class="text-left mb-5">
                                Vous avez déjà un compte ?<br>
                                <a class="text-underline" href="{{ route('login') }}">Connectez-vous !</a>
                            </p>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="js-customer-form needs-validation card card-account text-left cube-validate-form customer-form" 
                                id="customer-form" 
                                action="{{ route('sendVerificationCode') }}" 
                                method="POST" 
                                novalidate 
                                autocomplete="off">
                                @csrf

                                <h2 class="h3 font-weight-bold font-normal mt-0 mt-lg-5 mb-4">MES INFORMATIONS PERSONNELLES</h2>

                                <section class="form-fields">

                                    {{-- Nom --}}
                                    <div class="form-group lastname">
                                        <label class="required" for="lastname">Nom</label>
                                        <input class="form-control" name="lastname" type="text" value="{{ old('lastname') }}" id="lastname" required autocomplete="family-name">
                                        <small class="form-text text-muted">Seules les lettres et le point (.), suivi d'un espace, sont autorisés.</small>
                                    </div>

                                    {{-- Prénom --}}
                                    <div class="form-group firstname">
                                        <label class="required" for="firstname">Prénom</label>
                                        <input class="form-control" name="firstname" type="text" value="{{ old('firstname') }}" id="firstname" required autocomplete="given-name">
                                        <small class="form-text text-muted">Seules les lettres et le point (.), suivi d'un espace, sont autorisés.</small>
                                    </div>

                                    {{-- Email --}}
                                    <div class="form-group email">
                                        <label class="required" for="email">E-mail</label>
                                        <input class="form-control" name="email" type="email" value="{{ old('email') }}" id="email" required autocomplete="email">
                                    </div>

                                    {{-- Mot de passe --}}
                                    <div class="form-group password">
                                        <label class="required" for="password">Mot de passe</label>
                                        <div class="input-group js-parent-focus">
                                            <input class="form-control js-child-focus js-visible-password" name="password" id="password" type="password" value="" pattern=".{5,}" autocomplete="new-password" required>
                                            <span class="input-group-btn">
                                                <button class="btn btn-light" type="button" data-action="show-password" data-text-show="Montrer" data-text-hide="Cacher">Montrer</button>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">Au moins 5 caractères</small>
                                    </div>

                                    {{-- Confirmation mot de passe --}}
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmer le mot de passe</label>
                                        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required>
                                    </div>

                                    {{-- tel --}}
                                    <div class="form-group phone">
                                        <label for="tel">Téléphone</label>
                                        <input class="form-control" name="tel" type="tel" value="{{ old('tel') }}" id="tel" placeholder="0612345678">
                                    </div>

                                    {{-- Date de naissance --}}
                                    <div class="form-group birthday">
                                        <label for="birthday">Date de naissance</label>
                                        <input class="form-control" name="birthday" type="text" value="{{ old('birthday') }}" id="birthday" placeholder="DD/MM/YYYY" autocomplete="bday">
                                        <small class="form-text text-muted">(Ex. : 31/05/1970)</small>
                                    </div>

                                    {{-- Newsletter --}}
                                    <div class="form-group newsletter">
                                        <div class="custom-control custom-checkbox">
                                            <input name="newsletter" type="checkbox" value="1" id="newsletter" class="custom-control-input" {{ old('newsletter') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="newsletter">
                                                <span>Recevoir des informations relatives aux produits, aux offres et aux évènements de CUBE</span>
                                                <br>
                                                <em>Votre adresse email est uniquement utilisée pour vous envoyer les actualités de CUBE. Vous pouvez à tout moment utiliser le lien de désabonnement intégré à la newsletter. Voir notre politique de confidentialité.</em>
                                            </label>
                                        </div>
                                    </div>

                                </section>

                                {{-- Footer du formulaire --}}
                                <footer class="form-footer">
                                    <input type="hidden" name="submitCreate" value="1">
                                    <button class="btn btn-primary btn-primary--red form-control-submit ml-md-3" type="submit" data-link-action="save-customer">
                                        <span class="double-arrows double-arrows--white">Valider mes informations</span>
                                    </button>
                                    <p class="description description--xs description--grey mt-3">* Champs obligatoires</p>
                                    <p class="description description--xs description--grey mt-3">
                                        CUBE Bikes France est responsable du traitement et de la collecte des données personnelles obligatoires signalées par un astérisque...
                                    </p>
                                </footer>
                            </form>
                        </section>
                    </section>
                </section>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>