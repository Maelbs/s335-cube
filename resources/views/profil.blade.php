<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
</head>
<body>
    @include('layouts.header')

    <div class="profile-card">
        <h2>PROFIL</h2>

        <p><strong>Nom :</strong> {{ $client->nom_client }}</p>

        <p><strong>Prenom :</strong> {{ $client->prenom_client }}</p>

        <p><strong>Email :</strong> {{ $client->email_client }}</p>

        <p><strong>Téléphone :</strong> {{ $client->tel ?? 'Non renseigné' }}</p>

        <p><strong>Créé le :</strong> {{ $client->date_inscription->format('d/m/Y') }}</p>

        <p><strong>Anniversaire :</strong> {{ optional($client->date_naissance)->format('d/m/Y')}}</p>

        <a href="#">Modifier le profil</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="cursor:pointer;">LOG OUT</button>
        </form>
    </div>
    
    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>
