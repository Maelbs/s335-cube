<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUBE Bikes France</title>
    
</head>
<body>
    <header>
        <nav>
        <div class="menu-category">
            <nav>
                <ul>
                    <li><a id="btn-velo" href="#">Vélos</a></li>
                    <li><a id="btn-elec" href="#">Vélos électriques</a></li>
                    <li><a id="btn-accessoire" href="#">Accessoires</a></li>
                </ul>
            </nav>
        </div>
        <div class="menu-user">
            
                <a id="magasin" href="{{ url('/login') }}">Choisir un magasin</a></li>
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
                    

                   
                    <a id="recherche" href="{{ url('/recherche') }}"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 20 20">
                        <g>
                            <path fill-rule="evenodd" d="M4.828 4.828A5 5 0 1 0 11.9 11.9a5 5 0 0 0-7.07-7.07Zm6.364 6.364a4 4 0 1 1-5.656-5.657a4 4 0 0 1 5.656 5.657Z" clip-rule="evenodd"/>
                            <path d="M11.192 12.627a1 1 0 0 1 1.415-1.414l2.828 2.829a1 1 0 1 1-1.414 1.414l-2.829-2.829Z"/>
                        </g>
                    </svg></a>
                 
                    <a id="login" href="{{ url('/login') }}"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24">
                        <g stroke-width="2">
                            <path stroke-linejoin="round" d="M4 18a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/>
                            <circle cx="12" cy="7" r="3"/>
                        </g>
                    </svg></a>
             
                    <a id="panier" href="{{ url('/panier') }}"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 48 48">
                        <g>
                            <path d="M39 32H13L8 12h36l-5 20Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M3 6h3.5L8 12m0 0l5 20h26l5-20H8Z"/>
                            <circle cx="13" cy="39" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"/>
                            <circle cx="39" cy="39" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"/>
                        </g>
                    </svg></a>
                </div>
        </div>
        </nav>
        <div id="mega-menu">
            <div class="contenu-centre">
                <ul id="liste-categories"></ul>
            </div>
        </div>
    </header>


    <main>
    
        <div id="image-carousel" class="carousel">
          <img src="{{ asset('images/1.png') }}" alt="Image 1" class="carousel-image active">
          <img src="{{ asset('images/2.jpg') }}" alt="Image 2" class="carousel-image">
          <img src="{{ asset('images/3.jpg') }}" alt="Image 3" class="carousel-image">
        </div>
    </main>


    <footer>
        <p>&copy; 2025 CUBE Bikes France</p>
    </footer>

</body>
    <script src="{{ asset('js/caroussel.js') }}" defer></script>
    <script src="{{ asset('js/header.js') }}" defer></script>
</html>