<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation</title>
</head>
<body>
    @section('content')
    <div class="cube-checkout-container">
        
        {{-- COLONNE GAUCHE : ÉTAPES ET PAIEMENT --}}
        <div class="checkout-main">
            
            {{-- Étapes précédentes (Visuel seulement pour l'immersion) --}}
            <div class="checkout-step completed">
                <div class="step-header">
                    <span>1 - INFORMATIONS PERSONNELLES</span>
                    <a href="#" class="edit-link"><i class="fa fa-pencil"></i> Modifier</a>
                </div>
            </div>
            <div class="checkout-step completed">
                <div class="step-header">
                    <span>2 - ADRESSES</span>
                    <a href="#" class="edit-link"><i class="fa fa-pencil"></i> Modifier</a>
                </div>
            </div>
            <div class="checkout-step completed">
                <div class="step-header">
                    <span>3 - MODE DE LIVRAISON</span>
                    <a href="#" class="edit-link"><i class="fa fa-pencil"></i> Modifier</a>
                </div>
            </div>

            {{-- Étape 4 : PAIEMENT (Active) --}}
            <div class="checkout-step active">
                <h2 class="step-title">4 - PAIEMENT</h2>

                <div class="payment-box">
                    <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                        @csrf

                        {{-- Option 1 : PayPal --}}
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="payment_paypal" value="paypal" class="cube-radio" required>
                            <label for="payment_paypal" class="payment-label">
                                <span class="label-text">Payer avec PayPal</span>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="payment-logo paypal-logo">
                            </label>
                        </div>

                        {{-- Option 2 : Carte Bancaire --}}
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="payment_card" value="card" class="cube-radio">
                            <label for="payment_card" class="payment-label">
                                <span class="label-text">Payer par carte bancaire</span>
                                <div class="card-logos">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="CB" style="opacity:0.6"> {{-- Exemple CB --}}
                                </div>
                            </label>
                        </div>

                        {{-- Option 3 : Oney (Optionnel, pour coller à l'image) --}}
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="payment_oney3" value="oney3" class="cube-radio">
                            <label for="payment_oney3" class="payment-label">
                                <span class="label-text">Payer en 3x par carte bancaire avec Oney</span>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6d/Oney_logo.svg/1200px-Oney_logo.svg.png" alt="Oney" class="payment-logo oney-logo">
                                <span class="oney-badge">3x</span>
                            </label>
                        </div>

                        {{-- Conditions Générales --}}
                        <div class="terms-box">
                            <input type="checkbox" name="cgv" id="cgv" required>
                            <label for="cgv">
                                J'ai lu les <a href="#">conditions générales de vente</a> et j'y adhère sans réserve.
                            </label>
                        </div>

                        {{-- Bouton Payer --}}
                        <button type="submit" class="btn-cube-pay">
                            <span>&#9658;</span> Payer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE : RÉCAPITULATIF --}}
        <div class="checkout-sidebar">
            <div class="summary-box">
                <h3>RÉCAPITULATIF</h3>
                
                <div class="cart-summary-content">
                    <div class="summary-header">
                        <span>{{ count($cartItems ?? []) }} article(s)</span>
                        <a href="#" class="toggle-details">afficher les détails ⌄</a>
                    </div>

                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span>{{ number_format($subtotal ?? 429.00, 2, ',', ' ') }} €</span>
                    </div>

                    <div class="summary-row">
                        <span>Livraison</span>
                        <span>{{ $shipping ?? 'gratuit' }}</span>
                    </div>

                    <div class="summary-row total-row">
                        <span>Total TTC</span>
                        <span>{{ number_format($total ?? 429.00, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="tax-info">
                        Taxes incluses : {{ number_format($tax ?? 71.50, 2, ',', ' ') }} €
                    </div>
                </div>
            </div>

            <div class="promo-box">
                <h3>CODE PROMO</h3>
                <form action="#" class="promo-form">
                    <input type="text" placeholder="Code promo">
                    <button type="submit">Appliquer <span>&#9658;</span></button>
                </form>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>