<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styleBody.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>CUBE Bikes France</title>
</head>
<body>
    <!-- <div id="cookieBanner" class="cookie-banner">
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
    </div> -->
    @include('layouts.header')

    <main>
        <section class="intro-carousel" id="intro-carousel">
            <div id="image-carousel" class="carousel">
                <img src="{{ asset('images/1.png') }}" alt="Image 1" class="carousel-image active">
                <img src="{{ asset('images/2.jpg') }}" alt="Image 2" class="carousel-image">
                <img src="{{ asset('images/3.jpg') }}" alt="Image 3" class="carousel-image">
            </div>

            <div class="text-intro">
                <p id="text1">GAMME 2026</p>
                <p class="text2">Nouveaux vélos.</p>
                <p class="text2 text-stylee">Même Passion.</p>
                <div>
                    <p id="text3">
                        Un design audacieux, de nouvelles innovations, et toujours la même passion pour la perfection. 
                    </p>
                </div>
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
                            <p id="first-p">Performance et légèreté pour les passionnés de vitesse. 
                                Dévorer du bitume, repousser vos limites, passer des cols en tête ou performer sur les routes pavées et sortir des sentiers battus sur votre gravel? Tout est possible!</p>
                          <a href="{{ url('/boutique/Musculaire/2') }}" class="btn">Voir les vélos de route</a>
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
                                  <a href="{{ url('/boutique/Musculaire/1') }}" class="btn">Voir les VTT</a>
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
                            <p>Assistance électrique pour des trajets sans effort. Nos VTT électriques ont des points communs qui font toute la différence. Au programme : géométrie optimale et équipement parfaitement étudié. Objectif : partir à l'aventure le week-end ou à l'assaut du quotidien, en toute sérénité.</p>
                                   <a href="{{ url('/boutique/Electrique') }}" class="btn">Voir les vélos électriques</a>
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
                    <a href="{{ url('/boutique/Accessoires') }}" class="btn">Voir les selles</a>
                </div>
                <div class="accessoire-card">
                    <div class="accessoire-image" style="background-image: url('{{ asset('images/roue.jpg') }}');">
                        <h3>Roue</h3>
                    </div>
                    <a href="{{ url('/boutique/Accessoires') }}" class="btn">Voir les roues</a>
                </div>
                <div class="accessoire-card">
                    <div class="accessoire-image" style="background-image: url('{{ asset('images/eclairage.jpg') }}');">
                        <h3>Éclairages</h3>
                    </div>
                    <a href="{{ url('/boutique/Accessoires') }}" class="btn">Voir les éclairages</a>
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
    <script src="{{ asset('js/cookies.js') }}" defer></script>
    
</body>

</html>