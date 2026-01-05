<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - commercial</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/dashboard.css') }}">
</head>
<body>
    <div class="dashboard-container">
    
        <div class="dashboard-top-bar">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <span>Déconnexion</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="dashboard-header">
            <h1 class="dashboard-title">Espace Commercial</h1>
            <div class="separator"></div>
            <p class="dashboard-subtitle">Bienvenue. Sélectionnez une action pour gérer le catalogue.</p>
        </div>

        <div class="actions-grid">

            <a href="{{ route('commercial.edit.article') }}" class="action-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <span class="card-title">Modifier un<br>Article</span>
                <span class="card-desc">Mettez à jour les prix, stocks ou descriptions.</span>
            </a>
            
            <a href="{{ route('commercial.add.categorie') }}" class="action-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
                <span class="card-title">Ajouter une<br>Catégorie</span>
                <span class="card-desc">Créez de nouvelles familles de produits.</span>
            </a>

            <a href="{{ route('commercial.add.modele') }}" class="action-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <span class="card-title">Ajouter un<br>Modèle</span>
                <span class="card-desc">Définissez un nouveau modèle global.</span>
            </a>

            <a href="{{ route('commercial.add.velo') }}" class="action-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="card-title">Ajouter un<br>Vélo</span>
                <span class="card-desc">Ajoutez une variante spécifique au stock.</span>
            </a>

            <a href="{{ route('commercial.choix.imageModele') }}" class="action-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="card-title">Ajouter une<br>Image Modele</span>
                <span class="card-desc">Ajoutez une image modele.</span>
            </a>

            <a href="{{ route('commercial.choix.caracteristique') }}" class="action-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="card-title">Ajouter une<br>Caractéristique</span>
                <span class="card-desc">Ajoutez une caractéristique.</span>
            </a>
        </div>

    </div>
</body>
</html>


