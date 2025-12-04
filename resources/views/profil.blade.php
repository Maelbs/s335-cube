<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mon Profil | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
</head>
<body>
    @include('layouts.header')

    <div class="profile-wrapper">
        <div class="profile-card">
            
            <div class="card-header">
                <h2>Mon Compte</h2>
                <div class="subtitle">Informations personnelles</div>
            </div>

            <div class="card-body">
                <div class="info-row">
                    <span class="label">Nom</span>
                    <span class="value">{{ strtoupper($client->nom_client) }}</span>
                </div>

                <div class="info-row">
                    <span class="label">Prénom</span>
                    <span class="value">{{ ucfirst($client->prenom_client) }}</span>
                </div>

                <div class="info-row">
                    <span class="label">E-mail</span>
                    <span class="value">{{ $client->email_client }}</span>
                </div>

                <div class="info-row">
                    <span class="label">Téléphone</span>
                    <span class="value">{{ $client->tel ?? 'Non renseigné' }}</span>
                </div>

                <div class="info-row">
                    <span class="label">Membre depuis le</span>
                    <span class="value">{{ $client->date_inscription->format('d/m/Y') }}</span>
                </div>

                <div class="info-row">
                    <span class="label">Date de naissance</span>
                    <span class="value">{{ optional($client->date_naissance)->format('d/m/Y') ?? 'Non renseigné' }}</span>
                </div>
            </div>

            <div class="card-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        Se déconnecter
                    </button>
                </form>
            </div>

        </div>
    </div>
    
    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>