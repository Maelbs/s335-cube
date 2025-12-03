<header>
        <div class="menu-category">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('images/logo-cube.png') }}" alt="Logo CUBE Bikes" style="max-height: 150px;">
            </a>
            
            <ul class="nav-list">
                
                <li class="nav-item">
                    <a id="btn-velo" href="{{ route('boutique.index', ['type' => 'Musculaire']) }}" class="link_category">VÉLOS</a>
                    <div class="mega-menu-dropdown">
                        <div class="mega-menu-wrapper" id="wrapper-velo">
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
                            <div class="col-models-wrapper">
                                @if(isset($menuVelo))
                                    @foreach($menuVelo as $root)
                                        @foreach($root->enfants as $enfant)
                                            <div id="models-velo-{{ $enfant->id_categorie }}" class="models-container d-none">
                                                <h3 style="margin-bottom: 20px; font-weight:800;">
                                                    {{ strtoupper($enfant->nom_categorie) }}
                                                </h3>
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

                <li class="nav-item">
                    <a id="btn-elec" href="{{ route('boutique.index', ['type' => 'Electrique']) }}" class="link_category">VÉLOS ÉLECTRIQUES</a>
                    <div class="mega-menu-dropdown">
                        <div class="mega-menu-wrapper" id="wrapper-elec">
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
                                                    <p style="color:#999; font-style:italic;">Aucun modèle disponible.</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a id="btn-accessoire" href="{{ route('boutique.index', ['type' => 'Accessoires']) }}" class="link_category">ACCESSOIRES</a>

                    <div class="mega-menu-dropdown">
                        <div class="mega-menu-wrapper" id="wrapper-accessoire">

                            <div class="col-roots">
                                @foreach($categorieAccessoires as $root)
                                    <div class="menu-item root-trigger" data-target="sub-acc-{{ $root->id_categorie_accessoire }}">
                                        <a href="{{ route('boutique.index', ['type' => 'Accessoires', 'cat_id' => $root->id_categorie_accessoire]) }}" class="menu-link-reset">
                                            {{ strtoupper($root->nom_categorie_accessoire) }}
                                        </a>
                                        <span>&rsaquo;</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="col-subs-wrapper">
                                @foreach($categorieAccessoires as $root)
                                    <div id="sub-acc-{{ $root->id_categorie_accessoire }}" class="subs-container d-none">
                                        
                                        @if($root->enfants->isNotEmpty())
                                            @foreach($root->enfants as $enfant)
                                                <div class="menu-item sub-trigger" data-target="grandchild-acc-{{ $enfant->id_categorie_accessoire }}">
                                                    <a href="{{ route('boutique.index', ['type' => 'Accessoires', 'cat_id' => $root->id_categorie_accessoire, 'sub_id' => $enfant->id_categorie_accessoire]) }}" class="menu-link-reset">
                                                        {{ strtoupper($enfant->nom_categorie_accessoire) }}
                                                    </a>
                                                    <span>&rsaquo;</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="p-3 text-muted">Aucune sous-catégorie</p>
                                        @endif

                                    </div>
                                @endforeach
                            </div>

                            <div class="col-models-wrapper">
                                @foreach($categorieAccessoires as $root)
                                    @foreach($root->enfants as $enfant)
                                        
                                        <div id="grandchild-acc-{{ $enfant->id_categorie_accessoire }}" class="models-container d-none">
                                            <h3>{{ $enfant->nom_categorie_accessoire }}</h3>
                                            
                                            <div class="model-grid">
                                                @if($enfant->enfants && $enfant->enfants->isNotEmpty())
                                                    @foreach($enfant->enfants as $petitEnfant)
                                                        <a href="{{ route('boutique.index', ['type' => 'Accessoires', 'cat_id' => $root->id_categorie_accessoire, 'sub_id' => $enfant->id_categorie_accessoire, 'model_id' => $modele->id_modele]) }}" class="model-link">
                                                            {{ $petitEnfant->nom_categorie_accessoire }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">Voir tous</p>
                                                @endif
                                            </div>
                                        </div>

                                    @endforeach
                                @endforeach
                            </div>

                        </div>
                    </div>
                </li>
            </ul>
        </div>
        
        
        <div class="menu-user">
            <a id="magasin" href="{{ url('/login') }}">CHOISIR UN MAGASIN</a>
            
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
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
                <a id="recherche" href="#" onclick="openSearch(event)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 20 20">
                        <g>
                            <path fill-rule="evenodd" d="M4.828 4.828A5 5 0 1 0 11.9 11.9a5 5 0 0 0-7.07-7.07Zm6.364 6.364a4 4 0 1 1-5.656-5.657a4 4 0 0 1 5.656 5.657Z" clip-rule="evenodd"/>
                            <path d="M11.192 12.627a1 1 0 0 1 1.415-1.414l2.828 2.829a1 1 0 1 1-1.414 1.414l-2.829-2.829Z"/>
                        </g>
                    </svg>
                </a>
                <a id="login" href="{{ url('/login') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
                        <g stroke-width="2">
                            <path stroke-linejoin="round" d="M4 18a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/>
                            <circle cx="12" cy="7" r="3"/>
                        </g>
                    </svg>
                </a>
                <a id="panier" href="{{ route('cart.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 48 48">
                        <g>
                            <path d="M39 32H13L8 12h36l-5 20Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M3 6h3.5L8 12m0 0l5 20h26l5-20H8Z"/>
                            <circle cx="13" cy="39" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"/>
                            <circle cx="39" cy="39" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"/>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </nav> 

    <div id="full-search-overlay" class="search-overlay-full">
        <div class="search-wrapper">
            <form action="{{ route('boutique.index', ['type' => 'Musculaire']) }}" method="GET">
                <input type="text" name="search" placeholder="Chercher..." autocomplete="off">
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20">
                        <g>
                            <path fill-rule="evenodd" d="M4.828 4.828A5 5 0 1 0 11.9 11.9a5 5 0 0 0-7.07-7.07Zm6.364 6.364a4 4 0 1 1-5.656-5.657a4 4 0 0 1 5.656 5.657Z" clip-rule="evenodd"/>
                            <path d="M11.192 12.627a1 1 0 0 1 1.415-1.414l2.828 2.829a1 1 0 1 1-1.414 1.414l-2.829-2.829Z"/>
                        </g>
                    </svg>
                </button>
            </form>
        </div>
        <div class="close-btn" onclick="closeSearch()">
            &times;
        </div>
    </div>

</header>

<script src="{{ asset('js/header.js') }}" defer></script>
