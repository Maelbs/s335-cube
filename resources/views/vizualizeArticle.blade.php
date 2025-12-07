<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Méta nécessaire pour sécuriser les requêtes AJAX Laravel --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $article->nom_article }}</title>

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vizualizeArticle.css') }}">
</head>

<body>
    @include('layouts.header')

    {{-- STRUCTURE MODALE PANIER --}}
    <div id="cartModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModalAndRefresh()">×</button>
            <div class="modal-header">PRODUIT AJOUTÉ AU PANIER AVEC SUCCÈS</div>
            <div class="modal-body">
                {{-- Partie Gauche --}}
                <div class="modal-product">
                    <div class="modal-img">
                        <div id="modalImg">
                            @if ($isAccessoire)
                                <img src="{{ asset('images/ACCESSOIRES/' . substr($article->reference, 0, 5) . '/image_1.jpg') }}"
                                    alt="{{ $article->nom_article }}">
                            @else
                                <img src="{{ asset('images/VELOS/' . substr($article->reference, 0, 6) . '/image_1.jpg') }}"
                                    alt="{{ $article->nom_article }}">
                            @endif
                        </div>
                    </div>
                    <div class="modal-details">
                        <h3 id="modalName">NOM DE L'ARTICLE</h3>
                        <div class="modal-price" id="modalPrice">0,00 € TTC</div>
                        <div class="modal-meta">TAILLE : <span id="modalSize"
                                style="font-weight:bold; color:black;"></span></div>
                        <div class="modal-meta">QUANTITÉ : <span id="modalQty"
                                style="font-weight:bold; color:black;"></span></div>
                    </div>
                </div>
                {{-- Partie Droite (Résumé) --}}
                <div class="modal-summary">
                    <div style="margin-bottom: 15px; font-size: 0.9rem; color:#555;">
                        Il y a <span id="cartCount" style="font-weight:bold;"></span> articles dans votre panier.
                    </div>
                    <div class="summary-line"><span>Sous-total :</span><span id="cartSubtotal"
                            style="font-weight: bold;">0,00 €</span></div>
                    <div class="summary-line"><span>Transport :</span><span>Gratuit</span></div>
                    <div class="summary-total"><span>Total TTC</span><span id="cartTotal">0,00 €</span></div>
                    <div class="tax-info">Taxes incluses : <span id="cartTax">0,00 €</span></div>
                    <div class="modal-actions">
                        <button onclick="closeModalAndRefresh()" class="btn-continue">◀ Continuer mes achats</button>
                        <a href="{{ route('cart.index') }}" class="btn-checkout">Commander ▶</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-product-container">

        <div class="left-column-wrapper">

            {{-- 1. HERO IMAGE (Carrousel Dynamique Local) --}}
            <div class="product-hero-section" id="mainCarousel">

                {{-- NOUVEAU : BOUTON LOUPE --}}
                <button class="zoom-trigger-btn" onclick="openZoom()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="11" y1="8" x2="11" y2="14"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                </button>

                {{-- LOGIQUE PHP : Scan du dossier --}}
                @php
                    $prefixLength = $isAccessoire ? 5 : 6;
                    $folderName = substr($article->reference, 0, $prefixLength);

                    $dossierRacine = $isAccessoire ? 'images/ACCESSOIRES/' : 'images/VELOS/';

                    $webPath = $dossierRacine . $folderName;
                    $serverPath = public_path($webPath);

                    $validImages = [];

                    for ($i = 1; $i <= 10; $i++) {
                        if (file_exists($serverPath . '/image_' . $i . '.jpg')) {
                            $validImages[] = $webPath . '/image_' . $i . '.jpg';
                        } else {
                            break;
                        }
                    }
                @endphp

                <div class="carousel-track-container">
                    <ul class="carousel-track">
                        @if(count($validImages) > 0)
                            @foreach($validImages as $index => $imageUrl)
                                <li class="carousel-slide {{ $index === 0 ? 'current-slide' : '' }}">
                                    <img src="{{ asset($imageUrl) }}" alt="{{ $article->nom_article }}">
                                </li>
                            @endforeach
                        @else
                            <li class="carousel-slide current-slide">
                                <img src="https://placehold.co/800x500?text=Image+Non+Disponible" alt="Pas d'image">
                            </li>
                        @endif
                    </ul>

                    <button id="open-3d-btn" class="btn-3d-view" style="display: none;"
                        data-folder="{{ asset('images/MODELE3D/' . trim($article->reference)) }}/"
                        title="Ouvrir la vue 3D" aria-label="Ouvrir la vue 3D">
                        <svg class="icon-3d" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 22q-2.075 0-3.9-.788t-3.175-2.137q-1.35-1.35-2.137-3.175T2 12h2q0 2.875 1.813 5.075t4.637 2.775L9 18.4l1.4-1.4l4.55 4.55q-.725.25-1.463.35T12 22Zm.5-7V9h3q.425 0 .713.288T16.5 10v4q0 .425-.288.713T15.5 15h-3Zm-5 0v-1.5H10v-1H8.5v-1H10v-1H7.5V9h3q.425 0 .713.288T11.5 10v4q0 .425-.288.713T10.5 15h-3Zm6.5-1.5h1v-3h-1v3Zm6-1.5q0-2.875-1.813-5.075T13.55 4.15L15 5.6L13.6 7L9.05 2.45q.725-.25 1.463-.35T12 2q2.075 0 3.9.788t3.175 2.137q1.35 1.35 2.138 3.175T22 12h-2Z" />
                        </svg>
                    </button>
                </div>

                <div id="lightbox-3d" class="lightbox-overlay">
                    <div class="lightbox-content">
                        <button type="button" id="close-3d-btn" class="lightbox-close">&times;</button>

                        <div id="container-3d-viewer">

                            {{-- NOUVEAU LOADER CENTRÉ --}}
                            <div id="loader-wrapper" class="loader-wrapper">
                                <div class="spinner"></div>
                                <div id="loader-text" class="loading-text">Chargement 0%</div>
                            </div>

                            {{-- ZONE DE VISUALISATION --}}
                            <div id="product-viewer" style="width: 100%; height: 100%; cursor: grab;">
                                <img id="bike-image" src="" alt="Vue 360">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- NAVIGATION --}}
                @if(count($validImages) > 1)
                    <button class="carousel-button carousel-button--left">❮</button>
                    <button class="carousel-button carousel-button--right">❯</button>

                    <div class="carousel-nav">
                        @foreach($validImages as $index => $unused)
                            <button class="carousel-indicator {{ $index === 0 ? 'current-slide' : '' }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="specs-column">

                {{-- 2. FICHE TECHNIQUE --}}
                <div class="each-specs-column">
                    <div class="specs-header-row">
                        <h2>FICHE TECHNIQUE</h2>
                        <button class="toggle-specs-btn"></button>
                    </div>

                    <div class="specs-content">
                        @foreach($specifications as $typeNom => $caracteristiques)
                            <div class="spec-group-title">{{ $typeNom }}</div>
                            <div class="spec-group-list">
                                @if($loop->first && $typeVue === 'velo')
                                    <div class="spec-row">
                                        <div class="spec-label">Tailles</div>
                                        <div class="spec-value">
                                            @foreach($stock->sortBy('id_taille') as $inventaire)
                                                {{ $inventaire->taille->taille }}
                                                @if(!$loop->last) / @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @foreach($caracteristiques as $carac)
                                    <div class="spec-row">
                                        <div class="spec-label">{{ $carac->nom_caracteristique }}</div>
                                        <div class="spec-value">{{ $carac->pivot->valeur_caracteristique }}</div>
                                    </div>
                                @endforeach
                                @if($isAccessoire)
                                    <div class="spec-row">
                                        <div class="spec-label">Disponibilité</div>
                                        @php
                                            $stockWeb = $stock->sum('quantite_stock_en_ligne') > 0;
                                            $stockMag = $stock->flatMap->magasins->sum('pivot.quantite_stock_magasin') > 0;
                                        @endphp
                                        <div class="spec-value">
                                            @if($stockWeb)
                                                Disponible en ligne
                                            @endif

                                            @if($stockMag)
                                                / Commandable en magasin
                                            @endif
                                            @if(!$stockWeb && !$stockMag)
                                                En rupture de stock
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. GÉOMÉTRIE --}}
                @if($typeVue === 'velo' && $tailleGeometrie)
                    <div class="geo-section each-specs-column">
                        <div class="specs-header-row">
                            <h2>GÉOMÉTRIE</h2>
                            <button class="toggle-specs-btn"></button>
                        </div>

                        <div class="table-responsive">
                            <table class="geo-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        @foreach ($tailleGeometrie->sortBy('id_taille') as $taille)
                                            <th>
                                                {{ $taille->taille }}
                                                <span style="font-size: 0.9em; font-weight: 600;">
                                                    ({{ $taille->taille_min }}-{{ $taille->taille_max }})
                                                </span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $geometrieNonTriee = $article->varianteVelo->modele->geometries;
                                        $geometrieTRiee = $geometrieNonTriee->groupBy('nom_geometrie');
                                    @endphp
                                    @foreach ($geometrieTRiee as $nomGeometrie => $listeDeValeurs)
                                        <tr>
                                            <td class="nom-geometrie">{{ $nomGeometrie }}</td>
                                            @foreach ($tailleGeometrie as $taille)
                                                @php
                                                    $geoMatch = $listeDeValeurs->first(function ($geo) use ($taille) {
                                                        return $geo->pivot->id_taille == $taille->id_taille;
                                                    });
                                                @endphp
                                                <td>{{ $geoMatch ? $geoMatch->pivot->valeur_geometrie : '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- 4. DESCRIPTION & RESUME --}}
                @if($typeVue === 'velo')
                    <div class="each-specs-column">
                        <div class="specs-header-row">
                            <h2>Description</h2>
                            <button class="toggle-specs-btn"></button>
                        </div>
                        <p>{{ $article->varianteVelo->modele->description->texte_description ?? 'Aucune description disponible.' }}
                        </p>
                    </div>
                @endif

                <div class="each-specs-column" id="resume_container">
                    <div class="specs-header-row">
                        <h2>Résumé</h2>
                        <button class="toggle-specs-btn"></button>
                    </div>
                    <p>{{ $article->resume->contenu_resume }}</p>
                </div>

            </div>
        </div>

        {{-- COLONNE DROITE (SIDEBAR) --}}
        <div class="sidebar-column">

            <div class="badges">
                @if (!$isAccessoire)
                    @if (intval($article->varianteVelo->modele->millesime_modele) >= date("Y"))
                        <span class="badge-season" id="badge-new">Nouveau</span>
                    @endif
                @endif
                @if($typeVue === 'velo')
                    <span class="badge-season">Saison : {{ $article->varianteVelo->modele->millesime_modele }}</span>
                @endif
            </div>

            <h1 class="product-title">{{ $article->nom_article }}</h1>

            <div class="product-ref">
                Référence : {{ $article->reference}}
                @if($article->poids)| Poids : {{ $article->poids }} kg @endif
                @if($typeVue === 'velo')
                    | Matériau : {{ $article->varianteVelo->modele->materiau_cadre }}
                @endif
            </div>

            {{-- LOGIQUE STOCK & PRIX --}}
            @if($typeVue === 'velo')
                <div class="size-selector">
                    <p class="size-label">TAILLE</p>
                    <div class="sizes-grid">
                        @php $stockWebVelo = 0 @endphp
                        @foreach ($stock as $inventaire)
                            @php
                                $stockWeb = $inventaire->quantite_stock_en_ligne;
                                $stockMag = $inventaire->magasins->sum('pivot.quantite_stock_magasin');
                                $classeCss = ($stockWeb <= 0) ? 'out-of-stock' : '';
                                $stockWebVelo += $stockWeb;
                            @endphp
                            <button class="size-btn {{ $classeCss }}" onclick="selectionnerTaille(
                                        '{{ $inventaire->taille->taille }}', 
                                        {{ $stockWeb }}, 
                                        {{ $stockMag }}
                                                                                                                        )">
                                {{ $inventaire->taille->taille }}
                                <span style="font-size: 0.8em; display:block; font-weight: normal;">
                                    ({{ $inventaire->taille->taille_min }}-{{ $inventaire->taille->taille_max }})
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="price-section">
                    <span class="price">{{ number_format($article->varianteVelo->prix, 2, ',', ' ') }} € TTC</span>
                </div>

                <div class="dispo-info-container" style="margin-bottom: 15px;">
                    <div class="dispo-row">
                        <span id="dot-web" class="status-dot"></span>
                        <span id="text-web" class="dispo-text">Sélectionnez une taille</span>
                    </div>
                    <div class="dispo-row">
                        <span id="dot-magasin" class="status-dot"></span>
                        <span id="text-magasin" class="dispo-text">Sélectionnez une taille</span>
                    </div>
                </div>

            @else
                {{-- CAS ACCESSOIRE --}}
                <div style="margin-top: 20px; margin-bottom: 20px;">
                    @php
                        $stockWeb = $stock->sum('quantite_stock_en_ligne') > 0;
                        $stockMag = $stock->flatMap->magasins->sum('pivot.quantite_stock_magasin') > 0;
                        $colorDotWeb = $stockWeb ? '#28a745' : '#dc3545';
                        $colorDotMag = $stockMag ? '#28a745' : '#dc3545';
                        $enLigneStatus = $stockWeb ? 'Disponible en ligne' : 'Indisponible en ligne';
                        $enMagasinStatus = $stockMag ? 'Commandable en magasin' : 'Indisponible en magasin';
                    @endphp

                    <div class="dispo-info-container">
                        <div class="dispo-row">
                            <span class="status-dot" style="background-color: {{ $colorDotWeb }};"></span>
                            <span class="dispo-text" style="font-weight: bold;">{{ $enLigneStatus }}</span>
                        </div>
                        <div class="dispo-row">
                            <span class="status-dot" style="background-color: {{ $colorDotMag }};"></span>
                            <span class="dispo-text" style="font-weight: bold;">{{ $enMagasinStatus }}</span>
                        </div>
                    </div>
                </div>

                <div class="price-section">
                    <span class="price">{{ number_format($article->prix ?? 0, 2, ',', ' ') }} € TTC</span>
                </div>
            @endif


            <div class="action-buttons-container">
                <form id="form-ajout-panier"
                    data-action="{{ $isAccessoire ? route('cart.addAccessoire', $article->reference) : route('cart.add', $article->reference) }}">
                    @if($typeVue === 'velo')
                        <input type="hidden" name="taille" id="input-taille-selected" value="">
                    @else
                        <input type="hidden" name="taille" id="input-taille-selected" value="Unique">
                    @endif
                    <input type="hidden" name="quantity" value="1">
                    @if($typeVue === "accessoire")
                        @if($stockMag || $stockWeb)
                            <button type="button" onclick="addToCartAjax()" id="btn-panier" class="btn-skew ">
                                <span class="btn-content-unskew">
                                    <span class="text-label">Ajouter au panier</span>
                                </span>
                            </button>
                        @else
                            <p>Cet accessoire est en rupture de stock</p>
                        @endif
                    @else
                        @if($stockWebVelo)
                            <button type="button" onclick="addToCartAjax()" id="btn-panier" class="btn-skew"
                                style="display: none;">
                                <span class="btn-content-unskew">
                                    <span class="text-label">Ajouter au panier</span>
                                </span>
                            </button>
                        @endif
                    @endif
                </form>

                <button id="btn-contact-magasin" class="btn-skew" style="display: none;">
                    <span class="btn-content-unskew">
                        <span class="text-label">Contacter mon magasin</span>
                    </span>
                </button>

                <p id="msg-indisponible" style="display: none; color: red; font-weight: bold; margin-top:10px;">
                    Indisponible
                </p>
            </div>

            @if($typeVue === 'velo')
                <div style="margin-top: 20px;">
                    <p class="size-label">Également disponible en</p>
                    <div class="flex gap-2">
                        @php
                            $varianteActuelle = $article->varianteVelo;
                            $tousLesCousins = $varianteActuelle->modele->varianteVelos ?? collect([]);
                            $nomRef = trim($varianteActuelle->nom_article);
                            $variantesFiltrees = $tousLesCousins->filter(function ($item) use ($nomRef) {
                                return trim($item->nom_article) === $nomRef;
                            });
                            $couleursUniques = $variantesFiltrees->unique('id_couleur');
                        @endphp

                        @foreach ($couleursUniques as $variante)
                            @php
                                $estActif = ($variante->id_couleur == $varianteActuelle->id_couleur);
                                $lien = route('velo.show', ['reference' => $variante->reference]);
                                $hex = $variante->couleur->hexa_couleur ?? '000000';
                                $bg = str_starts_with($hex, '#') ? $hex : '#' . $hex;
                            @endphp
                            <a href="{{ $lien }}" class="couleur-velo"
                                style="display: inline-block; width: 30px; height: 30px; border-radius: 50%; margin-right: 5px;
                                                                                                                       border: {{ $estActif ? '5px solid #cbcbcb' : '1px solid #ddd' }};
                                                                                                                       background-color: {{ $bg }};">
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($article->varianteVelo->id_batterie)
                    <div style="margin-top: 15px;">
                        <p class="size-label">Batterie :</p>
                        <div class="flex gap-2">
                            @php
                                $batteriesUniques = $variantesFiltrees
                                    ->whereNotNull('id_batterie')
                                    ->unique('id_batterie')
                                    ->sortBy(function ($v) {
                                        return $v->batterie->puissance ?? $v->batterie->nom ?? 0;
                                    });
                            @endphp
                            @foreach ($batteriesUniques as $variante)
                                @php
                                    $estActif = ($variante->id_batterie == $varianteActuelle->id_batterie);
                                    $lien = route('velo.show', ['reference' => $variante->reference]);
                                    $nomBatterie = $variante->batterie->puissance ?? $variante->batterie->capacite_batterie ?? 'Batterie';
                                    if (is_numeric($nomBatterie)) {
                                        $nomBatterie .= ' Wh';
                                    }
                                @endphp
                                <a href="{{ $lien }}" class="choix-batterie {{ $estActif ? 'active' : '' }}">
                                    {{ $nomBatterie }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>

    {{-- Accessoire Vélos --}}
    @if(!$isAccessoire)
        <section class="st-similar-section section-grey">

            {{-- En-tête avec ton texte personnalisé --}}
            <div class="st-section-header" style="text-align: center; margin-bottom: 20px;">
                <h2 class="st-section-title text-section-grey">D’autres cyclistes ont également acheté</h2>
                <p class="text-section-grey">
                    Complétez votre équipement avec des accessoires populaires auprès de nos clients.
                    Chaque produit est sélectionné pour améliorer votre expérience de cycliste et s’adapter parfaitement à
                    votre vélo Cube.
                </p>
            </div>

            {{-- Wrapper du Carousel (Mêmes classes pour garder le CSS) --}}
            <div class="st-carousel-wrapper">
                <button class="st-nav-btn st-btn-left">❮</button>

                <div class="st-carousel-track">
                    @foreach ($article->varianteVelo->accessoires as $accessoire)
                        <div class="st-card-item">
                            {{-- Lien vers le produit (Vérifie si tu dois utiliser velo.show ou accessoire.show) --}}
                            <a href="{{ route('velo.show', $accessoire->reference) }}" class="st-card-link">

                                <div class="st-img-box">
                                    {{-- LOGIQUE IMAGE LOCALE ADAPTÉE POUR ACCESSOIRE --}}
                                    @php
                                        $prefix = 5;
                                        $folder = substr($accessoire->reference, 0, $prefix);
                                        $imgPath = 'images/ACCESSOIRES/' . $folder . '/image_1.jpg'; 
                                    @endphp

                                    @if(file_exists(public_path($imgPath)))
                                        <img src="{{ asset($imgPath) }}" alt="{{ $accessoire->nom_article }}">
                                    @else
                                        <img src="https://placehold.co/300x200?text=Pas+d+image"
                                            alt="{{ $accessoire->nom_article }}">
                                    @endif
                                </div>

                                <div class="st-info-box">
                                    <h3 class="st-prod-name">{{ $accessoire->nom_article }}</h3>
                                    <div class="st-prod-price">
                                        {{ number_format($accessoire->prix, 2, ',', ' ') }} €
                                    </div>
                                </div>

                                <div class="st-action-row">
                                    <span class="st-view-btn"><i class="arrow-icon">▶</i> VOIR LE PRODUIT</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <button class="st-nav-btn st-btn-right">❯</button>
            </div>
        </section>
    @endif

    {{-- Produits Similaires AVEC IMAGES LOCALES --}}
    @if($articlesSimilaires->isNotEmpty())
        <section class="st-similar-section">
            <h2 class="st-section-title">ARTICLES SIMILAIRES</h2>
            <div class="st-carousel-wrapper">
                <button class="st-nav-btn st-btn-left">❮</button>
                <div class="st-carousel-track">
                    @foreach($articlesSimilaires as $similaire)
                        <div class="st-card-item">
                            <a href="{{ route('velo.show', $similaire->reference) }}" class="st-card-link">
                                <div class="st-img-box">
                                    {{-- LOGIQUE POUR TROUVER L'IMAGE LOCALE (CORRIGÉE) --}}
                                    @php
                                        $simPrefix = $isAccessoire ? 5 : 6;
                                        $simFolder = substr($similaire->reference, 0, $simPrefix);
                                        $dossierRacineSim = $isAccessoire ? 'images/ACCESSOIRES/' : 'images/VELOS/';
                                        $simImgPath = $dossierRacineSim . $simFolder . '/image_1.jpg';
                                    @endphp

                                    @if(file_exists(public_path($simImgPath)))
                                        <img src="{{ asset($simImgPath) }}" alt="{{ $similaire->nom_article }}">
                                    @else
                                        {{-- Placeholder si pas d'image --}}
                                        <img src="https://placehold.co/300x200?text=Pas+d+image"
                                            alt="{{ $similaire->nom_article }}">
                                    @endif
                                </div>
                                <div class="st-info-box">
                                    <h3 class="st-prod-name">{{ $similaire->nom_article }}</h3>
                                    <div class="st-prod-price">{{ number_format($similaire->prix, 2, ',', ' ') }} €</div>
                                </div>
                                <div class="st-action-row">
                                    <span class="st-view-btn"><i class="arrow-icon">▶</i> VOIR LE PRODUIT</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <button class="st-nav-btn st-btn-right">❯</button>
            </div>
        </section>
    @endif

    {{-- MODALE ZOOM PLEIN ÉCRAN (AVEC NAVIGATION) --}}
    <div id="zoomModalOverlay" class="zoom-overlay">
        <button class="zoom-close-btn" onclick="closeZoom(event)">×</button>

        {{-- NOUVEAU : Boutons Précédent / Suivant --}}
        <button class="zoom-nav zoom-prev" onclick="changeZoomImage(-1)">❮</button>
        <button class="zoom-nav zoom-next" onclick="changeZoomImage(1)">❯</button>

        <div class="zoom-container" onclick="toggleZoomState(event)">
            <img id="zoomImageFull" src="" alt="Zoom Produit">
        </div>
    </div>

    {{-- SCRIPTS JS --}}
    <script src="{{ asset('js/vizualizeArticle.js') }}" defer></script>

    <script>
        function closeModalAndRefresh() {
            const modal = document.getElementById('cartModal');
            if (modal) {
                modal.style.display = 'none';
            }

            location.reload();
        }
    </script>
</body>

</html>