<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Méta nécessaire pour sécuriser les requêtes AJAX Laravel --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $velo->varianteVelo->modele->nom_modele }}</title>
    
    <link rel="stylesheet" href="{{ asset('css/vizualize_article.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<body>
    @include('layouts.header')

    {{-- ========================================= --}}
    {{-- STRUCTURE DE LA MODALE (Cachée par défaut) --}}
    {{-- ========================================= --}}
    <div id="cartModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">×</button>
            
            <div class="modal-header">
                PRODUIT AJOUTÉ AU PANIER AVEC SUCCÈS
            </div>

            <div class="modal-body">
                {{-- Partie Gauche: Détails du produit ajouté --}}
                <div class="modal-product">
                    <div class="modal-img">
                        <img id="modalImg" src="" alt="Velo">
                    </div>
                    <div class="modal-details">
                        <h3 id="modalName">NOM DU VELO</h3>
                        <div class="modal-price" id="modalPrice">0,00 € TTC</div>
                        <div class="modal-meta">TAILLE : <span id="modalSize" style="font-weight:bold; color:black;"></span></div>
                        <div class="modal-meta">QUANTITÉ : <span id="modalQty" style="font-weight:bold; color:black;"></span></div>
                    </div>
                </div>

                {{-- Partie Droite: Résumé du panier --}}
                <div class="modal-summary">
                    <div style="margin-bottom: 15px; font-size: 0.9rem; color:#555;">
                        Il y a <span id="cartCount" style="font-weight:bold;"></span> articles dans votre panier.
                    </div>

                    <div class="summary-line">
                        <span>Sous-total :</span>
                        <span id="cartSubtotal" style="font-weight: bold;">0,00 €</span>
                    </div>
                    <div class="summary-line">
                        <span>Transport :</span>
                        <span>Gratuit</span>
                    </div>

                    <div class="summary-total">
                        <span>Total TTC</span>
                        <span id="cartTotal">0,00 €</span>
                    </div>
                    <div class="tax-info">
                        Taxes incluses : <span id="cartTax">0,00 €</span>
                    </div>

                    <div class="modal-actions">
                        <button onclick="closeModal()" class="btn-continue">
                            ◀ Continuer mes achats
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn-checkout">
                            Commander ▶
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- FIN STRUCTURE MODALE --}}


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
                                            @foreach($stockParIdTaille->sortBy('id_taille') as $inventaire)
                                                {{ $inventaire->taille->taille }}
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

                {{-- Section Géométrie --}}
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
                                    @php $tailles = $velo->varianteVelo->modele->tailles; @endphp
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
                                            <td>{{ $geoMatch ? $geoMatch->pivot->valeur_geometrie : '-' }}</td>
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
                        <p>{{ optional($velo->varianteVelo->resume)->contenu_resume ?? 'Description indisponible.' }}</p>
                    </div>
            </div>
        </div> 

        {{-- COLONNE DROITE (SIDEBAR) --}}
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
                            $stockWeb = $inventaire->quantite_stock_en_ligne;
                            $stockMag = $inventaire->magasins->sum('pivot.quantite_stock_magasin');
                            $classeCss = ($stockWeb <= 0) ? 'out-of-stock' : '';
                        @endphp

                        <button 
                            class="size-btn {{ $classeCss }}"
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

            {{-- Indicateurs de stock --}}
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
                {{-- FORMULAIRE D'AJOUT PANIER (AJAX) --}}
                <form id="form-ajout-panier" data-action="{{ route('cart.add', $velo->reference) }}" style="display: none;">
                    {{-- Pas de @csrf ici car on l'envoie via le header fetch --}}
                    <input type="hidden" name="taille" id="input-taille-selected" value="">
                    <input type="hidden" name="quantity" value="1">

                    {{-- Type button pour ne pas recharger la page, on appelle la fonction JS --}}
                    <button type="button" onclick="addToCartAjax()" id="btn-panier" class="btn-skew">
                        <span class="btn-content-unskew">
                            <span class="text-label">Ajouter au panier</span>
                        </span>
                    </button>
                </form>

                <button id="btn-contact-magasin" class="btn-skew" style="display: none;">
                    <span class="btn-content-unskew">
                        <span class="text-label">Contacter mon magasin</span>
                    </span>
                </button>

                <p id="msg-indisponible" style="display: none; color: red; font-weight: bold; margin-top:10px;">
                    Indisponible
                </p>
            </div>

            {{-- Sélecteur de variantes (Couleurs) --}}
            <div>
                <p class="size-label">Également disponible en</p>
                <div class="flex gap-2"> 
                    @php
                        $varianteActuelle = $velo->varianteVelo;
                        $tousLesCousins = $varianteActuelle->modele->varianteVelos ?? collect([]);
                        $nomRef = trim($varianteActuelle->nom_article);
                        $variantesFiltrees = $tousLesCousins->filter(function ($item) use ($nomRef) {
                            return trim($item->nom_article) === $nomRef;
                        });
                        $couleursUniques = $variantesFiltrees->unique('id_couleur');
                    @endphp

                    @foreach ($couleursUniques as $variante)
                        @php
                            $estActif = ($variante->id_couleur == $varianteActuelle->id_couleur);
                            $lien = route('velo.show', ['reference' => $variante->reference]); 
                            $hex = $variante->couleur->hexa_couleur ?? '000000'; 
                            $bg = str_starts_with($hex, '#') ? $hex : '#' . $hex;
                            $nom = $variante->couleur->nom_couleur ?? '';
                        @endphp

                        <a href="{{ $lien }}" title="{{ $nom }}" class="couleur-velo" 
                            style="display: inline-block; width: 30px; height: 30px; border-radius: 50%; margin-right: 5px;
                            border: {{ $estActif ? '5px solid #cbcbcb' : '1px solid #ddd' }};
                            background-color: {{ $bg }};">
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Sélecteur de batterie --}}
            @if($velo->varianteVelo->id_batterie)
            <div style="margin-top: 15px;">
                <p class="size-label">Batterie :</p>
                <div class="flex gap-2"> 
                    @php
                        $batteriesUniques = $variantesFiltrees
                            ->whereNotNull('id_batterie')
                            ->unique('id_batterie')
                            ->sortBy(function($v) {
                                return $v->batterie->puissance ?? $v->batterie->nom ?? 0;
                            });
                    @endphp

                    @foreach ($batteriesUniques as $variante)
                        @php
                            $estActif = ($variante->id_batterie == $varianteActuelle->id_batterie);
                            $lien = route('velo.show', ['reference' => $variante->reference]); 
                            $nomBatterie = $variante->batterie->puissance ?? $variante->batterie->capacite_batterie ?? 'Batterie';
                            if(is_numeric($nomBatterie)) { $nomBatterie .= ' Wh'; }
                        @endphp

                        <a href="{{ $lien }}" class="choix-batterie {{ $estActif ? 'active' : '' }}">
                            {{ $nomBatterie }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div> {{-- Fin Sidebar --}}
    </div> {{-- Fin Page Product Container --}}
    
    {{-- Produits Similaires --}}
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
                                @if(optional($similaire->varianteVeloss)->modele)
                                    <span class="st-prod-year">{{ optional($similaire->varianteVeloss)->modele->millesime_modele }}</span>
                                @endif
                                <div class="st-prod-price">{{ number_format($similaire->prix, 2, ',', ' ') }} €</div>
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

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/vizualize_article.js') }}" defer></script>
    <script>
        // 1. Gestion de l'affichage des boutons selon le stock
        function selectionnerTaille(tailleNom, qtyWeb, qtyMagasin) {
            console.log("Taille:", tailleNom, "| Web:", qtyWeb, "| Magasin:", qtyMagasin);

            // Mise à jour de l'input caché
            document.getElementById('input-taille-selected').value = tailleNom;

            const formPanier = document.getElementById('form-ajout-panier');
            const btnMagasin = document.getElementById('btn-contact-magasin');
            const msgIndispo = document.getElementById('msg-indisponible');
            const dotWeb = document.getElementById('dot-web');
            const textWeb = document.getElementById('text-web');
            const dotMagasin = document.getElementById('dot-magasin');
            const textMagasin = document.getElementById('text-magasin');

            // Logique Web
            if (qtyWeb > 0) {
                formPanier.style.display = 'inline-block';
                dotWeb.className = 'status-dot active-green';
                textWeb.textContent = "Disponible en ligne";
                textWeb.style.color = '#15803d';
            } else {
                formPanier.style.display = 'none';
                dotWeb.className = 'status-dot inactive-gray';
                textWeb.textContent = "Indisponible en ligne";
                textWeb.style.color = '#6b7280';
            }

            // Logique Magasin
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

            // Rupture totale
            if (qtyWeb <= 0 && qtyMagasin <= 0) {
                msgIndispo.style.display = 'block';
            } else {
                msgIndispo.style.display = 'none';
            }
        }

        // 2. Fonction AJAX pour ajouter au panier et ouvrir la modale
        function addToCartAjax() {
            const form = document.getElementById('form-ajout-panier');
            const url = form.getAttribute('data-action');
            const taille = document.getElementById('input-taille-selected').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if(!taille) {
                alert("Veuillez sélectionner une taille.");
                return;
            }

            // Envoi de la requête
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    taille: taille,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fillAndOpenModal(data);
                } else {
                    alert("Une erreur est survenue lors de l'ajout au panier.");
                }
            })
            .catch(error => console.error('Erreur:', error));
        }

        // 3. Remplir la modale avec les données reçues
        function fillAndOpenModal(data) {
            // Mise à jour infos produit
            document.getElementById('modalName').textContent = data.product.name;
            document.getElementById('modalPrice').textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data.product.price) + " TTC";
            document.getElementById('modalImg').src = data.product.image;
            document.getElementById('modalSize').textContent = data.product.taille;
            document.getElementById('modalQty').textContent = data.product.qty;

            // Mise à jour résumé panier
            document.getElementById('cartCount').textContent = data.cart.count;
            
            const formattedTotal = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data.cart.total);
            const formattedTax = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(data.cart.total * 0.2); // TVA approx 20%

            document.getElementById('cartSubtotal').textContent = formattedTotal;
            document.getElementById('cartTotal').textContent = formattedTotal;
            document.getElementById('cartTax').textContent = formattedTax;

            // Afficher la modale
            document.getElementById('cartModal').style.display = 'flex';
        }

        // 4. Fermer la modale
        function closeModal() {
            document.getElementById('cartModal').style.display = 'none';
        }

        // Fermer si on clique en dehors de la boîte blanche
        window.onclick = function(event) {
            const modal = document.getElementById('cartModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>