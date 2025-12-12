<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une adresse | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/creerAdresses.css') }}"> 
</head>

<body>
    @include('layouts.header')

    <div class="dashboard-container">
        @include('layouts.sideBar')

        <main class="main-content scrollable">
            <div class="cube-container">

                <h1 class="cube-title">Nouvelle Adresse</h1>
                <p class="cube-subtitle">Ajoutez une adresse de livraison à votre carnet.</p>

                <form method="POST" action="{{ route('adresses.create') }}" autocomplete="off" class="creer-form">
                    @csrf

                    <div class="form-group">
                        <label class="required font-weight-bold" for="rue">Rue</label>
                        <input class="form-control" name="rue" type="text" id="rueId" 
                               placeholder="Commencez à taper votre adresse..." required>
                    </div>

                    <div class="form-group">
                        <label class="required font-weight-bold" for="code_postal">Code postal</label>
                        <input class="form-control" name="code_postal" type="text" id="zipId" readonly required>
                    </div>

                    <div class="form-group">
                        <label class="required font-weight-bold" for="ville">Ville</label>
                        <input class="form-control" name="ville" type="text" id="cityId" readonly required>
                    </div>

                    <div class="form-group">
                        <label class="required font-weight-bold" for="pays">Pays</label>
                        <input class="form-control" name="pays" type="text" id="countryId" readonly required>
                    </div>

                    <button class="cube-btn-primary w-100" style="margin-top:20px;">
                        Ajouter cette adresse
                    </button>

                </form>

            </div>
        </main>
    </div>

    <script src="{{ asset('js/livraison.js') }}" defer></script>
</body>
</html>