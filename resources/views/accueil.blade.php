<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>CUBE Bikes France</title>
</head>
<body>
    <div id="cookieBanner" class="cookie-banner">
        <div class="cookie-content">
            <div class="cookie-img-container">
                <img src="{{ asset('images/cookies-bg.jpg   ') }}" alt="CUBE Logo" class="cookie-img">
            </div>
            <div class="cookie-icon-container">
                <img src="{{ asset('images/logo-black.png   ') }}" alt="CUBE Logo" class="cookie-icon">
            </div>
            <div class="cookie-text">
                <button id="declineCookies" class="btn-decline">Continuer sans Accepter</button>
                <p>Avec votre accord, nous et nos 9 partenaires utilisons des cookies ou technologies similaires pour stocker, consulter et traiter des données personnelles telles que votre visite sur ce site internet, les adresses IP et les identifiants de cookie. Certains partenaires ne demandent pas votre consentement pour traiter vos données et se fondent sur leur intérêt commercial légitime. À tout moment, vous pouvez retirer votre consentement ou vous opposer au traitement des données fondé sur l'intérêt légitime en cliquant sur « En savoir plus » ou en allant dans notre politique de confidentialité sur ce site internet.</p>
            </div>
            <div class="cookie-buttons">
                <button id="acceptCookies" class="btn-accept">Accepter</button>
                <button id="customizeCookies" class="btn-customize">Personnaliser</button>
            </div>
        </div>
    </div>

    <!-- Cookie Preferences Modal -->
    <div id="cookieModal" class="cookie-modal">
        <div class="cookie-modal-content">
            <div class="cookie-modal-header">
                <h2>Préférences de cookies</h2>
                <button id="closeModal" class="close-modal">&times;</button>
            </div>
            <div class="cookie-modal-body">
                <p class="modal-intro">Avec votre accord, nous et nos 9 partenaires utilisons des cookies ou technologies similaires pour stocker, consulter et traiter des données personnelles telles que votre visite sur ce site internet, les adresses IP et les identifiants de cookie. Certains partenaires ne demandent pas votre consentement pour traiter vos données et se fondent sur leur intérêt commercial légitime. À tout moment, vous pouvez retirer votre consentement ou vous opposer au traitement des données fondé sur l'intérêt légitime en cliquant sur « En savoir plus » ou en allant dans notre politique de confidentialité sur ce site internet.</p>
                <p>Vos données personnelles sont traitées pour les finalités suivantes:</p>
                <p class="modal-intro use-detail">Comprendre les publics par le biais de statistiques ou de combinaisons de données provenant de différentes sources, Créer des profils de contenus personnalisés, Créer des profils pour la publicité personnalisée, Développer et améliorer les services, Mesurer la performance des contenus, Mesurer la performance des publicités, Nécessaires, Stocker et/ou accéder à des informations sur un appareil, Utiliser des données limitées pour sélectionner la publicité, Utiliser des profils pour sélectionner des contenus personnalisés, Utiliser des profils pour sélectionner des publicités personnalisées</p>
                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <div class="cookie-category-title">
                            <h3>🔒 Cookies nécessaires</h3>
                            <span class="required-badge">Obligatoire</span>
                        </div>
                        <label class="toggle-switch disabled">
                            <input type="checkbox" id="necessaryCookies" checked disabled>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p class="cookie-description">Ces cookies sont essentiels au fonctionnement du site et ne peuvent pas être désactivés. Ils permettent les fonctionnalités de base comme la navigation et l'accès aux zones sécurisées.</p>
                </div>

                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <div class="cookie-category-title">
                            <h3>📊 Cookies analytiques</h3>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="analyticsCookies">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p class="cookie-description">Ces cookies nous permettent de comprendre comment les visiteurs interagissent avec notre site en collectant des informations anonymes. Cela nous aide à améliorer l'expérience utilisateur.</p>
                </div>

                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <div class="cookie-category-title">
                            <h3>🎯 Cookies marketing</h3>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="marketingCookies">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p class="cookie-description">Ces cookies sont utilisés pour afficher des publicités pertinentes et suivre l'efficacité de nos campagnes marketing. Ils peuvent être définis par nos partenaires publicitaires.</p>
                </div>

                <div class="cookie-category">
                    <div class="cookie-category-header">
                        <div class="cookie-category-title">
                            <h3>⚙️ Cookies de préférences</h3>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="preferenceCookies">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p class="cookie-description">Ces cookies permettent au site de mémoriser vos préférences (langue, région, etc.) pour vous offrir une expérience personnalisée.</p>
                </div>
            </div>
            <div class="cookie-modal-footer">
                <button id="acceptAllModal" class="btn-modal btn-accept-all">Tout accepter</button>
                <button id="savePreferences" class="btn-modal btn-save">Enregistrer mes préférences</button>
                <button id="rejectAllModal" class="btn-modal btn-reject-all">Tout refuser</button>
            </div>
        </div>
    </div>
    <header>
        <nav>
            <div class="menu-category">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('images/cubelogo.png') }}" alt="Logo CUBE Bikes" style="max-height: 100px;">
                </a>
                
                <ul class="nav-list">
                    
                    <li class="nav-item">
                        <a id="btn-velo" href="#" class="link_category">VÉLOS</a>
                        
                        <div class="mega-menu-dropdown">
                            <div class="mega-menu-wrapper" id="wrapper-velo">
                                
                                <div class="col-roots">
                                    @if(isset($menuVelo))
                                        @foreach($menuVelo as $root)
                                            <div class="menu-item root-trigger" data-target="subs-velo-{{ $root->id_categorie }}">
                                                {{ strtoupper($root->nom_categorie) }}
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
                                                        {{ strtoupper($enfant->nom_categorie) }}
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
                                                    <h3 style="margin-bottom: 20px; font-family:'Fira Sans'; font-weight:800;">{{ strtoupper($enfant->nom_categorie) }}</h3>
                                                    @if($enfant->modeles->isNotEmpty())
                                                        <div class="model-grid">
                                                            @foreach($enfant->modeles as $modele)
                                                                <a href="#" class="model-link">{{ $modele->nom_modele }}</a>
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
                        <a id="btn-elec" href="#" class="link_category">VÉLOS ÉLECTRIQUES</a>

                        <div class="mega-menu-dropdown">
                            <div class="mega-menu-wrapper" id="wrapper-elec">
                                
                                <div class="col-roots">
                                    @if(isset($menuElec))
                                        @foreach($menuElec as $root)
                                            <div class="menu-item root-trigger" data-target="subs-elec-{{ $root->id_categorie }}">
                                                {{ strtoupper($root->nom_categorie) }}
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
                                                        {{ strtoupper($enfant->nom_categorie) }}
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
                                                    <h3 style="margin-bottom: 20px; font-family:'Fira Sans'; font-weight:800;">{{ strtoupper($enfant->nom_categorie) }}</h3>
                                                    @if($enfant->modeles->isNotEmpty())
                                                        <div class="model-grid">
                                                            @foreach($enfant->modeles as $modele)
                                                                <a href="#" class="model-link">{{ $modele->nom_modele }}</a>
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
                        <a id="btn-accessoire" href="#" class="link_category">ACCESSOIRES</a>
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
                    <a id="recherche" href="{{ url('/recherche') }}">
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
                
                    <a id="panier" href="{{ url('/panier') }}">
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
    </header>

    <main>
        <section class="intro-carousel" id="intro-carousel">
            <div id="image-carousel" class="carousel">
                <img src="{{ asset('images/1.png') }}" alt="Image 1" class="carousel-image active">
                <img src="{{ asset('images/2.jpg') }}" alt="Image 2" class="carousel-image">
                <img src="{{ asset('images/3.jpg') }}" alt="Image 3" class="carousel-image">
            </div>
        </section>

        <section class="intro-text" id="intro-text">
            <h1>Bienvenue chez CUBE Bikes France</h1>
            <h2>Des vélos d’une qualité exceptionnelle taillés pour la performance</h2>
            <p>Découvrez notre gamme de vélos et accessoires de qualité supérieure. Que vous soyez un cycliste passionné ou un amateur de balades, nous avons le vélo parfait pour vous.</p>
        </section>

        <section class="Univers" id="Univers">
            <div class="univers-cards">
                <div class="card">
                    <div class="card-content">
                        <div class="card-text">
                            <h3>Vélos de Route</h3>
                            <p>Performance et légèreté pour les passionnés de vitesse. 
                                Dévorer du bitume, repousser vos limites, passer des cols en tête ou performer sur les routes pavées et sortir des sentiers battus sur votre gravel? Tout est possible!</p>
                            <a href="{{ url('/Velo') }}" class="btn">Voir les vélos de route</a>
                        </div>
                        <div class="card-image">
                            <img src="{{ asset('images/velo_route.jpg') }}" alt="Vélo de route">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-text">
                            <h3>VTT</h3>
                            <p>Robustesse et maniabilité pour les terrains accidentés. S'évader dans la Nature, explorer les montagnes qui vous entourent,
                                vous dépenser en montée, prendre un maximum de plaisir en descente... Il y a forcément un VTT CUBE paré à vous accompagner dans vos
                                aventures.</p>
                            <a href="{{ url('/Velo') }}" class="btn">Voir les VTT</a>
                        </div>
                        <div class="card-image">
                            <img src="{{ asset('images/velo_vtt.jpg') }}" alt="VTT">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-text">
                            <h3>Vélos Électriques</h3>
                            <p>Assistance électrique pour des trajets sans effort. Nos VTT électriques ont des points communs qui font toute la différence. Au programme : géométrie optimale et équipement parfaitement étudié. Objectif : partir à l'aventure le week-end ou à l'assaut du quotidien, en toute sérénité. Dans la gamme Stereo, vous trouverez forcément l'E-Fully sur la même longueur d'onde que vous.</p>
                            <a href="{{ url('/velo_electrique') }}" class="btn">Voir les vélos électriques</a>
                        </div>
                        <div class="card-image">
                            <img src="{{ asset('images/velo_electrique.jpg') }}" alt="Vélos Électriques">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="accessoires" id="accessoires">
            <h2>Accessoires indispensables pour votre vélo</h2> 
            <div class="accessoires-cards">
                <div class="accessoire-card">
                    <div class="accessoire-image" style="background-image: url('{{ asset('images/selle.jpg') }}');">
                        <h3>Selle</h3>
                    </div>
                    <a href="{{ url('/accessoires') }}" class="btn">Voir les selles</a>
                </div>
                <div class="accessoire-card">
                    <div class="accessoire-image" style="background-image: url('{{ asset('images/roue.jpg') }}');">
                        <h3>Roue</h3>
                    </div>
                    <a href="{{ url('/accessoires') }}" class="btn">Voir les roues</a>
                </div>
                <div class="accessoire-card">
                    <div class="accessoire-image" style="background-image: url('{{ asset('images/eclairage.jpg') }}');">
                        <h3>Éclairages</h3>
                    </div>
                    <a href="{{ url('/accessoires') }}" class="btn">Voir les éclairages</a>
                </div>
            </div>
        </section>
    
        <div id="image-carousel-bottom" class="carousel">
          <img src="{{ asset('images/1.png') }}" alt="Image 1" class="carousel-image active">
          <img src="{{ asset('images/2.jpg') }}" alt="Image 2" class="carousel-image">
          <img src="{{ asset('images/3.jpg') }}" alt="Image 3" class="carousel-image">
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CUBE Bikes France</p>
    </footer>

    <script src="{{ asset('js/caroussel.js') }}" defer></script>
    <script src="{{ asset('js/header.js') }}" defer></script>
    <script src="{{ asset('js/cookies.js') }}" defer></script>
    
</body>

</html>