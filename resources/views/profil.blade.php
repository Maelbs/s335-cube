<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mon Compte | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @include('layouts.header')

    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="user-greeting">
                    <span class="welcome-label">Bienvenue</span>
                    <h1 class="user-name">{{ strtolower($client->prenom_client) }} {{ substr(strtolower($client->nom_client), 0, 1) }}.</h1>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-link">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Se déconnecter
                        </button>
                    </form>
                </div>

                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="#" class="nav-item active">TABLEAU DE BORD</a></li>
                        <li><a href="{{ route('profil.update.form') }}" class="nav-item ">MON PROFIL</a></li>
                        <li><a href="#" class="nav-item">MES COMMANDES</a></li>
                        <li><a href="#" class="nav-item">MES ADRESSES</a></li>
                        <li><a href="#" class="nav-item">MES VÉLOS</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="main-content">
            
            <div class="breadcrumb">
                ACCUEIL <span class="separator">></span> VOTRE COMPTE
            </div>

            <div class="dashboard-grid">
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>MON PROFIL</h2>
                        <p class="subtitle">{{ $client->prenom_client }} {{ $client->nom_client }}</p>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-group">
                            <label>Nom :</label>
                            <span>{{ strtoupper($client->nom_client) }}</span>
                        </div>
                        <div class="info-group">
                            <label>Prénom :</label>
                            <span>{{ ucfirst($client->prenom_client) }}</span>
                        </div>
                        <div class="info-group">
                            <label>E-mail :</label>
                            <span>{{ $client->email_client }}</span>
                        </div>
                        <div class="info-group">
                            <label>Téléphone :</label>
                            <span>{{ $client->tel ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="info-group">
                            <label>Né(e) le :</label>
                            <span>{{ optional($client->date_naissance)->format('d/m/Y') ?? 'Non renseigné' }}</span>
                        </div>
                        
                        <a href="{{ route('profil.update.form') }}" class="card-action">Modifier mes informations</a>                    
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>MES COMMANDES</h2>
                    </div>
                    <div class="card-body centered-content">
                        <p>Voir toutes mes commandes</p>
                        <a href="#" class="card-arrow-btn">➜</a>
                    </div>
                </div>

                 <div class="dashboard-card">
                    <div class="card-header">
                        <h2>MES VÉLOS</h2>
                        <p class="subtitle">Vous n'avez pas enregistré de vélo</p>
                    </div>
                    <div class="card-body centered-content">
                        <a href="#" class="text-link">Enregistrer un vélo</a>
                    </div>
                </div>

                 <div class="dashboard-card">
                    <div class="card-body centered-content flex-col">
                        <i class="fa-solid fa-user-lock icon-large"></i>
                        <h3>MES DONNÉES PERSONNELLES</h3>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>