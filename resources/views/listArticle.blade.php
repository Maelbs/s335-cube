<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUBE Bikes - {{ $titrePage }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listArticle.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
    
    <script>
        function scrollToListing() {
            document.getElementById('listing-anchor').scrollIntoView({ behavior: 'smooth' });
        }
        
        /* GESTION DU TRI (Dropdown) */
        function toggleSortMenu() {
            document.getElementById("sortDropdown").classList.toggle("active");
        }
        function applySort(sortValue) {
            let currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sortValue);
            // On ajoute l'ancre pour rester sur la liste
            currentUrl.hash = "listing-anchor";
            window.location.href = currentUrl.toString();
        }
        
        /* FERMETURE MENU AU CLIC EXTERIEUR */
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-btn') && !event.target.closest('.dropdown-btn')) {
                var dropdowns = document.getElementsByClassName("custom-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('active')) dropdowns[i].classList.remove('active');
                }
            }
        }

        /* FONCTION POUR PLIER/DÉPLIER LES FILTRES */
        function toggleSection(headerElement) {
            // On remonte au parent (.filter-section) et on toggle la classe 'closed'
            headerElement.parentElement.classList.toggle('closed');
        }
    </script>
</head>
<body>
    
    @include('layouts.header') 
    

    @php
        $bgImage = ($type === 'Electrique') 
            ? asset('https://www.cubebikes.fr/img/c/4519.jpg')  // Assurez-vous d'avoir cette image
            : asset('https://www.cubebikes.fr/img/c/4518.jpg'); // Et celle-ci
    @endphp

    <section class="hero-section" style="background-image: url('{{ $bgImage }}');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">
                {{ strtoupper($titrePage) }}
            </h1>
            <button onclick="scrollToListing()" class="btn-scroll-down">
                <i class="fas fa-angle-double-right"></i> VOIR TOUS LES VÉLOS
            </button>
        </div>
    </section>
    
    <div id="listing-anchor" class="main-container">
        
        <aside class='filters-sidebar'>
            <form id="filterForm" action="{{ url()->current() }}#listing-anchor" method="GET">
                
                @if(request('search')) 
                    <input type="hidden" name="search" value="{{ request('search') }}"> 
                @endif
                
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif


                @if($hierarchyItems->isNotEmpty())
                    <div class="filter-section">
                        <div class="filter-header" onclick="toggleSection(this)">
                            <h3>{{ $hierarchyTitle }}</h3>
                            <i class="fas fa-chevron-up"></i>
                        </div>
                        <div class="filter-content">
                            
                            @if($cat_id)
                                <div style="margin-bottom: 10px; padding-bottom:10px; border-bottom:1px dashed #eee;">
                                    @php
                                        // On prépare les paramètres pour le retour en arrière
                                        // On garde tous les filtres actuels (prix, couleur...) avec request()->query()
                                        $backParams = request()->query();
                                        
                                        // On définit la cible selon le niveau actuel
                                        $backRouteParams = ['type' => $type];
                                        if ($sub_id) {
                                            $backRouteParams['cat_id'] = $cat_id;
                                        }
                                        // Fusion des paramètres d'URL (chemin) et des filtres (query)
                                        $backUrl = route('boutique.index', array_merge($backRouteParams, $backParams));
                                    @endphp

                                    <a href="{{ $backUrl }}#listing-anchor" 
                                    style="font-size:12px; font-weight:700; color:#666; text-decoration:none; display:flex; align-items:center; gap:5px;">
                                        <i class="fas fa-arrow-left"></i> RETOUR
                                    </a>
                                </div>
                            @endif

                            @foreach($hierarchyItems as $item)
                                @php
                                    // 1. On récupère tous les filtres actuels (prix, couleur, tri...)
                                    $currentFilters = request()->query();

                                    // 2. On définit les paramètres de la route (le chemin /type/cat/sub...)
                                    $routeParams = ['type' => $type];
                                    $isActive = false;

                                    if ($hierarchyLevel === 'root') {
                                        $routeParams['cat_id'] = $item->id;
                                        $isActive = ($cat_id == $item->id);
                                    } 
                                    elseif ($hierarchyLevel === 'sub') {
                                        $routeParams['cat_id'] = $cat_id;
                                        $routeParams['sub_id'] = $item->id;
                                        $isActive = ($sub_id == $item->id);
                                    } 
                                    elseif ($hierarchyLevel === 'model') {
                                        $routeParams['cat_id'] = $cat_id;
                                        $routeParams['sub_id'] = $sub_id;
                                        $routeParams['model_id'] = $item->id;
                                        $isActive = ($model_id == $item->id);
                                    }

                                    // 3. FUSION MAGIQUE : On combine le chemin + les filtres existants
                                    // Cela permet de cliquer sur "VTT" sans perdre le filtre "Prix < 2000€"
                                    $targetUrl = route('boutique.index', array_merge($routeParams, $currentFilters));
                                @endphp

                                <label class="cube-checkbox" onclick="window.location.href='{{ $targetUrl }}#listing-anchor'">
                                    <input type="checkbox" {{ $isActive ? 'checked' : '' }}>
                                    <span class="box"></span>
                                    {{ strtoupper($item->name) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                @if(request()->except(['q', 'sort'])) <a href="{{ route('boutique.index', ['type' => $type]) }}#listing-anchor" class="btn-reset-top">
                        <i class="fas fa-times"></i> RÉINITIALISER LES FILTRES
                    </a>
                @endif

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)">
                        <h3>PRIX</h3><i class="fas fa-chevron-up"></i>
                    </div>
                    
                    <div class="filter-content price-filter-container">
                        <div id="price-slider"></div>

                        <input type="hidden" name="prix_min" id="input-prix-min" value="{{ request('prix_min') ?? 0 }}">
                        <input type="hidden" name="prix_max" id="input-prix-max" value="{{ request('prix_max') ?? $maxPrice }}">
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)">
                        <h3>TAILLE</h3><i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content">
                        @foreach($availableTailles as $taille)
                            @php $tailleVal = trim($taille->taille); @endphp
                            <label class="cube-checkbox">
                                <input type="checkbox" name="tailles[]" value="{{ $tailleVal }}" class="auto-submit"
                                    {{ (is_array(request('tailles')) && in_array($tailleVal, request('tailles'))) ? 'checked' : '' }}>
                                <span class="box"></span>
                                {{ $tailleVal }}
                            </label>
                        @endforeach
                    </div>
                </div>

                @if($type === 'Electrique' && $availableBatteries->isNotEmpty())
                    <div class="filter-section">
                        <div class="filter-header" onclick="toggleSection(this)">
                            <h3>BATTERIE</h3><i class="fas fa-chevron-up"></i>
                        </div>
                        <div class="filter-content">
                            @foreach($availableBatteries as $bat)
                                <label class="cube-checkbox">
                                    <input type="checkbox" name="batteries[]" value="{{ $bat->id_batterie }}" class="auto-submit"
                                        {{ (is_array(request('batteries')) && in_array($bat->id_batterie, request('batteries'))) ? 'checked' : '' }}>
                                    <span class="box"></span>
                                    {{ $bat->capacite_batterie }} Wh
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)">
                        <h3>MILLÉSIME</h3><i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content">
                        @foreach($availableMillesimes as $annee)
                            <label class="cube-checkbox">
                                <input type="checkbox" name="millesimes[]" value="{{ $annee }}" class="auto-submit"
                                    {{ (is_array(request('millesimes')) && in_array($annee, request('millesimes'))) ? 'checked' : '' }}>
                                <span class="box"></span>
                                {{ $annee }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)">
                        <h3>MATÉRIAU DU CADRE</h3><i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content">
                        @foreach($availableMateriaux as $materiau)
                            <label class="cube-checkbox">
                                <input type="checkbox" name="materiaux[]" value="{{ $materiau }}" class="auto-submit"
                                    {{ (is_array(request('materiaux')) && in_array($materiau, request('materiaux'))) ? 'checked' : '' }}>
                                <span class="box"></span>
                                {{ ucfirst($materiau) }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)">
                        <h3>FOURCHE</h3><i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content" style="max-height: 200px; overflow-y: auto;">
                        @foreach($availableFourches as $fourche)
                            <label class="cube-checkbox">
                                <input type="checkbox" name="fourches[]" value="{{ $fourche->id_fourche }}" class="auto-submit"
                                    {{ (is_array(request('fourches')) && in_array($fourche->id_fourche, request('fourches'))) ? 'checked' : '' }}>
                                <span class="box"></span>
                                {{ Str::limit($fourche->nom_fourche, 25) }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)">
                        <h3>COULEUR</h3><i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="filter-content" style="max-height: 200px; overflow-y: auto;">
                        @foreach($availableCouleurs as $c)
                            @php $couleurVal = trim($c->nom_couleur); @endphp
                            <label class="cube-checkbox">
                                <input type="checkbox" name="couleurs[]" value="{{ $c->id_couleur }}" class="auto-submit"
                                    {{ (is_array(request('couleurs')) && in_array($c->id_couleur, request('couleurs'))) ? 'checked' : '' }}>
                                <span class="box"></span>
                                {{ ucfirst($couleurVal) }}
                            </label>
                        @endforeach
                    </div>
                </div>

            </form>
        </aside>
        
        <main class='products-grid-wrapper'>
            <div class="top-bar">
                <span>{{ $velos->total() }} PRODUITS</span>
                
                <div class="custom-dropdown" id="sortDropdown">
                    <button class="dropdown-btn" onclick="toggleSortMenu()">
                        TRIER PAR : <span id="currentSortLabel">
                            @switch(request('sort'))
                                @case('price_desc') PRIX, DÉCROISSANT @break
                                @case('name_asc') NOM, A À Z @break
                                @case('name_desc') NOM, Z À A @break
                                @case('ref_asc') REFERENCE, A TO Z @break
                                @case('ref_desc') REFERENCE, Z TO A @break
                                @default PRIX, CROISSANT
                            @endswitch
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <div class="dropdown-content" id="sortOptions">
                        <div onclick="applySort('relevance')">PERTINENCE</div>
                        <div onclick="applySort('name_asc')">NOM, A À Z</div>
                        <div onclick="applySort('name_desc')">NOM, Z À A</div>
                        <div onclick="applySort('price_asc')">PRIX, CROISSANT</div>
                        <div onclick="applySort('price_desc')">PRIX, DÉCROISSANT</div>
                        <div onclick="applySort('ref_asc')">REFERENCE, A TO Z</div>
                        <div onclick="applySort('ref_desc')">REFERENCE, Z TO A</div>
                    </div>
                </div>
            </div>

            @if($velos->isEmpty())
                <div class="empty-state" style="padding:50px; text-align:center; color:#666;">
                    <p>Aucun vélo ne correspond à vos critères.</p>
                    <a href="{{ url()->current() }}" style="text-decoration:underline; font-weight:bold;">Réinitialiser</a>
                </div>
            @else
                <div class="products-grid">
                    @foreach($velos as $velo)
                        <div class="product-card">
                            <div class="badge-new">NOUVEAU</div>
                            <div class="product-image">
                                <a href="{{ url('/velo/' . $velo->reference) }}">
                                    <img src="{{ $velo->parent->photo_principale }}" alt="{{ $velo->nom_article }}">
                                </a>
                            </div>
                            <div class="product-details">
                                <h2 class="product-title">
                                    <a href="{{ url('/velo/' . $velo->reference) }}">
                                        {{ strtoupper($velo->modele->nom_modele) }} - {{ strtoupper(str_replace($velo->modele->nom_modele, '', $velo->nom_article)) }}
                                    </a>
                                </h2>
                                <div class="availability">
                                    <div class="status-line">
                                        <span class="dot {{ $velo->dispo_en_ligne ? 'green' : 'red' }}"></span>
                                        <span class="text">Disponible en ligne</span>
                                    </div>
                                    <div class="status-line">
                                        <span class="dot {{ $velo->dispo_magasin ? 'green' : 'orange' }}"></span>
                                        <span class="text">Disponible en magasins <i class="far fa-question-circle info-icon"></i></span>
                                    </div>
                                </div>
                                <div class="product-footer">
                                    <div class="price">{{ number_format($velo->prix, 0, ',', ' ') }} €</div>
                                </div>
                            </div>
                            <a href="{{ url('/velo/' . $velo->reference) }}" class="btn-skew">
                                <span>VOIR LE PRODUIT</span> <i class="fas fa-caret-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="pagination-wrapper">
                    {{ $velos->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </main>
    </div>
    
    <footer>
        <p>&copy; 2025 CUBE Bikes France</p>
    </footer>

    <script src="{{ asset('js/header.js') }}" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const inputs = form.querySelectorAll('.auto-submit');

            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Petit effet visuel pour montrer que ça charge (optionnel)
                    document.querySelector('.products-grid-wrapper').style.opacity = '0.5';
                    form.submit();
                });
            });
        });


        document.addEventListener('DOMContentLoaded', function () {
            var slider = document.getElementById('price-slider');
            
            // Valeurs min/max globales (définit les bornes du slider)
            var minPrice = 0;
            var maxPrice = {{ $maxPrice }}; // Valeur max venant de votre contrôleur PHP

            // Valeurs actuelles (soit la requête, soit les bornes par défaut)
            var currentMin = {{ request('prix_min') ?? 0 }};
            var currentMax = {{ request('prix_max') ?? $maxPrice }};

            noUiSlider.create(slider, {
                start: [currentMin, currentMax], // Positions initiales des poignées
                connect: true, // Colorier la barre entre les deux
                range: {
                    'min': minPrice,
                    'max': maxPrice
                },
                tooltips: [
                {
                    to: function (value) {
                        return Math.round(value) + ' €'; // Pas de décimales, juste l'entier
                    }
                },
                {
                    to: function (value) {
                        return Math.round(value) + ' €';
                    }
                }
            ],
                step: 10, // Pas de 10€
            });

            // QUAND L'UTILISATEUR RELÂCHE LA POIGNÉE (Change)
            slider.noUiSlider.on('change', function (values, handle) {
                // 1. Mettre à jour les inputs cachés
                document.getElementById('input-prix-min').value = Math.round(values[0]);
                document.getElementById('input-prix-max').value = Math.round(values[1]);

                // 2. Soumettre le formulaire automatiquement (comme pour vos checkboxes)
                document.getElementById('filterForm').submit();
            });
        });
    </script>
</body>
</html>