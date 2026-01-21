
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="catalog-container">
        
        <div style="margin-bottom: 20px;">
            <a href="{{ route('commercial.dashboard') }}" style="text-decoration: none; color: #666; font-weight: bold; display: flex; align-items: center; gap: 5px;">
                <span>←</span> Retour au Dashboard
            </a>
        </div>

        <div class="header-section">
            <h1 class="page-title">Gestion images du Catalogue</h1>
            <p style="color: #666; margin-top: 5px;">Ajouter Images.</p>
        </div>

        @if(session('success'))
            <div class="alert-box alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-box alert-error">{{ session('error') }}</div>
        @endif

        <div class="tabs-header">
            <button class="tab-btn active" onclick="openTab(event, 'tab-velos')">Vélos Musculaires ({{ count($velosMusculaires) }})</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-vae')">Vélos Électriques ({{ count($velosElectriques) }})</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-accessoires')">Accessoires ({{ count($accessoires) }})</button>
        </div>

        <div id="tab-velos" class="tab-content active">
            <div class="products-grid">
                @forelse($velosMusculaires as $article)
                    {{-- ON AJOUTE 'dossier' => 'VELOS' --}}
                    @include('commercial.layoutsCommercial.cardsImage', ['article' => $article, 'dossier' => 'VELOS'])
                @empty
                    @endforelse
            </div>
        </div>

        <div id="tab-vae" class="tab-content">
            <div class="products-grid">
                @forelse($velosElectriques as $article)
                    {{-- ON AJOUTE 'dossier' => 'VELOS' --}}
                    @include('commercial.layoutsCommercial.cardsImage', ['article' => $article, 'dossier' => 'VELOS'])
                @empty
                    @endforelse
            </div>
        </div>

        <div id="tab-accessoires" class="tab-content">
            <div class="products-grid">
                @forelse($accessoires as $article)
                    {{-- ON AJOUTE 'dossier' => 'ACCESSOIRES' --}}
                    @include('commercial.layoutsCommercial.cardsImage', ['article' => $article, 'dossier' => 'ACCESSOIRES'])
                @empty
                    @endforelse
            </div>
        </div>

    </div>
</body>
</html>

<style>
    /* Variables CUBE */
    :root {
        --cube-red: #e30613;
        --cube-black: #1a1a1a;
        --cube-grey: #f4f4f4;
    }

    .catalog-container {
        max-width: 1300px;
        margin: 40px auto;
        padding: 0 20px;
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    .header-section {
        margin-bottom: 40px;
        text-align: center;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 900;
        font-style: italic;
        text-transform: uppercase;
        margin: 0;
    }

    /* --- ONGLETS (TABS) --- */
    .tabs-header {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid #ddd;
    }

    .tab-btn {
        padding: 15px 30px;
        font-size: 1rem;
        font-weight: 800;
        text-transform: uppercase;
        background: none;
        border: none;
        cursor: pointer;
        color: #888;
        border-bottom: 4px solid transparent;
        transition: all 0.3s;
    }

    .tab-btn:hover {
        color: var(--cube-black);
    }

    .tab-btn.active {
        color: var(--cube-black);
        border-bottom-color: var(--cube-red);
    }

    /* Contenu des onglets */
    .tab-content {
        display: none;
        animation: fadeIn 0.4s ease-in-out;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- GRILLE --- */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }
    
    /* Messages */
    .alert-box {
        padding: 15px;
        margin-bottom: 20px;
        border-left: 5px solid;
    }
    .alert-success { background: #d4edda; color: #155724; border-color: #28a745; }
    .alert-error { background: #f8d7da; color: #721c24; border-color: #dc3545; }

</style>



<script>
    function openTab(evt, tabName) {
        // Cacher tous les onglets
        const contents = document.querySelectorAll(".tab-content");
        contents.forEach(content => content.classList.remove("active"));

        // Désactiver tous les boutons
        const btns = document.querySelectorAll(".tab-btn");
        btns.forEach(btn => btn.classList.remove("active"));

        // Activer la cible
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
</script>