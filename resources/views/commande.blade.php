<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé | Cube Bikes</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,600;0,800;1,800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">

    <style>
        /* Style spécifique pour l'encadré magasin */
        .magasin-info-card {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-left: 5px solid #333;
            /* Noir Cube */
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
            /* Vert pour rassurer */
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

                <div class="form-section">
                    <h2><i class="fa-solid fa-location-dot"></i> Livraison</h2>

                    {{-- SI UN MAGASIN EST SÉLECTIONNÉ (VÉLO) --}}
                    @if(isset($magasin) && $magasin)

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
                                    <i class="fa-solid fa-check-circle"></i> Votre commande sera livrée et préparée ici.
                                </div>
                            </div>
                        </div>

                        {{-- SINON (ACCESSOIRES OU DEFAUT) --}}
                    @else

                        <div class="select-wrapper">
                            <select name="id_adresse" class="custom-select" required>
                                <option value="" disabled selected>-- Choisissez votre adresse --</option>
                                @forelse($adresses as $adresse)
                                    <option value="{{ $adresse->id_adresse }}">
                                        {{ $adresse->rue }} - {{ $adresse->code_postal }} {{ $adresse->ville }}
                                    </option>
                                @empty
                                    <option value="" disabled>Aucune adresse enregistrée</option>
                                @endforelse
                            </select>
                            <i class="fa-solid fa-chevron-down select-icon"></i>
                        </div>

                        @if($adresses->isEmpty())
                            <a href="{{ route('profil') }}" class="add-address-link">+ Ajouter une nouvelle adresse</a>
                        @endif

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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>

</body>

</html>