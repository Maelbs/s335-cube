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
    <link rel="stylesheet" href="{{ asset('css/vizualize_article.css') }}">
</head>

<body>
    @include('layouts.header')

    {{-- STRUCTURE MODALE PANIER --}}
    <div id="cartModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">×</button>
            <div class="modal-header">PRODUIT AJOUTÉ AU PANIER AVEC SUCCÈS</div>
            <div class="modal-body">
                {{-- Partie Gauche --}}
                <div class="modal-product">
                    <div class="modal-img"><img id="modalImg" src="" alt="Article"></div>
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
                        <button onclick="closeModal()" class="btn-continue">◀ Continuer mes achats</button>
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

                {{-- LOGIQUE PHP : Scan du dossier --}}
                @php
                    $prefixLength = $isAccessoire ? 5 : 6;
                    $folderName = substr($article->reference, 0, $prefixLength);

                    // Chemin Web (src) et Serveur (check file)
                    $webPath = 'images/VELOS/' . $folderName;
                    $serverPath = public_path($webPath);

                    $validImages = [];

                    // On cherche de image_1.jpg à image_10.jpg
                    for ($i = 1; $i <= 10; $i++) {
                        if (file_exists($serverPath . '/image_' . $i . '.jpg')) {
                            $validImages[] = $webPath . '/image_' . $i . '.jpg';
                        } else {
                            break; // On arrête dès qu'une image manque
                        }
                    }
                @endphp

                <div class="carousel-track-container">
                    <ul class="carousel-track">
                        @if(count($validImages) > 0)
                            {{-- On boucle sur les images trouvées --}}
                            @foreach($validImages as $index => $imageUrl)
                                <li class="carousel-slide {{ $index === 0 ? 'current-slide' : '' }}">
                                    <img src="{{ asset($imageUrl) }}" alt="{{ $article->nom_article }}">
                                </li>
                            @endforeach
                        @else
                            {{-- Aucune image trouvée --}}
                            <li class="carousel-slide current-slide">
                                <img src="https://placehold.co/800x500?text=Image+Non+Disponible" alt="Pas d'image">
                            </li>
                        @endif
                    </ul>
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
                                        <div class="spec-label">Tailles disponibles</div>
                                        <div class="spec-value">Disponible en magasin | Disponible en ligne</div>
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
                    @if ($article->varianteVelo->modele->millesime_modele >= date("Y"))
                        <div class="badge-season newMillesime">
                            <span>Nouveau</span>
                        </div>
                    @endif
                @endif
                @if($typeVue === 'velo')
                    <span class="badge-season">Saison : {{ $article->varianteVelo->modele->millesime_modele }}</span>
                @endif
            </div>

            <h1 class="product-title">{{ $article->nom_article }}</h1>
            <div class="product-ref">
                @php
                    $poids = $article->poids;
                    $unitePoids = 'kg';
                    if ($poids < 1) {
                        $poids *= 1000;
                        $unitePoids = 'g';

                    }
                @endphp
                Référence : {{ $article->reference}} | Poids : {{ $poids }} {{ $unitePoids }}
                @if($typeVue === 'velo')
                    | Matériau : {{ $article->varianteVelo->modele->materiau_cadre }}
                @endif
            </div>

            {{-- LOGIQUE STOCK & PRIX --}}
            @if($typeVue === 'velo')
                <div class="size-selector">
                    <p class="size-label">TAILLE</p>
                    <div class="sizes-grid">
                        @foreach ($stock as $inventaire)
                            @php
                                $stockWeb = $inventaire->quantite_stock_en_ligne;
                                $stockMag = $inventaire->magasins->sum('pivot.quantite_stock_magasin');
                                $classeCss = ($stockWeb <= 0) ? 'out-of-stock' : '';
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
                <form id="form-ajout-panier" data-action="{{ route('cart.add', $article->reference) }}">
                    @if($typeVue === 'velo')
                        <input type="hidden" name="taille" id="input-taille-selected" value="">
                    @else
                        <input type="hidden" name="taille" id="input-taille-selected" value="Unique">
                    @endif
                    <input type="hidden" name="quantity" value="1">

                    <button type="button" onclick="addToCartAjax()" id="btn-panier" class="btn-skew">
                        <span class="btn-content-unskew">
                            <span class="text-label">Ajouter au panier</span>
                        </span>
                    </button>
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
                            <a href="{{ $lien }}" class="couleur-velo" style="display: inline-block; width: 30px; height: 30px; border-radius: 50%; margin-right: 5px;
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
    @if(! $isAccessoire)
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
                                        // On suppose que pour les accessoires le préfixe est de 5 (selon ta logique précédente)
                                        $prefix = 5;
                                        $folder = substr($accessoire->reference, 0, $prefix);

                                        // ATTENTION: Vérifie si tes accessoires sont dans 'images/VELOS' ou 'images/ACCESSOIRES'
                                        $imgPath = 'images/VELOS/' . $folder . '/image_1.jpg'; 
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
                                    {{-- LOGIQUE POUR TROUVER L'IMAGE LOCALE --}}
                                    @php
                                        $simPrefix = $isAccessoire ? 5 : 6;
                                        $simFolder = substr($similaire->reference, 0, $simPrefix);
                                        $simImgPath = 'images/VELOS/' . $simFolder . '/image_1.jpg';
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

    {{-- SCRIPTS JS --}}
    <script src="{{ asset('js/vizualize_article.js') }}" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.querySelector('.carousel-track');
            if (!track || track.children.length <= 1) return;

            const slides = Array.from(track.children);
            const nextButton = document.querySelector('.carousel-button--right');
            const prevButton = document.querySelector('.carousel-button--left');
            const dotsNav = document.querySelector('.carousel-nav');
            const dots = dotsNav ? Array.from(dotsNav.children) : [];

            const slideWidth = slides[0].getBoundingClientRect().width;

            const setSlidePosition = (slide, index) => {
                slide.style.left = slideWidth * index + 'px';
            };
            slides.forEach(setSlidePosition);

            const moveToSlide = (track, currentSlide, targetSlide) => {
                track.style.transform = 'translateX(-' + targetSlide.style.left + ')';
                currentSlide.classList.remove('current-slide');
                targetSlide.classList.add('current-slide');
            }

            const updateDots = (currentDot, targetDot) => {
                if (currentDot && targetDot) {
                    currentDot.classList.remove('current-slide');
                    targetDot.classList.add('current-slide');
                }
            }

            if (nextButton) {
                nextButton.addEventListener('click', e => {
                    const currentSlide = track.querySelector('.current-slide');
                    let nextSlide = currentSlide.nextElementSibling;
                    const currentDot = dotsNav ? dotsNav.querySelector('.current-slide') : null;
                    let nextDot = currentDot ? currentDot.nextElementSibling : null;

                    if (!nextSlide) {
                        nextSlide = slides[0];
                        if (dots.length) nextDot = dots[0];
                    }

                    moveToSlide(track, currentSlide, nextSlide);
                    updateDots(currentDot, nextDot);
                });
            }

            if (prevButton) {
                prevButton.addEventListener('click', e => {
                    const currentSlide = track.querySelector('.current-slide');
                    let prevSlide = currentSlide.previousElementSibling;
                    const currentDot = dotsNav ? dotsNav.querySelector('.current-slide') : null;
                    let prevDot = currentDot ? currentDot.previousElementSibling : null;

                    if (!prevSlide) {
                        prevSlide = slides[slides.length - 1];
                        if (dots.length) prevDot = dots[dots.length - 1];
                    }

                    moveToSlide(track, currentSlide, prevSlide);
                    updateDots(currentDot, prevDot);
                });
            }

            if (dotsNav) {
                dotsNav.addEventListener('click', e => {
                    const targetDot = e.target.closest('button');
                    if (!targetDot) return;

                    const currentSlide = track.querySelector('.current-slide');
                    const currentDot = dotsNav.querySelector('.current-slide');
                    const targetIndex = dots.findIndex(dot => dot === targetDot);
                    const targetSlide = slides[targetIndex];

                    moveToSlide(track, currentSlide, targetSlide);
                    updateDots(currentDot, targetDot);
                });
            }

            window.addEventListener('resize', () => {
                const newSlideWidth = slides[0].getBoundingClientRect().width;
                slides.forEach((slide, index) => {
                    slide.style.left = newSlideWidth * index + 'px';
                });
                const currentSlide = track.querySelector('.current-slide');
                track.style.transform = 'translateX(-' + currentSlide.style.left + ')';
            });
        });

        function selectionnerTaille(tailleNom, qtyWeb, qtyMagasin) {
            document.getElementById('input-taille-selected').value = tailleNom;

            const formPanier = document.getElementById('form-ajout-panier');
            const btnMagasin = document.getElementById('btn-contact-magasin');
            const msgIndispo = document.getElementById('msg-indisponible');
            const dotWeb = document.getElementById('dot-web');
            const textWeb = document.getElementById('text-web');
            const dotMagasin = document.getElementById('dot-magasin');
            const textMagasin = document.getElementById('text-magasin');

            if (qtyWeb > 0) {
                formPanier.style.display = 'inline-block';
                dotWeb.className = 'status-dot active-green';
                textWeb.textContent = "Disponible en ligne";
                textWeb.style.color = '#15803d';
            } else {
                formPanier.style.display = 'none';
                dotWeb.className = 'status-dot inactive-gray';
                textWeb.textContent = "Indisponible en ligne";
                textWeb.style.color = '#6b7280';
            }

            if (qtyMagasin > 0) {
                btnMagasin.style.display = 'inline-block';
                dotMagasin.className = 'status-dot active-green';
                textMagasin.textContent = "Disponible en magasin";
                textMagasin.style.color = '#15803d';
            } else {
                btnMagasin.style.display = 'none';
                dotMagasin.className = 'status-dot inactive-gray';
                textMagasin.textContent = "Indisponible en magasin";
                textMagasin.style.color = '#6b7280';
            }

            if (qtyWeb <= 0 && qtyMagasin <= 0) {
                msgIndispo.style.display = 'block';
            } else {
                msgIndispo.style.display = 'none';
            }
        }

        function addToCartAjax() {
            const form = document.getElementById('form-ajout-panier');
            const url = form.getAttribute('data-action');
            const taille = document.getElementById('input-taille-selected').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (!taille) {
                alert("Veuillez sélectionner une taille.");
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    taille: taille,
                    quantity: 1
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fillAndOpenModal(data);
                    } else {
                        alert("Erreur: " + (data.message || "Une erreur est survenue"));
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }

        function fillAndOpenModal(data) {
            document.getElementById('modalName').textContent = data.product.name;
            document.getElementById('modalPrice').textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data.product.price) + " TTC";
            document.getElementById('modalImg').src = data.product.image;
            document.getElementById('modalSize').textContent = data.product.taille;
            document.getElementById('modalQty').textContent = data.product.qty;

            document.getElementById('cartCount').textContent = data.cart.count;
            const formattedTotal = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data.cart.total);
            const formattedTax = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data.cart.total * 0.2);

            document.getElementById('cartSubtotal').textContent = formattedTotal;
            document.getElementById('cartTotal').textContent = formattedTotal;
            document.getElementById('cartTax').textContent = formattedTax;

            document.getElementById('cartModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('cartModal').style.display = 'none';
        }

        window.onclick = function (event) {
            const modal = document.getElementById('cartModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>