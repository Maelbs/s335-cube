<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $velo->varianteVelo->modele->nom_modele }}</title>
        {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
        
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vizualize_article.css') }}">
</head>
<body>
    @include('layouts.header')

    <div class="page-product-container">
    
        <div class="left-column-wrapper">
            
            <div class="product-hero-section">
                @if($velo->photos->isNotEmpty())
                    <img src="{{ asset('storage/' . $velo->photos->first()->url) }}" alt="{{ $velo->nom_article }}">
                @else
                    <img src="https://placehold.co/800x500?text=Image+Non+Disponible" alt="Pas d'image">
                @endif
            </div>

            <div class="specs-column">
                <div class="each-specs-column">
                    <div class="specs-header-row">
                        <h2>FICHE TECHNIQUE</h2>
                        <button class="toggle-specs-btn"></button>
                    </div>
                    
                    <div class="specs-content">
                        @foreach($specifications as $typeNom => $caracteristiques)
                            <div class="spec-group-title">{{ $typeNom }}</div>
                            <div class="spec-group-list">
                                @if($loop->first)
                                    <div class="spec-row">
                                        <div class="spec-label">Tailles</div>
                                        <div class="spec-value">
                                            {{-- On trie par ID pour avoir l'ordre croissant (XS, S, M, L...) --}}
                                            @foreach($stockParIdTaille->sortBy('id_taille') as $inventaire)
                                                
                                                {{-- Affiche le nom de la taille --}}
                                                {{ $inventaire->taille->taille }}

                                                {{-- Ajoute un séparateur sauf à la fin --}}
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
                            </div>
                        @endforeach
                    </div>
                </div>
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
                                    @php 
                                        $tailles = $velo->varianteVelo->modele->tailles; 
                                    @endphp

                                    @foreach ($tailles->sortBy('id_taille') as $taille)
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
                                    $geometrieNonTriee = $velo->varianteVelo->modele->geometries;
                                    $geometrieTRiee = $geometrieNonTriee->groupBy('nom_geometrie');
                                @endphp

                                @foreach ($geometrieTRiee as $nomGeometrie => $listeDeValeurs)
                                    <tr>
                                        <td class="nom-geometrie">{{ $nomGeometrie }}</td>

                                        @foreach ($tailles as $taille)
                                            @php
                                                $geoMatch = $listeDeValeurs->first(function($geo) use ($taille) {
                                                    return $geo->pivot->id_taille == $taille->id_taille;
                                                });
                                            @endphp

                                            <td>
                                                {{ $geoMatch ? $geoMatch->pivot->valeur_geometrie : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="each-specs-column">
                    <div class="specs-header-row">
                        <h2>Description</h2>
                        <button class="toggle-specs-btn"></button>
                    </div>
                    <p>{{ $velo->varianteVelo->modele->description->texte_description }}</p>
                </div>
                <div class="each-specs-column" id="resume_container">
                    <div class="specs-header-row">
                        <h2>Résumé</h2>
                        <button class="toggle-specs-btn"></button>
                    </div>
                    <p>{{ $velo->varianteVelo->resume->contenu_resume}}</p>
                </div>
            </div>
        </div> 
        <div class="sidebar-column">
            <div class="badges">
                <span class="badge-season">Saison : {{ $velo->varianteVelo->modele->millesime_modele}}</span>
            </div>

            <h1 class="product-title">{{ $velo->nom_article }}</h1>
            <div class="product-ref">Référence : {{ $velo->reference}} | Poids : {{ $velo->poids }} kg | Matériau du cadre : {{ $velo->varianteVelo->modele->materiau_cadre }}</div>

            <div class="size-selector">
                <p class="size-label">TAILLE</p>
                
                <div class="sizes-grid">
                    @foreach ($stockParIdTaille as $inventaire)   
                        @php
                            // 1. Récupération des stocks
                            $stockWeb = $inventaire->quantite_stock_en_ligne;
                            // Calcul de la somme des stocks de tous les magasins liés (via la table pivot)
                            $stockMag = $inventaire->magasins->sum('pivot.quantite_stock_magasin');

                            // 2. Gestion de la classe CSS (si vide en ligne => grisé)
                            $classeCss = ($stockWeb <= 0) ? 'out-of-stock' : '';
                        @endphp

                        <button 
                            class="size-btn {{ $classeCss }}"
                            {{-- 3. C'EST ICI LE PLUS IMPORTANT : On passe les stocks au JS --}}
                            onclick="selectionnerTaille(
                                '{{ $inventaire->taille->taille }}', 
                                {{ $stockWeb }}, 
                                {{ $stockMag }}
                            )"
                        >
                            {{ $inventaire->taille->taille }} 
                            
                            <span style="font-size: 0.8em; display:block; font-weight: normal;">
                                ({{ $inventaire->taille->taille_min }}-{{ $inventaire->taille->taille_max }})
                            </span>
                        </button> 
                    @endforeach
                </div>
            </div>

            <div class="price-section">
                <span class="price">{{ $velo->varianteVelo->prix }} € TTC</span>
            </div>

            {{-- INDICATEURS DE DISPONIBILITÉ (Les ronds) --}}
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

            <div class="action-buttons-container">
                {{-- BOUTON 1 : PANIER (Noir, biseauté) --}}
                <button id="btn-panier" class="btn-skew" style="display: none;">
                    <span class="btn-content-unskew">
                        <span class="text-label">Ajouter au panier</span>
                    </span>
                </button>

                {{-- BOUTON 2 : MAGASIN (Blanc, biseauté) --}}
                <button id="btn-contact-magasin" class="btn-skew" style="display: none;">
                    <span class="btn-content-unskew">
                        <span class="text-label">Contacter mon magasin</span>
                    </span>
                </button>

                <p id="msg-indisponible" style="display: none; color: red; font-weight: bold; margin-top:10px;">
                    Indisponible
                </p>
            </div>

            <div>
                <p class="size-label">Également disponible en</p>

                <div class="flex gap-2"> 
                    @php
                        // 1. Variante actuelle
                        $varianteActuelle = $velo->varianteVelo;
                        
                        // 2. CORRECTION ICI : On ajoute le 's' pour correspondre à votre Modèle
                        $tousLesCousins = $varianteActuelle->modele->varianteVelos ?? collect([]);

                        // 3. Filtrage par nom (pour ne garder que les "Attention SL" et virer les "Attention Pro")
                        $nomRef = trim($varianteActuelle->nom_article);
                        
                        $variantesFiltrees = $tousLesCousins->filter(function ($item) use ($nomRef) {
                            // On compare les noms pour être sûr qu'on reste dans la même sous-famille
                            return trim($item->nom_article) === $nomRef;
                        });

                        // 4. Une seule pastille par couleur
                        $couleursUniques = $variantesFiltrees->unique('id_couleur');
                    @endphp

                    @foreach ($couleursUniques as $variante)
                        @php
                            // Vérification active
                            $estActif = ($variante->id_couleur == $varianteActuelle->id_couleur);
                            
                            // Lien vers la référence de cette variante
                            $lien = route('velo.show', ['reference' => $variante->reference]); 
                            
                            // Gestion couleur + Securité si pas de couleur
                            $hex = $variante->couleur->hexa_couleur ?? '000000'; 
                            $bg = str_starts_with($hex, '#') ? $hex : '#' . $hex;
                            $nom = $variante->couleur->nom_couleur ?? '';
                        @endphp

                        <a 
                            href="{{ $lien }}" 
                            title="{{ $nom }}"
                            class="couleur-velo" 
                            style="
                                display: inline-block; 
                                width: 30px; 
                                height: 30px; 
                                border-radius: 50%; 
                                margin-right: 5px;
                                border: {{ $estActif ? '5px solid #cbcbcb' : '1px solid #ddd' }};
                                background-color: {{ $bg }};
                            "
                        >
                        </a>
                    @endforeach
                </div>
            </div>


            {{-- On vérifie d'abord si le vélo actuel a une batterie (si c'est un vélo musculaire, on n'affiche rien) --}}
            @if($velo->varianteVelo->id_batterie)

            <div style="margin-top: 15px;">
                <p class="size-label">Batterie :</p>

                <div class="flex gap-2"> 
                    @php
                        $varianteActuelle = $velo->varianteVelo;
                        
                        // 1. On récupère tous les cousins via le Modèle
                        $tousLesCousins = $varianteActuelle->modele->varianteVelos ?? collect([]);

                        // 2. Filtrage par NOM (Exactement comme pour les couleurs)
                        // On veut comparer "Cube Stereo Hybrid 140" avec "Cube Stereo Hybrid 140"
                        $nomRef = trim($varianteActuelle->nom_article);
                        
                        $variantesFiltrees = $tousLesCousins->filter(function ($item) use ($nomRef) {
                            return trim($item->nom_article) === $nomRef;
                        });

                        // 3. Unique par BATTERIE
                        // On retire aussi ceux qui n'ont pas de batterie (id_batterie null)
                        $batteriesUniques = $variantesFiltrees
                            ->whereNotNull('id_batterie')
                            ->unique('id_batterie')
                            ->sortBy(function($v) {
                                // Optionnel : Trier par puissance (suppose que vous avez un champ puissance ou nom)
                                return $v->batterie->puissance ?? $v->batterie->nom ?? 0;
                            });
                    @endphp

                    @foreach ($batteriesUniques as $variante)
                        @php
                            $estActif = ($variante->id_batterie == $varianteActuelle->id_batterie);
                            $lien = route('velo.show', ['reference' => $variante->reference]); 
                            
                            // Récupération du nom de la batterie (adaptez 'puissance' selon votre table Batterie)
                            // Ex: '500 Wh', '625 Wh' ou 'PowerTube 750'
                            $nomBatterie = $variante->batterie->puissance ?? $variante->batterie->capacite_batterie ?? 'Batterie';
                            
                            // Ajout du suffixe "Wh" si ce n'est qu'un chiffre (optionnel, pour le style)
                            if(is_numeric($nomBatterie)) { $nomBatterie .= ' Wh'; }
                        @endphp

                        <a 
                            href="{{ $lien }}" 
                            class="choix-batterie {{ $estActif ? 'active' : '' }}" 
                        >
                            {{ $nomBatterie }}
                        </a>
                    @endforeach
                </div>
            </div>

            @endif

    </div>

    @if($velosSimilaires->isNotEmpty())
    <section class="st-similar-section">
        <h2 class="st-section-title">ARTICLES SIMILAIRES</h2>
        
        <div class="st-carousel-wrapper">
            <button class="st-nav-btn st-btn-left">❮</button>
            
            <div class="st-carousel-track">
                @foreach($velosSimilaires as $similaire)
                    <div class="st-card-item">
                        <a href="{{ route('velo.show', $similaire->reference) }}" class="st-card-link">
                            
                            <div class="st-img-box">
                                <img src="{{ $similaire->photo_principale }}" alt="{{ $similaire->nom_article }}">
                            </div>

                            <div class="st-info-box">
                                <h3 class="st-prod-name">{{ $similaire->nom_article }}</h3>
                                
                                @if(optional($similaire->varianteVelo)->modele)
                                    <span class="st-prod-year">
                                        {{ optional($similaire->varianteVelo)->modele->millesime_modele }}
                                    </span>
                                @endif
                                
                                <div class="st-prod-price">
                                    {{ number_format($similaire->prix, 2, ',', ' ') }} €
                                </div>
                            </div>

                            <div class="st-action-row">
                                <span class="st-view-btn">
                                    <i class="arrow-icon">▶</i> VOIR LE PRODUIT
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <button class="st-nav-btn st-btn-right">❯</button>
        </div>
    </section>
    @endif


    <script src="{{ asset('js/vizualize_article.js') }}" defer></script>
    <script>
        function selectionnerTaille(tailleNom, qtyWeb, qtyMagasin) {
            console.log("Taille:", tailleNom, "| Web:", qtyWeb, "| Magasin:", qtyMagasin);

            // 1. Éléments DOM
            const btnPanier = document.getElementById('btn-panier');
            const btnMagasin = document.getElementById('btn-contact-magasin');
            const msgIndispo = document.getElementById('msg-indisponible');

            const dotWeb = document.getElementById('dot-web');
            const textWeb = document.getElementById('text-web');
            const dotMagasin = document.getElementById('dot-magasin');
            const textMagasin = document.getElementById('text-magasin');

            // 2. LOGIQUE WEB
            if (qtyWeb > 0) {
                btnPanier.style.display = 'inline-block';
                dotWeb.className = 'status-dot active-green';
                textWeb.textContent = "Disponible en ligne";
                textWeb.style.color = '#15803d'; // Vert foncé
            } else {
                btnPanier.style.display = 'none';
                dotWeb.className = 'status-dot inactive-gray';
                textWeb.textContent = "Indisponible en ligne";
                textWeb.style.color = '#6b7280'; // Gris
            }

            // 3. LOGIQUE MAGASIN
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

            // 4. RUPTURE TOTALE
            if (qtyWeb <= 0 && qtyMagasin <= 0) {
                msgIndispo.style.display = 'block';
            } else {
                msgIndispo.style.display = 'none';
            }
        }
    </script>
    

</body>
</html>