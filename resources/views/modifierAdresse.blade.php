<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une adresse | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modifierAdresse.css') }}">
</head>

<body>
@include('layouts.header')

<div class="dashboard-container">
    @include('layouts.sideBar')

    <main class="main-content scrollable">
        <div class="cube-container">

            <h1 class="cube-title">Modifier l’adresse</h1>
            
            <div class="cube-subtitle" style="margin-bottom: 30px;">
                @if($isFacturation)
                    <span style="background-color: #ffd700; color: #000; padding: 8px 16px; border-radius: 4px; font-weight: 800; text-transform: uppercase; font-size: 0.9rem; border: 1px solid #e6c200; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Adresse de facturation
                    </span>
                @else
                    <span style="background-color: #e9ecef; color: #555; padding: 8px 16px; border-radius: 4px; font-weight: 800; text-transform: uppercase; font-size: 0.9rem; border: 1px solid #dee2e6; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-truck"></i> Adresse de livraison
                    </span>
                @endif
            </div>

            <form method="POST" action="{{ route('adresses.update', $adresse->id_adresse) }}">
                @csrf

                <div class="form-group">
                    <label>Rue</label>
                    <input class="form-control" type="text" name="rue" id="rueId" 
                           value="{{ old('rue', $adresse->rue) }}" required>
                </div>

                <div class="form-group">
                    <label>Code Postal</label>
                    <input class="form-control" type="text" name="code_postal" id="zipId"
                        value="{{ old('code_postal', $adresse->code_postal) }}" readonly required>
                </div>

                <div class="form-group">
                    <label>Ville</label>
                    <input class="form-control" type="text" name="ville" id="cityId"
                        value="{{ old('ville', $adresse->ville) }}" readonly required>
                </div>

                <div class="form-group">
                    <label>Pays</label>
                    <input class="form-control" type="text" name="pays" id="countryId" value="{{ old('pays', $adresse->pays) }}" readonly required>
                </div>

                <button class="cube-btn-primary w-100" style="margin-top:20px;">Enregistrer les modifications</button>
            </form>

        </div>
    </main>
</div>

<script src="{{ asset('js/livraison.js') }}" defer></script>

</body>
</html>