<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $velo->varianteVelo->modele->nom_modele }}</title>
        {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/vizualize_article.css') }}">


</head>
<body>

    @include('layouts.header') 


    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
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
                            $classBtn = "";
                            if($inventaire->quantite_stock_en_ligne == 0) {
                                $classBtn = "indisponible-en-ligne";
                            }
                        
                        @endphp
                        <button 
                            class="size-btn {{ $classBtn }}"
                            onclick="selectionnerTaille('{{ $inventaire->taille->taille }}')"
                        >
                            {{ $inventaire->taille->taille }} 
                            
                            <span>
                                ({{ $inventaire->taille->taille_min }}-{{ $inventaire->taille->taille_max }})
                            </span>
                        </button> 
                        
                    @endforeach
                </div>
            </div>

            <div class="price-section">
                <span class="price">{{ $velo->varianteVelo->prix }} € TTC</span>
            </div>
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

            <button class="btn-add-cart">AJOUTER AU PANIER</button>

            <div>
                <p class="size-label">Également disponible en</p>

                <div class="flex gap-2"> 
                    @php
                        $autresVariantes = $velo->varianteVelo->modele->varianteVelos;
                        $couleursUniques = $autresVariantes->unique('id_couleur');
                    @endphp

                    @foreach ($couleursUniques as $variante)
                        @php
                            // Si c'est la même couleur que le vélo actuel, on passe au suivant
                            if($variante->id_couleur == $velo->varianteVelo->id_couleur) {
                                continue; 
                            }

                            $lienArticle = route('velo.show', ['reference' => $variante->reference]); 
                        @endphp

                        <a 
                            href="{{ $lienArticle }}" 
                            title="{{ $variante->couleur->nom_couleur ?? '' }}"
                            class="couleur-velo" 
                            style="
                                display: inline-block; 
                                width: 30px; 
                                height: 30px; 
                                border-radius: 50%; 
                                border: 1px solid #ddd;
                                background-color: {{ str_starts_with($variante->couleur->hexa_couleur, '#') ? '' : '#' }}{{ $variante->couleur->hexa_couleur }};
                            "
                        >
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

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
                            
                            {{-- Image --}}
                            <div class="st-img-box">
                                <img src="{{ $similaire->photo_principale }}" alt="{{ $similaire->nom_article }}">
                            </div>

                            {{-- Infos --}}
                            <div class="st-info-box">
                                <h3 class="st-prod-name">{{ $similaire->nom_article }}</h3>
                                
                                @if(optional($similaire->varianteVelo)->modele)
                                    <span class="st-prod-year">{{ optional($similaire->varianteVelo)->modele->millesime_modele }}</span>
                                @endif
                                
                                <div class="st-prod-price">{{ number_format($similaire->prix, 2, ',', ' ') }} €</div>
                            </div>

                            {{-- Bouton --}}
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

</body>
</html>