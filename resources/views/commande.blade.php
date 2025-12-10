<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande</title>
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">
</head>
<body>

    <div class="container">

        <h1>Paiement</h1>

        <form id="payment-form" method="POST" action="{{ route('stripe.payment') }}">
            @csrf

            <h2>Adresse de livraison</h2>

            <select name="id_adresse" class="select-adresse" required>
                <option value="">-- Sélectionner une adresse --</option>
                @foreach($adresses as $adresse)
                    <option value="{{ $adresse->id_adresse }}">
                        {{ $adresse->rue }}, {{ $adresse->code_postal }} {{ $adresse->ville }}
                    </option>
                @endforeach
            </select>

            <p>Choisissez votre méthode de paiement :</p>

            <div class="options">
                <label class="option">
                    <input type="radio" name="methode" value="stripe" required checked>
                    <div class="box">
                        <span>Carte Bancaire</span>
                    </div>
                </label>

                <label class="option">
                    <input type="radio" name="methode" value="paypal" required>
                    <div class="box">
                        <span>PayPal</span>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn-big">PAYER</button>

        </form>

    </div>

    <script>
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
    </script>

</body>
</html>
