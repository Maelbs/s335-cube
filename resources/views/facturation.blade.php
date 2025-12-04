<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CUBE Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <style>
        /* Style simple pour cacher/montrer */
        .d-none { display: none !important; }
        
        /* Style du bouton "Suivant" */
        .btn-next {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .btn-next:hover { background-color: #555; }

        /* Style section adresse */
        #address-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    @include('layouts.header')
    
    <section id="wrapper" class="container" style="margin-top: 10rem">
        <div class="row">
            <div id="content-wrapper" class="col-12 px-0">
                <section class="register-form text-center">
                    
                    <h1 class="h3 register-form__title mt-5">Créez votre compte</h1>
                    <p class="text-left mb-5">
                        Vous avez déjà un compte ? <a href="{{ route('login') }}">Connectez-vous !</a>
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger" style="color: red; text-align: left; margin-bottom: 20px;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- FORMULAIRE UNIQUE -->
                    <form class="js-customer-form needs-validation card card-account text-left cube-validate-form customer-form" 
                        id="customer-form" 
                        action="{{ route('register.submit') }}" 
                        method="POST" 
                        autocomplete="off">
                        @csrf

                        <!-- PARTIE 1 : INFOS PERSOS -->
                        <div id="personal-info-section">
                            <h2 class="h3 font-weight-bold font-normal mt-0 mt-lg-5 mb-4">MES INFORMATIONS PERSONNELLES</h2>

                            <section class="form-fields">
                                {{-- Nom --}}
                                <div class="form-group">
                                    <label class="required" for="lastname">Nom</label>
                                    <input class="form-control" name="lastname" type="text" value="{{ old('lastname') }}" id="lastname" required>
                                </div>

                                {{-- Prénom --}}
                                <div class="form-group">
                                    <label class="required" for="firstname">Prénom</label>
                                    <input class="form-control" name="firstname" type="text" value="{{ old('firstname') }}" id="firstname" required>
                                </div>

                                {{-- Email --}}
                                <div class="form-group">
                                    <label class="required" for="email">E-mail</label>
                                    <input class="form-control" name="email" type="email" value="{{ old('email') }}" id="email" required>
                                </div>

                                {{-- Mot de passe --}}
                                <div class="form-group">
                                    <label class="required" for="password">Mot de passe</label>
                                    <input class="form-control" name="password" id="password" type="password" required>
                                    <small class="text-muted">Au moins 6 caractères</small>
                                </div>

                                {{-- Confirmation --}}
                                <div class="form-group">
                                    <label class="required" for="password_confirmation">Confirmation</label>
                                    <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required>
                                </div>

                                {{-- Téléphone --}}
                                <div class="form-group">
                                    <label class="required" for="tel">Téléphone</label>
                                    <input class="form-control" name="tel" type="tel" value="{{ old('tel') }}" id="tel" required>
                                </div>

                                {{-- Date Naissance --}}
                                <div class="form-group">
                                    <label for="birthday">Date de naissance</label>
                                    <input class="form-control" name="birthday" type="text" value="{{ old('birthday') }}" id="birthday" placeholder="JJ/MM/AAAA">
                                </div>
                            </section>

                            <!-- Bouton pour afficher la suite -->
                            <button type="button" id="btn-show-address" class="btn-next">
                                SUIVANT : MON ADRESSE
                            </button>
                        </div>


                        <!-- PARTIE 2 : ADRESSE (Cachée par défaut) -->
                        <div id="address-section" class="d-none">
                            <h2 class="h3 font-weight-bold font-normal mt-4 mb-4">MON ADRESSE</h2>

                            <section class="form-fields">
                                {{-- Rue --}}
                                <div class="form-group">
                                    <label class="required" for="address">Adresse</label>
                                    <input class="form-control" name="address" type="text" value="{{ old('address') }}" id="address" required>
                                </div>

                                {{-- Complément (Optionnel) --}}
                                <div class="form-group">
                                    <label for="address_complement">Complément d'adresse (Optionnel)</label>
                                    <input class="form-control" name="address_complement" type="text" value="{{ old('address_complement') }}" id="address_complement">
                                </div>

                                {{-- Code Postal --}}
                                <div class="form-group">
                                    <label class="required" for="zipcode">Code postal</label>
                                    <input class="form-control" name="zipcode" type="text" value="{{ old('zipcode') }}" id="zipcode" required>
                                </div>

                                {{-- Ville --}}
                                <div class="form-group">
                                    <label class="required" for="city">Ville</label>
                                    <input class="form-control" name="city" type="text" value="{{ old('city') }}" id="city" required>
                                </div>

                                {{-- Pays (Fixe France pour l'instant) --}}
                                <div class="form-group">
                                    <label class="required" for="country">Pays</label>
                                    <input class="form-control" name="country" type="text" value="France" id="country" readonly>
                                </div>
                            </section>

                            <footer class="form-footer mt-4">
                                <button class="btn btn-primary btn-primary--red form-control-submit ml-md-3" type="submit">
                                    <span class="double-arrows double-arrows--white">Valider et recevoir le code</span>
                                </button>
                                <p class="description description--xs description--grey mt-3">* Champs obligatoires</p>
                            </footer>
                        </div>

                    </form>
                </section>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
    
    <!-- Script pour gérer l'affichage progressif -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnNext = document.getElementById('btn-show-address');
            const addressSection = document.getElementById('address-section');
            const btnNextContainer = btnNext.parentElement; // Pour cacher le bouton après clic

            btnNext.addEventListener('click', function() {
                // Simple validation HTML5 avant de passer à la suite
                const inputs = document.querySelectorAll('#personal-info-section input[required]');
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        input.style.borderColor = 'red';
                    } else {
                        input.style.borderColor = '#ccc';
                    }
                });

                if (isValid) {
                    // Affiche la section adresse
                    addressSection.classList.remove('d-none');
                    // Cache le bouton "Suivant" car on a maintenant le bouton "Valider" en bas
                    btnNext.style.display = 'none';
                    
                    // Scroll fluide vers la nouvelle section
                    addressSection.scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert("Veuillez remplir tous les champs obligatoires avant de continuer.");
                }
            });
        });
    </script>
</body>
</html>