<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

        </div>

    </div>
</body>
</html>
<style>
    /* Variables CUBE */
    :root {
        --cube-red: #e30613;
        --cube-black: #1a1a1a;
        --cube-dark-grey: #2f353a;
        --cube-light-grey: #f4f6f8;
        --cube-white: #ffffff;
    }

    /* Conteneur principal */
    .dashboard-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: var(--cube-black);
    }

    /* --- Barre du haut pour le bouton déconnexion --- */
    .dashboard-top-bar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn-logout {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        background-color: transparent;
        border: 2px solid var(--cube-red);
        color: var(--cube-red);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .btn-logout:hover {
        background-color: var(--cube-red);
        color: var(--cube-white);
    }

    .btn-logout svg {
        width: 18px;
        height: 18px;
        stroke-width: 2.5;
    }

    /* En-tête */
    .dashboard-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 900;
        text-transform: uppercase;
        font-style: italic;
        margin: 0;
        letter-spacing: 1px;
    }

    .dashboard-subtitle {
        font-size: 1.1rem;
        color: #666;
        margin-top: 10px;
        font-weight: 300;
    }

    /* Séparateur rouge */
    .separator {
        width: 60px;
        height: 4px;
        background-color: var(--cube-red);
        margin: 20px auto 0;
    }

    /* Grille des actions */
    .actions-grid {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    /* Carte d'action */
    .action-card {
        background-color: var(--cube-white);
        width: 260px; /* Légèrement réduit pour faire tenir 4 cartes si besoin */
        padding: 40px 25px;
        border-radius: 4px;
        text-decoration: none;
        color: var(--cube-black);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #e0e0e0;
        border-bottom: 4px solid transparent;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    /* Effet Hover */
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-bottom-color: var(--cube-red);
    }

    /* Icône dans la carte */
    .card-icon {
        width: 70px;
        height: 70px;
        background-color: var(--cube-light-grey);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: background-color 0.3s ease;
    }

    .action-card:hover .card-icon {
        background-color: var(--cube-red);
    }

    .card-icon svg {
        width: 35px;
        height: 35px;
        stroke: var(--cube-black);
        stroke-width: 2;
        transition: stroke 0.3s ease;
    }

    .action-card:hover .card-icon svg {
        stroke: var(--cube-white);
    }

    /* Textes de la carte */
    .card-title {
        font-size: 1.15rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .card-desc {
        font-size: 0.85rem;
        color: #888;
        line-height: 1.4;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .actions-grid {
            flex-direction: column;
            align-items: center;
        }
        .action-card {
            width: 100%;
            max-width: 350px;
        }
    }
</style>

