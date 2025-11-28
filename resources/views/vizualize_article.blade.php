<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Velo 1</title>
    <link rel="stylesheet" href="{{ asset('css/vizualize_article.css') }}">
</head>
<body>
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
                <div class="specs-header-row">
                    <h2>FICHE TECHNIQUE</h2>
                    <button class="toggle-specs-btn">-</button>
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
                <div class="geo-section">
                    <div class="geo-header">
                        <h2>GÉOMÉTRIE</h2>
                    </div>

                    <div class="table-responsive">
                        <table class="geo-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    @php 
                                        $tailles = $velo->varianteVelo->modele->tailles; 
                                    @endphp

                                    @foreach ($tailles as $taille)
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
            </div>
        </div> 
        <div class="sidebar-column">
            <div class="badges">
                <span class="badge-new">NOUVEAU</span>
                <span class="badge-season">{{ $velo->varianteVelo->modele->millesime_modele}}</span>
            </div>

            <h1 class="product-title">{{ $velo->nom_article }}</h1>
            <div class="product-ref">Réf: {{ $velo->reference ?? 'N/A' }}</div>

            <div class="size-selector">
                <p class="size-label">TAILLE</p>
                <div class="sizes-grid">
                    @foreach ($tailles as $taille)
                        <button class="size-btn">{{ $taille->taille }}</button>  
                    @endforeach
                </div>
            </div>

            <div class="price-section">
                <span class="price">{{ $velo->varianteVelo->prix }} € TTC</span>
            </div>

            <button class="btn-add-cart">AJOUTER AU PANIER</button>
        </div>

    </div>
</body>
</html>