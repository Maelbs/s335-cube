<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <link rel="stylesheet" href="{{ asset('css/magasin.css') }}">
    <style>
        /* On cible l'ID et la Classe pour être sûr à 100% */
        #store-locator-overlay, .sl-overlay {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        /* On autorise l'affichage UNIQUEMENT si la classe .visible est ajoutée par JS */
        #store-locator-overlay.visible, .sl-overlay.visible {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
</head>

<header>
    <div class="menu-category">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('images/logo-cube.png') }}" alt="Logo CUBE Bikes" style="max-height: 150px;" fetchpriority="high">
        </a>

        <ul class="nav-list">

            {{-- 1. MENU VÉLOS (MUSCULAIRE) --}}
            <li class="nav-item">
                <a id="btn-velo" href="{{ route('boutique.index', ['type' => 'Musculaire']) }}" class="link_category">VÉLOS</a>
                <div class="mega-menu-dropdown">
                    <div class="mega-menu-wrapper" id="wrapper-velo">
                        {{-- Colonne Racines --}}
                        <div class="col-roots">
                            @if(isset($menuVelo))
                                @foreach($menuVelo as $root)
                                    <div class="menu-item root-trigger" data-target="subs-velo-{{ $root->id_categorie }}">
                                        <a href="{{ route('boutique.index', ['type' => 'Musculaire', 'cat_id' => $root->id_categorie]) }}" class="menu-link-reset">
                                            {{ strtoupper($root->nom_categorie) }}
                                        </a>
                                        <span>&rsaquo;</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Colonne Sous-Catégories --}}
                        <div class="col-subs-wrapper">
                            @if(isset($menuVelo))
                                @foreach($menuVelo as $root)
                                    <div id="subs-velo-{{ $root->id_categorie }}" class="subs-container d-none">
                                        @foreach($root->enfants as $enfant)
                                            <div class="menu-item sub-trigger" data-target="models-velo-{{ $enfant->id_categorie }}">
                                                <a href="{{ route('boutique.index', ['type' => 'Musculaire', 'cat_id' => $root->id_categorie, 'sub_id' => $enfant->id_categorie]) }}" class="menu-link-reset">
                                                    {{ strtoupper($enfant->nom_categorie) }}
                                                </a>
                                                <span>&rsaquo;</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Colonne Modèles --}}
                        <div class="col-models-wrapper">
                            @if(isset($menuVelo))
                                @foreach($menuVelo as $root)
                                    @foreach($root->enfants as $enfant)
                                        <div id="models-velo-{{ $enfant->id_categorie }}" class="models-container d-none">
                                            <h3 style="margin-bottom: 20px; font-weight:800;">{{ strtoupper($enfant->nom_categorie) }}</h3>
                                            @if($enfant->modeles->isNotEmpty())
                                                <div class="model-grid">
                                                    @foreach($enfant->modeles as $modele)
                                                        <a href="{{ route('boutique.index', ['type' => 'Musculaire', 'cat_id' => $root->id_categorie, 'sub_id' => $enfant->id_categorie, 'model_id' => $modele->id_modele]) }}" class="model-link">
                                                            {{ $modele->nom_modele }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p style="color:#999; font-style:italic;">Voir tous</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </li>

            {{-- 2. MENU VÉLOS ÉLECTRIQUES --}}
            <li class="nav-item">
                <a id="btn-elec" href="{{ route('boutique.index', ['type' => 'Electrique']) }}" class="link_category">VÉLOS ÉLECTRIQUES</a>
                <div class="mega-menu-dropdown">
                    <div class="mega-menu-wrapper" id="wrapper-elec">
                        {{-- Colonne Racines --}}
                        <div class="col-roots">
                            @if(isset($menuElec))
                                @foreach($menuElec as $root)
                                    <div class="menu-item root-trigger" data-target="subs-elec-{{ $root->id_categorie }}">
                                        <a href="{{ route('boutique.index', ['type' => 'Electrique', 'cat_id' => $root->id_categorie]) }}" class="menu-link-reset">
                                            {{ strtoupper($root->nom_categorie) }}
                                        </a>
                                        <span>&rsaquo;</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Colonne Sous-Catégories --}}
                        <div class="col-subs-wrapper">
                            @if(isset($menuElec))
                                @foreach($menuElec as $root)
                                    <div id="subs-elec-{{ $root->id_categorie }}" class="subs-container d-none">
                                        @foreach($root->enfants as $enfant)
                                            <div class="menu-item sub-trigger" data-target="models-elec-{{ $enfant->id_categorie }}">
                                                <a href="{{ route('boutique.index', ['type' => 'Electrique', 'cat_id' => $root->id_categorie, 'sub_id' => $enfant->id_categorie]) }}" class="menu-link-reset">
                                                    {{ strtoupper($enfant->nom_categorie) }}
                                                </a>
                                                <span>&rsaquo;</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Colonne Modèles --}}
                        <div class="col-models-wrapper">
                            @if(isset($menuElec))
                                @foreach($menuElec as $root)
                                    @foreach($root->enfants as $enfant)
                                        <div id="models-elec-{{ $enfant->id_categorie }}" class="models-container d-none">
                                            <h3 style="margin-bottom: 20px; font-weight:800;">{{ strtoupper($enfant->nom_categorie) }}</h3>
                                            @if($enfant->modeles->isNotEmpty())
                                                <div class="model-grid">
                                                    @foreach($enfant->modeles as $modele)
                                                        <a href="{{ route('boutique.index', ['type' => 'Electrique', 'cat_id' => $root->id_categorie, 'sub_id' => $enfant->id_categorie, 'model_id' => $modele->id_modele]) }}" class="model-link">
                                                            {{ $modele->nom_modele }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p style="color:#999; font-style:italic;">Voir tous</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </li>

            {{-- 3. MENU ACCESSOIRES --}}
            <li class="nav-item">
                <a id="btn-accessoire" href="{{ route('boutique.index', ['type' => 'Accessoires']) }}" class="link_category">ACCESSOIRES</a>
                <div class="mega-menu-dropdown">
                    <div class="mega-menu-wrapper" id="wrapper-accessoire">
                        {{-- Colonne Racines --}}
                        <div class="col-roots">
                            @if(isset($categorieAccessoires))
                                @foreach($categorieAccessoires as $root)
                                    <div class="menu-item root-trigger" data-target="sub-acc-{{ $root->id_categorie_accessoire }}">
                                        <a href="{{ route('boutique.index', ['type' => 'Accessoires', 'cat_id' => $root->id_categorie_accessoire]) }}" class="menu-link-reset">
                                            {{ strtoupper($root->nom_categorie_accessoire) }}
                                        </a>
                                        <span>&rsaquo;</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Colonne Sous-Catégories --}}
                        <div class="col-subs-wrapper">
                            @if(isset($categorieAccessoires))
                                @foreach($categorieAccessoires as $root)
                                    <div id="sub-acc-{{ $root->id_categorie_accessoire }}" class="subs-container d-none">
                                        @if($root->enfants->isNotEmpty())
                                            @foreach($root->enfants as $enfant)
                                                <div class="menu-item sub-trigger" data-target="grandchild-acc-{{ $enfant->id_categorie_accessoire }}">
                                                    <a href="{{ route('boutique.index', ['type' => 'Accessoires', 'cat_id' => $enfant->cat_id_categorie_accessoire, 'sub_id' => $enfant->id_categorie_accessoire]) }}" class="menu-link-reset">
                                                        {{ strtoupper($enfant->nom_categorie_accessoire) }}
                                                    </a>
                                                    <span>&rsaquo;</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="p-3 text-muted">Voir les produits</p>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- Colonne Modèles (Petit-Enfants pour accessoires) --}}
                        <div class="col-models-wrapper">
                            @if(isset($categorieAccessoires))
                                @foreach($categorieAccessoires as $root)
                                    @foreach($root->enfants as $enfant)
                                        <div id="grandchild-acc-{{ $enfant->id_categorie_accessoire }}" class="models-container d-none">
                                            <h3>{{ $enfant->nom_categorie_accessoire }}</h3>
                                            <div class="model-grid">
                                                @if($enfant->enfants && $enfant->enfants->isNotEmpty())
                                                    @foreach($enfant->enfants as $petitEnfant)
                                                        <a href="{{ route('boutique.index', ['type' => 'Accessoires', 'cat_id' => $enfant->cat_id_categorie_accessoire, 'sub_id' => $petitEnfant->cat_id_categorie_accessoire, 'model_id' => $petitEnfant->id_categorie_accessoire]) }}" class="model-link">
                                                            {{ $petitEnfant->nom_categorie_accessoire }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">Voir tous les produits</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            {{-- 4. MENU AIDE & FAQ --}}
            <li class="nav-item">
                <a href="{{ route('aide') }}" class="link_category">AIDE & FAQ</a>
            </li>

            <li class="nav-item">
                <a href="{{ route('contact') }}" class="link_category">Contactez-nous</a>
            </li>
        </ul>
    </div>
    
    <style>
        .menu-link-reset {
            text-decoration: none; 
            color: inherit; 
            display: block; 
            width: 100%; 
            height: 100%;
        }
    </style>
    
    <div class="menu-user">
       
    <div class="header-store-info">
        @if(isset($magasinHeader) && $magasinHeader)
            {{-- CAS 1 : Un magasin est sélectionné --}}
            <button onclick="toggleStoreLocator()" class="btn-select-store">{{ strtoupper($magasinHeader->nom_magasin) }}</button>

        @else
            {{-- CAS 2 : Aucun magasin sélectionné --}}
            <button onclick="toggleStoreLocator()" class="btn-select-store">
                Choisir mon magasin
            </button>
        @endif
    </div>
        
        <svg xmlns="http://www.w3.org/2000/svg" onclick="toggleStoreLocator()" width="25" height="25" viewBox="0 0 24 24">
            <g>
                <path d="M20.485 3h-3.992l.5 5s1 1 2.5 1a3.23 3.23 0 0 0 2.139-.806a.503.503 0 0 0 .15-.465L21.076 3.5a.6.6 0 0 0-.591-.5Z"/>
                <path d="m16.493 3l.5 5s-1 1-2.5 1s-2.5-1-2.5-1V3h4.5Z"/>
                <path d="M11.993 3v5s-1 1-2.5 1s-2.5-1-2.5-1l.5-5h4.5Z"/>
                <path d="M7.493 3H3.502a.6.6 0 0 0-.592.501L2.205 7.73a.504.504 0 0 0 .15.465c.328.29 1.061.806 2.138.806c1.5 0 2.5-1 2.5-1l.5-5Z"/>
                <path d="M3 9v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9"/>
                <path d="M14.833 21v-6a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v6"/>
            </g>
        </svg>
        
        <div class="icone">
            <a id="recherche" href="#" onclick="openSearch(event)" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 20 20">
                    <g>
                        <path fill-rule="evenodd" d="M4.828 4.828A5 5 0 1 0 11.9 11.9a5 5 0 0 0-7.07-7.07Zm6.364 6.364a4 4 0 1 1-5.656-5.657a4 4 0 0 1 5.656 5.657Z" clip-rule="evenodd"/>
                        <path d="M11.192 12.627a1 1 0 0 1 1.415-1.414l2.828 2.829a1 1 0 1 1-1.414 1.414l-2.829-2.829Z"/>
                    </g>
                </svg>
            </a>
            
            @auth
                <a href="{{ url('/profil') }}" aria-label="Profile">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
                        <g stroke-width="2">
                            <path stroke-linejoin="round" d="M4 18a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/>
                            <circle cx="12" cy="7" r="3"/>
                        </g>
                    </svg>
                </a>
            @endauth

            @guest
                <a href="{{ url('/connexion') }}" aria-label="Connexion">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
                        <g stroke-width="2">
                            <path stroke-linejoin="round" d="M4 18a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/>
                            <circle cx="12" cy="7" r="3"/>
                        </g>
                    </svg>
                </a>
            @endguest

            {{-- 🛒 MINI PANIER --}}
            
            <div class="cart-dropdown-wrapper">
                <a id="panier" href="{{ route('cart.index') }}" aria-label="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 48 48">
                        <g><path d="M39 32H13L8 12h36l-5 20Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M3 6h3.5L8 12m0 0l5 20h26l5-20H8Z" /><circle cx="13" cy="39" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" /><circle cx="39" cy="39" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" /></g>
                    </svg>
                    @if(count($cartItems) > 0)
                        <span style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:16px; height:16px; font-size:10px; display:flex; justify-content:center; align-items:center;">
                            {{ count($cartItems) }}
                        </span>
                    @endif
                </a>

                <div class="cart-preview-box">
                    <div class="cart-items-list">
                        @if(count($cartItems) > 0)
                            @foreach($cartItems as $item)
                                <div class="cart-item">
                                    <div class="cart-item-img">
                                        {{-- Image gérée par le Composer (URL absolue) --}}
                                        <img src="{{ $item['image'] }}" 
                                             alt="{{ $item['name'] }}"
                                             style="width: 100%; height: auto; object-fit: contain;"
                                             onerror="this.onerror=null; this.src='https://placehold.co/80x80?text=No+Img'">
                                    </div>

                                    <div class="cart-item-details">
                                        <span class="item-name">
                                            {{ $item['name'] }}
                                            
                                            {{-- AJOUT DE LA TAILLE ICI --}}
                                            @if(isset($item['taille']) && $item['taille'] !== 'Non renseigné' && $item['taille'] !== 'Unique')
                                                <span style="font-weight: bold; color: #333;">({{ $item['taille'] }})</span>
                                            @endif

                                            @if(($item['quantity'] ?? 1) > 1)
                                                <small class="text-muted">x{{ $item['quantity'] }}</small>
                                            @endif
                                        </span>
                                        <span class="item-price">{{ number_format($item['price'], 2, ',', ' ') }} €</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div style="padding: 30px 20px; text-align: center; color: #999;">
                                <p style="margin:0; font-style: italic;">Votre panier est vide.</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="cart-footer">
                        <div class="cart-total-row">
                            <span class="label">TOTAL <small>(Hors livraison)</small></span>
                            {{-- Utilisation directe de $cartTotal --}}
                            <span class="price">{{ number_format($cartTotal, 2, ',', ' ') }} € TTC</span>
                        </div>
                        <a href="{{ route('cart.index') }}" class="btn-view-cart">
                            <span style="margin-right:5px;">&#9658;</span> Voir le panier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="full-search-overlay" class="search-overlay-full">
    <div class="search-wrapper">
        <form action="{{ route('boutique.index', ['type' => 'Musculaire']) }}" method="GET">
            <input type="text" name="search" placeholder="Chercher..." autocomplete="off">
            <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20">
                    <g><path fill-rule="evenodd" d="M4.828 4.828A5 5 0 1 0 11.9 11.9a5 5 0 0 0-7.07-7.07Zm6.364 6.364a4 4 0 1 1-5.656-5.657a4 4 0 0 1 5.656 5.657Z" clip-rule="evenodd" /><path d="M11.192 12.627a1 1 0 0 1 1.415-1.414l2.828 2.829a1 1 0 1 1-1.414 1.414l-2.829-2.829Z" /></g>
                </svg>
            </button>
        </form>
    </div>
    <div class="close-btn" onclick="closeSearch()">&times;</div>
</div>
@include('layouts.storeLocator')

<script src="{{ asset('js/header.js') }}" defer></script>
<script src="{{ asset('js/map.js') }}" defer></script>

@include('layouts.chatbot')
