<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUBE Bikes - {{ $titrePage }}</title>
    <link rel="stylesheet" href="{{ asset('css/listArticle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styleBody.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
    
    <script>
        function scrollToListing() {
            document.getElementById('listing-anchor').scrollIntoView({ behavior: 'smooth' });
        }
        
        function toggleSortMenu() {
            document.getElementById("sortDropdown").classList.toggle("active");
        }
        
        function applySort(sortValue) {
            let currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', sortValue);
            currentUrl.hash = "listing-anchor";
            window.location.href = currentUrl.toString();
        }
        
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-btn') && !event.target.closest('.dropdown-btn')) {
                var dropdowns = document.getElementsByClassName("custom-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('active')) dropdowns[i].classList.remove('active');
                }
            }
        }

        function toggleSection(headerElement) {
            headerElement.parentElement.classList.toggle('closed');
        }
    </script>
</head>
<body>
    
    @include('layouts.header') 

    @php
    if ($type === 'Electrique') {
        $bgImage = 'https://www.cubebikes.fr/img/c/4519.jpg';
    }
    elseif ($type === 'Musculaire') {
        $bgImage = 'https://www.cubebikes.fr/img/c/4518.jpg';
    } 
    else {
        $bgImage = asset('images/accessoires-de-velo.png');
    }
    @endphp
    

    <section class="hero-section" style="background-image: url('{{ $bgImage }}');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">{{ strtoupper($titrePage) }}</h1>
            <button onclick="scrollToListing()" class="btn-scroll-down">
                <i class="fas fa-angle-double-right"></i> VOIR TOUS LES {{ $isAccessoire ? 'ARTICLES' : 'VÉLOS' }}
            </button>
        </div>
    </section>
    
    <div id="listing-anchor" class="main-container">
        
        <aside class='filters-sidebar'>
            <form id="filterForm" action="{{ url()->current() }}#listing-anchor" method="GET">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                @if($hierarchyItems->isNotEmpty())
                    <div class="filter-section">
                        <div class="filter-header" onclick="toggleSection(this)">
                            <h3>{{ $hierarchyTitle }}</h3><i class="fas fa-chevron-up"></i>
                        </div>
                        <div class="filter-content">
                            @foreach($hierarchyItems as $item)
                                @php
                                    $currentFilters = request()->query();
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
                                    
                                    $targetUrl = route('boutique.index', array_merge($routeParams, $currentFilters));
                                @endphp
                                <label class="cube-checkbox" onclick="window.location.href='{{ $targetUrl }}#listing-anchor'">
                                    <input type="checkbox" {{ $isActive ? 'checked' : '' }}>
                                    <span class="box"></span>{{ strtoupper($item->name) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="filter-section">
                    <div class="filter-header" onclick="toggleSection(this)"><h3>DISPONIBILITÉ</h3><i class="fas fa-chevron-up"></i></div>
                    <div class="filter-content">
                        <label class="cube-checkbox">
                            <input type="checkbox" name="dispo_ligne" value="1" class="auto-submit" {{ request('dispo_ligne') ? 'checked' : '' }}>
                            <span class="box"></span>Disponible en ligne ({{ $countOnline }})
                        </label>
                        <label class="cube-checkbox">
                            <input type="checkbox" name="dispo_magasin" value="1" class="auto-submit" {{ request('dispo_magasin') ? 'checked' : '' }}>
                            <span class="box"></span>Disponible en magasin ({{ $countStore }})
                        </label>
                    </div>
                </div>

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

                @if(!$isAccessoire)
                    <div class="filter-section">
                        <div class="filter-header" onclick="toggleSection(this)"><h3>TAILLE</h3><i class="fas fa-chevron-up"></i></div>
                        <div class="filter-content">
                            @foreach($availableTailles as $taille)
                                <label class="cube-checkbox">
                                    <input type="checkbox" name="tailles[]" value="{{ trim($taille->taille) }}" class="auto-submit" {{ (is_array(request('tailles')) && in_array(trim($taille->taille), request('tailles'))) ? 'checked' : '' }}>
                                    <span class="box"></span>{{ trim($taille->taille) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="filter-section">
                        <div class="filter-header" onclick="toggleSection(this)"><h3>COULEUR</h3><i class="fas fa-chevron-up"></i></div>
                        <div class="filter-content" style="max-height: 200px; overflow-y: auto;">
                            @foreach($availableCouleurs as $c)
                                <label class="cube-checkbox">
                                    <input type="checkbox" name="couleurs[]" value="{{ $c->id_couleur }}" class="auto-submit" {{ (is_array(request('couleurs')) && in_array($c->id_couleur, request('couleurs'))) ? 'checked' : '' }}>
                                    <span class="box"></span>{{ ucfirst(trim($c->nom_couleur)) }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

            </form>
        </aside>
        
        <main class='products-grid-wrapper'>
            <div class="top-bar">
                <span>{{ $articles->total() }} PRODUITS</span>
                
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

            @if($articles->isEmpty())
                <div class="empty-state" style="padding:50px; text-align:center; color:#666;">
                    <p>Aucun article ne correspond à vos critères.</p>
                    <a href="{{ url()->current() }}" style="text-decoration:underline; font-weight:bold;">Réinitialiser</a>
                </div>
            @else
                <div class="products-grid">
                    @foreach($articles as $article)
                        <div class="product-card">
                            <div class="badge-new">NOUVEAU</div>
                            <div class="product-image">
                                <a href="{{ url($isAccessoire ? '/accessoire/' : '/velo/') . $article->reference }}">
                                    @if ($isAccessoire)
                                        <img src="{{ asset('images/VELOS/' . substr($article->reference,0,5) . '/image_1.jpg') }}" alt="{{ $article->nom_article }}">
                                    @else
                                        <img src="{{ asset('images/VELOS/' . substr($article->reference,0,6) . '/image_1.jpg') }}" alt="{{ $article->nom_article }}">
                                    @endif
                                </a>
                            </div>
                            
                            <div class="product-details">
                                <h2 class="product-title">
                                    <a href="{{ url($isAccessoire ? '/accessoire/' : '/velo/') . $article->reference }}">
                                        @if($isAccessoire)
                                            {{ strtoupper($article->nom_article) }}
                                        @else
                                            {{ strtoupper($article->modele->nom_modele ?? '') }} - {{ strtoupper(str_replace($article->modele->nom_modele ?? '', '', $article->nom_article)) }}
                                        @endif
                                    </a>
                                </h2>

                                @php
                                    $dispoLigne = false;
                                    $dispoMag = false;

                                    if ($isAccessoire) {
                                        // Disponibilité Accessoires (Booléens simples)
                                        $dispoLigne = (bool)$article->dispo_en_ligne;
                                        $dispoMag = (bool)$article->dispo_magasin;
                                    } else {
                                        // Disponibilité Vélos (Via inventaires)
                                        $filtreTailles = request('tailles');
                                        
                                        // Relation 'inventaires' héritée de Article (hasMany ArticleInventaire)
                                        $invs = $article->inventaires;
                                        
                                        if ($filtreTailles && is_array($filtreTailles)) {
                                            $invs = $invs->filter(fn($i) => in_array(trim($i->taille->taille), $filtreTailles));
                                        }

                                        // Calcul Dispo Ligne
                                        $stockLigne = $invs->sum('quantite_stock_en_ligne');
                                        $dispoLigne = ($stockLigne > 0);
                                        
                                        // Calcul Dispo Magasin (Somme Pivot via ArticleInventaire -> magasins)
                                        $stockMag = 0;
                                        foreach($invs as $i) { 
                                            // 'magasins' est la relation belongsToMany dans ArticleInventaire
                                            $stockMag += $i->magasins->sum('pivot.quantite_stock_magasin'); 
                                        }
                                        $dispoMag = ($stockMag > 0);
                                    }
                                @endphp

                                <div class="availability">
                                    <div class="status-line">
                                        <span class="dot {{ $dispoLigne ? 'green' : 'red' }}"></span>
                                        <span class="text">Disponible en ligne</span>
                                    </div>
                                    <div class="status-line">
                                        <span class="dot {{ $dispoMag ? 'green' : 'orange' }}"></span>
                                        <span class="text">Disponible en magasins <i class="far fa-question-circle info-icon"></i></span>
                                    </div>
                                </div>

                                <div class="product-footer">
                                    <div class="price">{{ number_format($article->prix, 0, ',', ' ') }} €</div>
                                </div>
                            </div>
                            
                            <a href="{{ url($isAccessoire ? '/accessoire' : '/velo') . '/' . $article->reference }}" class="btn-skew">
                                <span>VOIR LE PRODUIT</span> <i class="fas fa-caret-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="pagination-wrapper">
                    {{ $articles->links('pagination::bootstrap-4') }}
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
                    document.querySelector('.products-grid-wrapper').style.opacity = '0.5';
                    form.submit();
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var slider = document.getElementById('price-slider');
            var minPrice = 0;
            var maxPrice = {{ $maxPrice }};
            var currentMin = {{ request('prix_min') ?? 0 }};
            var currentMax = {{ request('prix_max') ?? $maxPrice }};

            noUiSlider.create(slider, {
                start: [currentMin, currentMax],
                connect: true,
                range: { 'min': minPrice, 'max': maxPrice },
                tooltips: [
                    { to: function (value) { return Math.round(value) + ' €'; } },
                    { to: function (value) { return Math.round(value) + ' €'; } }
                ],
                step: 10,
            });

            slider.noUiSlider.on('change', function (values, handle) {
                document.getElementById('input-prix-min').value = Math.round(values[0]);
                document.getElementById('input-prix-max').value = Math.round(values[1]);
                document.getElementById('filterForm').submit();
            });
        });
    </script>
</body>
</html>