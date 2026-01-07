<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styleBody.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cookies.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>CUBE Bikes France</title>
</head>
<body>
    @include('layouts.header') 
<div class="cookie-overlay" id="cookieOverlay">
    <div class="cookie-modal" id="cookieModal">
    
    <div id="step1-container" style="display: flex; width: 100%; height: 100%;">
        <div class="cookie-col-left" style="background-image: url('{{ asset('images/cookies-bg.jpg') }}');"></div>

        <div class="cookie-col-right">
            <div class="cookie-header">
                <img src="{{ asset('images/cubelogo.png') }}" alt="CUBE" class="cookie-logo">
                <a href="#" class="link-refuse" onclick="refuseCookies()">Continuer sans accepter &rarr;</a>
            </div>

            <div class="cookie-body">
                <p>
                    Nous utilisons des cookies pour assurer le bon fonctionnement du site et, avec votre accord, pour analyser notre trafic anonymement.
                </p>
                <p class="cookie-bold-title">Pourquoi ?</p>
                <p class="cookie-small-text">
                    Cela nous permet de savoir combien de personnes visitent CUBE Bikes France afin d'améliorer nos services. Aucune donnée n'est revendue à des tiers.
                </p>
            </div>

            <div class="cookie-footer">
                <button class="btn-cookie btn-grey" onclick="goToStep2()">PERSONNALISER &rarr;</button>
                <button class="btn-cookie btn-blue" onclick="acceptCookies()">ACCEPTER & FERMER</button>
            </div>
        </div>
    </div>

    <div id="step2-container" class="step2-wrapper" style="display: none;">

        <div class="step2-header">
            <h2 class="step2-title">Gestion des cookies</h2>
            <span class="step2-close" onclick="closeModal()">&#10005;</span>
        </div>

        <div class="step2-scroll-area">
            <p class="step2-intro">
                Vous pouvez choisir d'activer ou non la mesure d'audience. Les cookies nécessaires au fonctionnement technique (panier, session) ne peuvent pas être désactivés.
            </p>

            <div class="step2-section-label">VOS PRÉFÉRENCES</div>

            <div class="accordion-group">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span class="acc-title"><span class="icon">+</span> Nécessaires</span>
                    <span class="status-required">REQUIS</span>
                </div>
                <div class="accordion-body">
                    <div class="sub-item-row">
                        <div class="sub-text">Cookies techniques (Session, Sécurité)</div>
                        <span class="status-required">REQUIS</span>
                    </div>
                </div>
            </div>

            <div class="accordion-group">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span class="acc-title"><span class="icon">+</span> Mesure d'audience</span>
                    <div class="toggle-container" onclick="event.stopPropagation()">
                        <button class="toggle-btn" onclick="toggleGlobal(this, 'refuse')">Refuser</button>
                        <button class="toggle-btn selected" onclick="toggleGlobal(this, 'accept')">Accepter</button>
                    </div>
                </div>
                <div class="accordion-body">
                    <p class="section-desc">Nous comptabilisons les visites pour améliorer l'expérience utilisateur.</p>
                    
                    <div class="sub-item-row">
                        <div class="sub-text">Statistiques de visite anonymes</div>
                        <div class="toggle-container">
                            <button class="toggle-btn" onclick="toggleOption(this)">Refuser</button>
                            <button class="toggle-btn selected" onclick="toggleOption(this)">Accepter</button>
                        </div>
                    </div>
                </div>
            </div>

            </div> 
        
        <div class="step2-footer">
            <div class="logo-didomi">
                <span style="font-weight:bold; font-size:10px; color:#555;">PROTECTION VIE PRIVÉE</span>
            </div>
            <div class="footer-actions">
                <button class="btn-save-choices" onclick="acceptCookies()">ENREGISTRER</button>
            </div>
        </div>
    </div>
</div>
</div>

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