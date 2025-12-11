<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Sécurisé | Cube Bikes</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,600;0,800;1,800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">
</head>
<body>

    @include('layouts.header')

    <div class="checkout-wrapper">
        <div class="checkout-card">
            
            <div class="card-header">
                <h1>Finaliser ma commande</h1>
                <p class="subtitle">Sélectionnez votre adresse et votre moyen de paiement</p>
            </div>

            <form id="payment-form" method="POST" action="{{ route('stripe.payment') }}">
                @csrf

                <div class="form-section">
                    <h2><i class="fa-solid fa-location-dot"></i> Adresse de livraison</h2>
                    
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
        document.addEventListener('DOMContentLoaded', function() {
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