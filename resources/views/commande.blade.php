<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Commande</title>
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">
</head>
<body>

@section('content')
    <div class="container">
        <h1 class="text-center">Finaliser votre commande</h1>

        <!-- Formulaire de Commande -->
        <form action="{{ route('commande.finaliser') }}" method="POST" id="commandeForm">
            @csrf

            <!-- Section Livraison -->
            <div id="livraisonSection">
                @include('layouts.commande.livraison')
                <button type="button" id="continuerLivraisonBtn" class="btn btn-primary">Continuer</button>
            </div>

            <!-- Section Paiement (initialement cachée) -->
            <div id="paiementSection" style="display: none;">
                @include('layouts.commande.paiement')
                <button type="button" id="retourLivraisonBtn" class="btn btn-secondary">Retour</button>
                <button type="button" id="continuerPaiementBtn" class="btn btn-primary">Continuer</button>
            </div>

            <!-- Section Récapitulatif (initialement cachée) -->
            <div id="recapitulatifSection" style="display: none;">
                @include('layouts.commande.recapitulatif')
                <button type="button" id="retourPaiementBtn" class="btn btn-secondary">Retour</button>
                <button type="submit" class="btn btn-success">Payer et confirmer la commande</button>
            </div>
        </form>
    </div>

    <script>
        // Étape 1: Continuer de la section Livraison vers Paiement
        document.getElementById('continuerLivraisonBtn').addEventListener('click', function() {
            const livraisonType = document.getElementById('livraison').value;
            if (!livraisonType) {
                alert('Veuillez choisir un type de livraison');
                return;
            }

            // Masquer la section livraison et afficher la section paiement
            document.getElementById('livraisonSection').style.display = 'none';
            document.getElementById('paiementSection').style.display = 'block';
        });

        // Retour de la section Paiement vers Livraison
        document.getElementById('retourLivraisonBtn').addEventListener('click', function() {
            document.getElementById('livraisonSection').style.display = 'block';
            document.getElementById('paiementSection').style.display = 'none';
        });

        // Étape 2: Continuer de la section Paiement vers Récapitulatif
        document.getElementById('continuerPaiementBtn').addEventListener('click', function() {
            const paymentMethod = document.getElementById('paiement_method').value;
            if (!paymentMethod) {
                alert('Veuillez choisir un mode de paiement');
                return;
            }

            // Masquer la section paiement et afficher le récapitulatif
            document.getElementById('paiementSection').style.display = 'none';
            document.getElementById('recapitulatifSection').style.display = 'block';
        });

        // Retour de la section Récapitulatif vers Paiement
        document.getElementById('retourPaiementBtn').addEventListener('click', function() {
            document.getElementById('paiementSection').style.display = 'block';
            document.getElementById('recapitulatifSection').style.display = 'none';
        });
    </script>

@endsection

</body>
</html>
