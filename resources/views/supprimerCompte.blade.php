<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression du compte</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/commande.css') }}">
</head>
<body>

<header>
    <div class="logo" style="padding:15px 20px;">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" height="30">
    </div>
</header>

<div class="checkout-wrapper">
    <div class="checkout-card">

        <div class="card-header">
            <h1>Suppression du compte</h1>
            <p class="subtitle">
                Cette action supprimera définitivement votre compte.
            </p>
        </div>

        <div class="form-section">
            <h2>
                <i class="fas fa-triangle-exclamation"></i>
                Action définitive
            </h2>

            <p>
                La suppression de votre compte entraînera :
            </p>

            <ul style="padding-left:20px;">
                <li>La suppression totale de votre compte</li>
                <li>La perte définitive de toutes vos données</li>
                <li>L’impossibilité de récupérer votre accès</li>
            </ul>

            <p style="color:#d32f2f; font-weight:700; margin-top:15px;">
                Cette action est irréversible.
            </p>
        </div>

        <div class="form-footer">
            <form method="POST" action="{{ route('profil.destroy') }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn-pay">
                    Supprimer définitivement mon compte
                </button>
            </form>

            <div class="secure-badge">
                <a href="{{ route('profil') }}" class="add-address-link">
                    ← Retour au profil (annuler)
                </a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
