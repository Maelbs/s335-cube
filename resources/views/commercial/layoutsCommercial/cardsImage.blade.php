<style>
    /* Style local pour la carte */
    .product-card {
        border: 1px solid #e0e0e0;
        background: white;
        padding: 20px;
        text-align: center;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; /* Uniformise la hauteur */
    }

    .product-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transform: translateY(-5px);
        border-color: #ccc;
    }

    .product-img {
        width: 100%;
        height: 160px;
        object-fit: contain;
        margin-bottom: 15px;
    }

    .product-info {
        margin-bottom: 15px;
    }

    .product-ref {
        font-size: 0.75rem;
        color: #888;
        font-family: monospace;
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 5px;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #1a1a1a;
        margin: 5px 0;
        line-height: 1.3;
        /* Limite à 2 lignes */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: #1a1a1a;
    }

    /* Actions */
    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: auto; /* Pousse les boutons vers le bas */
    }

    .btn-action {
        flex: 1;
        padding: 10px 0;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
        border: 1px solid;
    }

    .btn-edit {
        background-color: #1a1a1a;
        color: white;
        border-color: #1a1a1a;
    }
    .btn-edit:hover {
        background-color: #333;
    }

    .btn-delete {
        background-color: white;
        color: #e30613;
        border-color: #e30613;
    }
    .btn-delete:hover {
        background-color: #e30613;
        color: white;
    }
</style>

<div class="product-card">
    @php
        // 1. Nettoyage de la référence (car CHAR(10) en base de données garde les espaces)
        $refClean = trim($article->reference);

        // 2. Construction du chemin relatif
        // On suppose que l'image s'appelle toujours 'image_1.webp'
        $cheminRelatif = 'images/' . $dossier . '/' . $refClean . '/image_1.webp';

        // 3. Vérification physique du fichier
        // public_path() donne le chemin absolu sur le serveur (C:/wamp/www/...)
        if (file_exists(public_path($cheminRelatif))) {
            $urlImage = asset($cheminRelatif);
        } else {
            // Image par défaut si le fichier n'existe pas
            $urlImage = 'https://placehold.co/300x200?text=Pas+d+image';
        }
    @endphp
    
    <img src="{{ $urlImage }}" alt="{{ $article->nom_article }}" class="product-img">
    
    <div class="product-info">
        <div class="product-ref">REF: {{ trim($article->reference) }}</div>
        <h3 class="product-name">{{ $article->nom_article }}</h3>
        <div class="product-price">{{ number_format($article->prix, 2, ',', ' ') }} €</div>
    </div>

    <div class="card-actions">
        <a href="{{ route('commercial.add.imageModele', ['reference' => $article->reference]) }}" class="btn-action btn-edit">
            Ajouter des photos
        </a>
    </div>
</div>