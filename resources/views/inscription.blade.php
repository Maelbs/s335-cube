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

                            <form
                                class="js-customer-form needs-validation card card-account text-left cube-validate-form customer-form"
                                id="customer-form" action="{{ route('register.check') }}" method="POST" novalidate
                                autocomplete="off">
                                @csrf

                                <h2 class="h3 font-weight-bold font-normal mt-0 mt-lg-5 mb-4">MES INFORMATIONS
                                    PERSONNELLES</h2>

                                <section class="form-fields">
                                    <div class="form-group lastname">
                                        <label class="required" for="lastname">Nom</label>
                                        <input class="form-control @error('lastname') error-border @enderror"
                                            name="lastname" type="text" value="{{ old('lastname') }}" id="lastname"
                                            required autocomplete="family-name">
                                        @error('lastname') <div class="error-text">{{ $message }}</div> @enderror
                                        <small class="form-text text-muted">Seules les lettres et le point (.) sont
                                            autorisés.</small>
                                    </div>

                                    {{-- Prénom --}}
                                    <div class="form-group firstname">
                                        <label class="required" for="firstname">Prénom</label>
                                        <input class="form-control @error('firstname') error-border @enderror"
                                            name="firstname" type="text" value="{{ old('firstname') }}" id="firstname"
                                            required autocomplete="given-name">
                                        @error('firstname') <div class="error-text">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="form-group email">
                                        <label class="required" for="email">E-mail</label>
                                        <input class="form-control @error('email') error-border @enderror" name="email"
                                            type="email" value="{{ old('email') }}" id="email" required
                                            autocomplete="email">
                                        {{-- C'est ici que l'erreur "Email déjà pris" s'affichera --}}
                                        @error('email') <div class="error-text">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Mot de passe (L'utilisateur DOIT le retaper, c'est normal) --}}
                                    <div class="form-group password">
                                        <label class="required" for="password">Mot de passe</label>
                                        <div class="input-group js-parent-focus">
                                            <input
                                                class="form-control js-child-focus js-visible-password @error('password') error-border @enderror"
                                                name="password" id="password" type="password" value=""
                                                autocomplete="new-password" required>
                                        </div>
                                        @error('password') <div class="error-text">{{ $message }}</div> @enderror
                                        <small class="form-text text-muted">Au moins 5 caractères</small>
                                    </div>

                                    {{-- Confirmation (À retaper aussi) --}}
                                    <div class="form-group">
                                        <label class="required" for="password_confirmation">Confirmer le mot de
                                            passe</label>
                                        <input class="form-control" type="password" name="password_confirmation"
                                            id="password_confirmation" required>
                                    </div>

                                    {{-- Tel --}}
                                    <div class="form-group phone">
                                        <label class="required" for="tel">Téléphone</label>
                                        <input class="form-control @error('tel') error-border @enderror" name="tel"
                                            type="tel" value="{{ old('tel') }}" id="tel" placeholder="0612345678" required>
                                        @error('tel') <div class="error-text">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Date de naissance --}}
                                    <div class="form-group birthday">
                                        <label class="required" for="birthday">Date de naissance</label>
                                        <input class="form-control @error('birthday') error-border @enderror"
                                            name="birthday" type="date" value="{{ old('birthday') }}" id="birthday"
                                            required max="{{ date('Y-m-d') }}" autocomplete="bday">
                                        @error('birthday') <div class="error-text">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Newsletter --}}
                                    <div class="form-group newsletter">
                                        <div class="custom-control custom-checkbox">
                                            <input name="newsletter" type="checkbox" value="1" id="newsletter"
                                                class="custom-control-input" {{ old('newsletter') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="newsletter">
                                                <span>Recevoir des informations relatives aux produits, aux offres et
                                                    aux évènements de CUBE</span>
                                                <br>
                                                <em>Votre adresse email est uniquement utilisée pour vous envoyer les
                                                    actualités de CUBE. Vous pouvez à tout moment utiliser le lien de
                                                    désabonnement intégré à la newsletter. Voir notre politique de
                                                    confidentialité.</em>
                                            </label>
                                        </div>
                                    </div>

                                </section>

                                {{-- Footer du formulaire --}}
                                <footer class="form-footer">
                                    <input type="hidden" name="submitCreate" value="1">
                                    <button class="btn-valider" type="submit" data-link-action="save-customer">
                                        <span class="double-arrows double-arrows--white">Valider mes informations</span>
                                    </button>
                                    <p class="text-connexion">
                                        Vous avez déjà un compte ?<br>
                                        <a class="text-underline" href="{{ route('login') }}">Connectez-vous !</a>
                                    </p>
                                    <p class="description" id="traitement-text">
                                        CUBE Bikes France est responsable du traitement et de la collecte des données
                                        personnelles obligatoires signalées par un astérisque. Ces données sont
                                        nécessaires au traitement de votre commande, au suivi du programme de
                                        fidélisation, à la gestion de la relation commerciale. Elles pourront être
                                        utilisées pour la réalisation d’analyses statistiques. Conformément à la Loi
                                        Informatique et Liberté de 6 janvier 1978, vous bénéficiez d’un droit d’accès,
                                        de rectification et d’opposition aux données vous concernant que vous pouvez
                                        exercez en écrivant à CUBE Bikes France - ZI les 4 Chevaliers - Rond Point de la
                                        République - 17180 Périgny, ou par email service-clients@cubebike.fr en joignant
                                        une copie de votre pièce d’identité recto/verso.
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
    <script src="{{ asset('js/inscription.js') }}" defer></script>
</body>

</html>