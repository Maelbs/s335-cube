<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Velo 1</title>
    <link rel="stylesheet" href="{{ asset('css/vizualize_article.css') }}">
     <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
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
                <div class="specs-column mt-10">
                    <div class="specs-header-row">
                        <h2>GÉOMÉTRIE</h2>
                        </div>

                    <div class="specs-content">
                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                    @foreach ($velo->varianteVelo->modele->geometries as $geometrie)
                                        <th>{{ $geometrie->nom_geometrie }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
        <div class="sidebar-column">
            <div class="badges">
                <span class="badge-new">NOUVEAU</span>
                <span class="badge-season">2025</span>
            </div>

            <h1 class="product-title">{{ $velo->nom_article }}</h1>
            <div class="product-ref">Réf: {{ $velo->reference ?? 'N/A' }}</div>

            <div class="size-selector">
                <p class="size-label">TAILLE</p>
                <div class="sizes-grid">
                    <button class="size-btn">XS</button>
                    <button class="size-btn">S</button>
                    <button class="size-btn selected">M</button>
                    <button class="size-btn">L</button>
                    <button class="size-btn">XL</button>
                </div>
            </div>

            <div class="price-section">
                <span class="price">899,00 € TTC</span>
            </div>

            <button class="btn-add-cart">AJOUTER AU PANIER</button>
        </div>

    </div>
</body>
</html>