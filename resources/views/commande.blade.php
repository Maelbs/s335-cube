<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé | Cube Bikes</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,600;0,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">
    {{-- J'ai inclus ton CSS personnalisé pour les cartes magasin ici --}}
    <link rel="stylesheet" href="{{ asset('css/panier.css') }}"> 

    <style>
        /* Styles spécifiques pour la carte magasin */
        .magasin-info-card {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-left: 5px solid #333;
            padding: 20px;
            border-radius: 5px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .magasin-icon {
            font-size: 24px;
            color: #333;
            margin-top: 5px;
        }

        .magasin-details h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #222;
        }

        .magasin-details p {
            margin: 2px 0;
            color: #555;
            font-size: 15px;
        }

        .delivery-note {
            margin-top: 10px;
            font-size: 13px;
            color: #28a745;
            font-weight: 600;
        }
    </style>
</head>

<body>

    @include('layouts.header')

    <div class="checkout-wrapper">
        <div class="checkout-card">

            <div class="card-header">
                <h1>Finaliser ma commande</h1>
                <p class="subtitle">Récapitulatif de livraison et paiement</p>
            </div>

            <form id="payment-form" method="POST" action="{{ route('stripe.payment') }}">
                @csrf

                <div class="form-section mb-5">
                    <h2 class="h4 font-weight-bold text-uppercase mb-4" style="border-bottom: 2px solid #333; padding-bottom: 10px;">
                        <i class="fa-solid fa-location-dot"></i> Livraison
                    </h2>

                    @if(isset($magasin) && $magasin && $contientVelo)
                        {{-- CAS 1 : VÉLO (Magasin forcé) --}}
                        <input type="hidden" name="id_adresse" value="magasin_session">
                        
                        <div class="magasin-info-card">
                            <div class="magasin-icon">
                                <i class="fa-solid fa-shop"></i>
                            </div>
                            <div class="magasin-details">
                                <h3>{{ $magasin->nom_magasin }}</h3>
                                @php $adresseMag = $magasin->adresses->first(); @endphp
                                @if($adresseMag)
                                    <p>{{ $adresseMag->rue }}</p>
                                    <p>{{ $adresseMag->code_postal }} {{ $adresseMag->ville }}</p>
                                @endif
                                <div class="delivery-note">
                                    <i class="fa-solid fa-check-circle"></i> Votre commande sera préparée ici.
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- CAS 2 : ACCESSOIRES (Choix) --}}
                        <div class="delivery-choices">
                            
                            {{-- OPTION A : Retrait Magasin --}}
                            @if(isset($magasin))
                            <div class="form-check mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="delivery_mode" id="mode_magasin" value="magasin" checked onchange="toggleDeliveryMode()">
                                <label class="form-check-label w-100" for="mode_magasin" style="cursor: pointer; margin-left: 10px;">
                                    <strong><i class="fa-solid fa-shop"></i> Retrait en magasin</strong> - {{ $magasin->nom_magasin }}
                                </label>
                            </div>
                            @endif

                            {{-- OPTION B : Livraison Domicile --}}
                            <div class="form-check mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="delivery_mode" id="mode_domicile" value="domicile" {{ !isset($magasin) ? 'checked' : '' }} onchange="toggleDeliveryMode()">
                                <label class="form-check-label w-100" for="mode_domicile" style="cursor: pointer; margin-left: 10px;">
                                    <strong><i class="fa-solid fa-truck"></i> Livraison à domicile</strong>
                                </label>
                            </div>
                        </div>

                        {{-- CONTENEUR ADRESSES (Caché si Magasin sélectionné) --}}
                        <div id="domicile-options" style="display: {{ !isset($magasin) ? 'block' : 'none' }}; margin-left: 20px;">
                            
                            <p class="mb-3 font-weight-bold">Choisissez votre adresse :</p>

                            {{-- Liste des adresses existantes --}}
                            @foreach($adresses as $adresse)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="id_adresse" id="adr_{{ $adresse->id_adresse }}" value="{{ $adresse->id_adresse }}" onchange="toggleNewAddressForm(false)">
                                    <label class="form-check-label" for="adr_{{ $adresse->id_adresse }}">
                                        {{ $adresse->rue }}, {{ $adresse->code_postal }} {{ $adresse->ville }}
                                    </label>
                                </div>
                            @endforeach

                            {{-- Option "Nouvelle adresse" --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="id_adresse" id="adr_new" value="new" onchange="toggleNewAddressForm(true)" {{ $adresses->isEmpty() ? 'checked' : '' }}>
                                <label class="form-check-label text-primary font-weight-bold" for="adr_new">
                                    + Ajouter une nouvelle adresse
                                </label>
                            </div>

                            {{-- Formulaire Nouvelle Adresse --}}
                            <div id="new-address-form" style="display: {{ $adresses->isEmpty() ? 'block' : 'none' }}; border-left: 3px solid #007bff; padding-left: 15px; margin-top: 15px;">
                                
                                <section class="form-fields">
                                    <div class="form-group mb-3">
                                        <label class="required font-weight-bold" for="rue">Rue</label>
                                        <input class="form-control" name="rue" type="text" value="{{ old('rue') }}" id="rue" placeholder="Ex: 10 rue de la paix..." autocomplete="off">
                                        <ul id="adresse-suggestions" class="list-group position-absolute" style="z-index: 1000; display:none; width: 100%;"></ul>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="required font-weight-bold" for="zipcode">Code postal</label>
                                        <input class="form-control" name="zipcode" type="text" value="{{ old('zipcode') }}" id="zipcode" readonly style="background-color: #e9ecef;">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="required font-weight-bold" for="country">Pays</label>
                                        <input class="form-control" name="country" type="text" value="{{ old('country') }}" id="country" readonly style="background-color: #e9ecef;">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="required font-weight-bold" for="city">Ville</label>
                                        <input class="form-control" name="city" type="text" value="{{ old('city') }}" id="city" readonly style="background-color: #e9ecef;">
                                    </div>
                                </section>

                                <div class="form-group mt-3 custom-toggle-container">
                                    <label class="custom-toggle-container d-flex align-items-center" for="save_address" style="width: 100%; cursor: pointer;">
                                        <div class="toggle-switch mr-2">
                                            {{-- J'ai retiré l'attribut "checked" ici --}}
                                            <input type="checkbox" name="save_address" id="save_address" value="1"> 
                                            <span class="slider"></span> 
                                        </div>
                                        <span class="toggle-label ml-2">Enregistrer cette adresse pour mes prochaines commandes</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-section">
                    <h2><i class="fa-regular fa-credit-card"></i> Méthode de paiement</h2>

                    <div class="payment-options">

                        <label class="payment-option">
                            <input type="radio" name="methode" value="stripe" checked>
                            <div class="option-box">
                                <div class="icon-wrapper stripe-color">
                                    <i class="fa-brands fa-cc-visa"></i>
                                    <i class="fa-brands fa-cc-mastercard"></i>
                                </div>
                                <span class="option-title">Carte Bancaire</span>
                                <span class="option-desc">Paiement sécurisé via Stripe</span>
                                <div class="check-circle"><i class="fa-solid fa-check"></i></div>
                            </div>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="methode" value="paypal">
                            <div class="option-box">
                                <div class="icon-wrapper paypal-color">
                                    <i class="fa-brands fa-paypal"></i>
                                </div>
                                <span class="option-title">PayPal</span>
                                <span class="option-desc">Simple et rapide</span>
                                <div class="check-circle"><i class="fa-solid fa-check"></i></div>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn-pay">
                        <span>Payer ma commande</span>
                    </button>
                    <div class="secure-badge">
                        <i class="fa-solid fa-lock"></i> Paiement 100% sécurisé crypté SSL
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script src="{{ asset('js/header.js') }}"></script>
    
    {{-- SCRIPT UNIQUE ET CORRIGÉ --}}
    <script>
        // Fonctions Globales pour être accessibles via les onclick/onchange HTML
        function toggleDeliveryMode() {
            const isMagasin = document.getElementById('mode_magasin') ? document.getElementById('mode_magasin').checked : false;
            const divDomicile = document.getElementById('domicile-options');
            
            if (divDomicile) {
                divDomicile.style.display = isMagasin ? 'none' : 'block';

                if (!isMagasin) {
                    const checkedAddr = document.querySelector('input[name="id_adresse"]:checked');
                    if (!checkedAddr) {
                        const newAddrRadio = document.getElementById('adr_new');
                        if(newAddrRadio) {
                            newAddrRadio.checked = true;
                            toggleNewAddressForm(true);
                        }
                    } else {
                        // Si une adresse est déjà cochée (ex: validation échouée), on vérifie si c'est "new"
                        toggleNewAddressForm(checkedAddr.value === 'new');
                    }
                }
            }
        }

        function toggleNewAddressForm(show) {
            const form = document.getElementById('new-address-form');
            if(!form) return;

            const inputs = form.querySelectorAll('input[type="text"]');
            
            if (show) {
                form.style.display = 'block';
                // On active les champs pour qu'ils soient envoyés
                inputs.forEach(input => input.disabled = false); 
            } else {
                form.style.display = 'none';
                // On désactive les champs pour qu'ils ne bloquent pas la validation
                inputs.forEach(input => input.disabled = true); 
            }
        }

        // Écouteurs d'événements au chargement du DOM
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Gestion du changement d'action du formulaire (Stripe/PayPal)
            const form = document.getElementById('payment-form');
            const radios = document.querySelectorAll('input[name="methode"]');

            radios.forEach(radio => {
                radio.addEventListener('change', () => {
                    if (radio.value === 'stripe') {
                        form.action = "{{ route('stripe.payment') }}";
                    } else if (radio.value === 'paypal') {
                        form.action = "{{ route('paypal.payment') }}";
                    }
                });
            });

            // 2. Initialisation de l'affichage Livraison
            // Important : Appeler cette fonction au chargement pour gérer le retour d'erreur (validation Laravel)
            toggleDeliveryMode();
            
            // Si "Nouvelle adresse" est pré-cochée (ex: erreur de validation), on s'assure que les champs sont actifs
            const newAddrRadio = document.getElementById('adr_new');
            if(newAddrRadio && newAddrRadio.checked) {
                toggleNewAddressForm(true);
            } else {
                // Sinon on s'assure qu'ils sont désactivés si le formulaire est caché
                const formNewAddr = document.getElementById('new-address-form');
                if(formNewAddr && formNewAddr.style.display === 'none') {
                    toggleNewAddressForm(false);
                }
            }

            // 3. API GOUV (Autocomplétion)
            const rueInput = document.getElementById('rue');
            const suggestionsList = document.getElementById('adresse-suggestions');

            if (rueInput) {
                rueInput.addEventListener('input', function() {
                    const query = this.value;
                    if (query.length > 3) {
                        fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=5`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsList.innerHTML = '';
                                suggestionsList.style.display = 'block';
                                
                                data.features.forEach(feature => {
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item list-group-item-action';
                                    li.style.cursor = 'pointer';
                                    li.textContent = feature.properties.label;
                                    
                                    li.addEventListener('click', function() {
                                        document.getElementById('rue').value = feature.properties.name;
                                        document.getElementById('zipcode').value = feature.properties.postcode;
                                        document.getElementById('city').value = feature.properties.city;
                                        document.getElementById('country').value = 'France';
                                        suggestionsList.style.display = 'none';
                                    });
                                    suggestionsList.appendChild(li);
                                });
                            });
                    } else {
                        suggestionsList.style.display = 'none';
                    }
                });

                document.addEventListener('click', function(e) {
                    if (e.target !== rueInput && e.target !== suggestionsList) {
                        if(suggestionsList) suggestionsList.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>