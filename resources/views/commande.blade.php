<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé | Cube Bikes</title>

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">
    <link rel="stylesheet" href="{{ asset('css/panier.css') }}">
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
                    <h2 class="h4 font-weight-bold text-uppercase mb-4"
                        style="border-bottom: 2px solid #333; padding-bottom: 10px;">
                        <i class="fa-solid fa-location-dot"></i> Livraison
                    </h2>

                    @if($contientVelo)

                        <input type="hidden" name="delivery_mode" value="magasin">

                        @if(isset($magasin) && $magasin)
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
                                        <i class="fa-solid fa-check-circle"></i> Votre vélo sera préparé et monté par ce magasin.
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-warning" style="background-color: #fff3cd; color: #856404;">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                                <strong>Attention :</strong> L'achat d'un vélo nécessite un retrait en magasin.
                                <br>
                                <button type="button" onclick="toggleStoreLocation()" class="btn btn-sm btn-dark mt-2">
                                    <i class="fa-solid fa-map-location-dot"></i> Choisir un magasin partenaire
                                </button>
                            </div>
                        @endif

                    @else
                        <div class="delivery-choices mb-4">
                            
                            <div class="mb-3">
                                <input class="delivery-option-radio" type="radio" name="delivery_mode" id="mode_magasin"
                                    value="magasin" {{ (isset($magasin)) ? 'checked' : '' }} onchange="toggleDeliveryMode()">
                                
                                <label class="delivery-option-card" for="mode_magasin">
                                    @if(isset($magasin) && $magasin)
                                        <div class="magasin-info-card">
                                            <div class="magasin-icon">
                                                <i class="fa-solid fa-shop"></i>
                                            </div>
                                            <div class="magasin-details">
                                                <h3>Retrait en magasin : {{ $magasin->nom_magasin }}</h3>
                                                @php $adresseMag = $magasin->adresses->first(); @endphp
                                                @if($adresseMag)
                                                    <p>{{ $adresseMag->rue }}</p>
                                                    <p>{{ $adresseMag->code_postal }} {{ $adresseMag->ville }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 d-flex align-items-center" style="background-color: #fff3cd; color: #856404;">
                                            <i class="fa-solid fa-triangle-exclamation mr-3" style="font-size: 24px;"></i>
                                            <div>
                                                <strong>Retrait en magasin partenaire</strong><br>
                                                <span class="small">Aucun magasin sélectionné. Cliquez pour choisir sur la carte.</span>
                                            </div>
                                            <button type="button" onclick="toggleStoreLocation();" class="btn btn-sm btn-dark ml-auto">
                                                Choisir
                                            </button>
                                        </div>
                                    @endif
                                </label>
                            </div>

                            <div class="mb-3">
                                <input class="delivery-option-radio" type="radio" name="delivery_mode" id="mode_domicile"
                                    value="domicile" {{ (!isset($magasin)) ? 'checked' : '' }} onchange="toggleDeliveryMode()">

                                <label class="delivery-option-card p-3" for="mode_domicile">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3" style="font-size: 24px; color: #333;">
                                            <i class="fa-solid fa-truck"></i>
                                        </div>
                                        <div>
                                            <h3 style="margin: 0; font-size: 18px; color: #222;">Livraison à domicile</h3>
                                            <p style="margin: 2px 0; color: #555; font-size: 15px;">Livraison classique chez vous.</p>
                                        </div>
                                    </div>
                                </label>
                            </div>

                        </div>

                        <div id="domicile-options" style="display: {{ !isset($magasin) ? 'block' : 'none' }}; margin-top: 20px;">

                            <h3 class="h6 font-weight-bold mb-3" style="margin-left:5px;">Où souhaitez-vous être livré ?</h3>

                            <div class="address-grid">
                                @foreach($adresses as $adresse)
                                    <label class="address-card-wrapper">
                                        <input class="address-card-input" type="radio" name="id_adresse"
                                            value="{{ $adresse->id_adresse }}"
                                            id="adr_{{ $adresse->id_adresse }}"
                                            onchange="toggleNewAddressForm(false)">
                                    <div class="address-card">
                                        <span class="card-indicator"></span>

                                        @if($adresse->pivot && $adresse->pivot->nom_destinataire)
                                            <span style="font-size: 0.8rem; color: #bfa15f; font-weight:bold; margin-bottom:5px;">
                                                <i class="fa-solid fa-gift"></i> Pour {{ $adresse->pivot->prenom_destinataire }}
                                            </span>
                                        @endif
                                        
                                        <span class="addr-street">{{ $adresse->rue }}</span>
                                        <span class="addr-city">{{ $adresse->code_postal }} {{ $adresse->ville }}</span>
                                        <span class="addr-city">{{ $adresse->pays }}</span>
                                    </div>
                                    </label>
                                @endforeach

                                <label class="address-card-wrapper">
                                    <input class="address-card-input" type="radio" name="id_adresse"
                                        value="new" id="adr_new"
                                        onchange="toggleNewAddressForm(true)"
                                        {{ $adresses->isEmpty() ? 'checked' : '' }}>
                                    <div class="address-card add-new-card">
                                        <i class="fa-solid fa-circle-plus"></i>
                                        <span style="font-weight: 600;">Nouvelle adresse</span>
                                    </div>
                                </label>
                            </div>

                            <div id="new-address-form" style="display: {{ $adresses->isEmpty() ? 'block' : 'none' }};">
                                <div style="background: #fff; border: 1px solid #eee; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
                                    <section class="form-fields">
                                        <div class="gift-card">
                                            <div class="gift-header">
                                                <div class="gift-icon-wrapper"><i class="fa-solid fa-gift"></i></div>
                                                <h5 class="gift-title">Souhaitez-vous faire un cadeau ?</h5>
                                            </div>
                                            <p class="gift-desc">Remplissez ces informations <strong>uniquement</strong> si le colis est réceptionné par une tierce personne.<br>Si c'est pour vous, laissez vide.</p>
                                            <div class="row gift-inputs">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nom_destinataire">Nom du destinataire</label>
                                                    <input class="form-control" name="nom_destinataire" type="text" value="{{ old('nom_destinataire') }}" id="nom_destinataire" placeholder="Ex: Dupont" autocomplete="off">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="prenom_destinataire">Prénom du destinataire</label>
                                                    <input class="form-control" name="prenom_destinataire" type="text" value="{{ old('prenom_destinataire') }}" id="prenom_destinataire" placeholder="Ex: Marie" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modern-form-group mb-3 position-relative">
                                            <label class="required" for="rue">Rue</label>
                                            <input class="modern-input" name="rue" type="text" value="{{ old('rue') }}" id="rue" placeholder="Commencez à saisir votre adresse..." autocomplete="off">
                                            <ul id="adresse-suggestions" class="list-group position-absolute" style="z-index: 1000; display:none; width: 100%; margin-top: 5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"></ul>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 modern-form-group mb-3">
                                                <label class="required" for="zipcode">Code postal</label>
                                                <input class="modern-input" name="zipcode" type="text" value="{{ old('zipcode') }}" id="zipcode" readonly>
                                            </div>
                                            <div class="col-md-4 modern-form-group mb-3">
                                                <label class="required" for="city">Ville</label>
                                                <input class="modern-input" name="city" type="text" value="{{ old('city') }}" id="city" readonly>
                                            </div>
                                            <div class="col-md-4 modern-form-group mb-3">
                                                <label class="required" for="country">Pays</label>
                                                <input class="modern-input" name="country" type="text" value="{{ old('country') }}" id="country" readonly>
                                            </div>
                                        </div>
                                    </section>
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="form-group custom-toggle-container mb-0">
                                            <label class="custom-toggle-container d-flex align-items-center" for="save_address" style="width: 100%; cursor: pointer;">
                                                <div class="toggle-switch mr-3">
                                                    <input type="checkbox" name="save_address" id="save_address" value="1">
                                                    <span class="slider"></span>
                                                </div>
                                                <span class="toggle-label font-weight-bold text-dark">Enregistrer cette adresse pour mes prochaines commandes</span>
                                            </label>
                                        </div>
                                    </div>
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
    <script src="{{ asset('js/commande.js') }}"></script>
</body>

</html>