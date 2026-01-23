<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Anonymisation du compte</title>
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
            <h1>Anonymisation du compte</h1>
            <p class="subtitle">
                Cette action entraîne la perte immédiate d’accès à votre compte.
            </p>
        </div>

        <div class="form-section">
            <h2>
                <i class="fas fa-user-secret"></i>
                Informations importantes
            </h2>

            <p>
                En anonymisant votre compte :
            </p>

            <ul style="padding-left:20px;">
                <li>Vos données personnelles seront <strong>définitivement anonymisées</strong></li>
                <li>Votre compte deviendra inaccessible</li>
                <li>Vous serez immédiatement déconnecté</li>
            </ul>

            <p style="color:#d32f2f; font-weight:600; margin-top:15px;">
                Cette action est irréversible.
            </p>
        </div>

        <div class="form-footer">
            <form method="POST" action="{{ route('profil.anonymize') }}">
                @csrf
                <button type="submit" class="btn-pay">
                    Anonymiser mon compte
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
