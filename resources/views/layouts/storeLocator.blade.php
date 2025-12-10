{{-- 1. SÉCURISATION DES DONNÉES (PHP) --}}
@php
    // On crée une variable locale sûre. Si $stock n'existe pas (Accueil), elle vaut null.
    $stockLocal = isset($stock) ? $stock : null;

    // Préparation du JSON pour la Map (JS)
    $jsonMagasins = $tousLesMagasins->map(function($magasin) use ($stockLocal) {
        $ad = $magasin->adresses->first();
        
        $enStock = false;
        // On utilise $stockLocal qui est sûr (soit null, soit la collection)
        if ($stockLocal) {
            $enStock = $stockLocal->flatMap->magasins->where('id_magasin', $magasin->id_magasin)->count() > 0;
        }

        return [
            'id' => $magasin->id_magasin,
            'nom' => $magasin->nom_magasin,
            'adresse' => $ad ? ($ad->rue . ' ' . $ad->code_postal . ' ' . $ad->ville) : '',
            'ville' => $ad ? $ad->ville : '',
            'stock' => $enStock
        ];
    })->values();
@endphp

{{-- 2. STRUCTURE DE LA MODALE (Overlay + Panel) --}}
<div id="store-locator-overlay" class="sl-overlay">
    <div class="sl-panel">
        
        {{-- HEADER DU PANEL --}}
        <div class="sl-header">
            <h2>CHOISIR UN MAGASIN</h2>
            <button onclick="toggleStoreLocator()" class="sl-close-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="sl-content">
            {{-- RECHERCHE --}}
            <div class="sl-search-box">
                <input type="text" id="storeSearchInput" placeholder="Saisir une adresse, un code postal...">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>

            {{-- SWITCH STOCK (Affiché uniquement si on est sur une page produit) --}}
            @if($stockLocal)
            <div class="sl-toggle-row">
                <label class="sl-switch">
                    <input type="checkbox" id="stockToggle">
                    <span class="sl-slider round"></span>
                </label>
                <span class="sl-toggle-label">VOIR UNIQUEMENT LES MAGASINS AYANT LE PRODUIT EN STOCK</span>
            </div>
            @endif

            {{-- ONGLETS LISTE / CARTE --}}
            <div class="sl-tabs">
                <button class="sl-tab active" onclick="switchView('list')">VUE LISTE</button>
                <button class="sl-tab" onclick="switchView('map')">VUE CARTE</button>
            </div>
            
            <p class="sl-disclaimer">Le stock est approximatif. Pour plus d'informations, veuillez contacter le magasin.</p>

            <div class="sl-views-wrapper" style="position: relative; flex-grow: 1; overflow: hidden;">
                
                {{-- VUE 1 : LISTE --}}
                <div id="view-list" class="sl-list-container custom-scroll">
                    @foreach($tousLesMagasins as $magasin)
                        @php
                            $adresse = $magasin->adresses->first();
                            $enStock = false;
                            
                            // Logique Stock Blade sécurisée
                            if ($stockLocal) {
                                $magasinDansStock = $stockLocal->flatMap->magasins->firstWhere('id_magasin', $magasin->id_magasin);
                                if($magasinDansStock && $magasinDansStock->pivot->quantite_stock_magasin > 0) {
                                    $enStock = true;
                                }
                            }
                            
                            // Création string recherche
                            $searchString = strtolower($magasin->nom_magasin . ' ' . ($adresse ? $adresse->ville . ' ' . $adresse->code_postal : ''));
                        @endphp

                        <div class="sl-card" 
                             data-has-stock="{{ $enStock ? 'true' : 'false' }}"
                             data-searchString="{{ $searchString }}">
                            
                            <div class="sl-card-header">
                                <h3>{{ $magasin->nom_magasin }}</h3>
                            </div>
                            
                            <div class="sl-card-body">
                                <div class="sl-card-info">
                                    {{-- Badge Stock (Seulement si page produit) --}}
                                    @if($stockLocal)
                                        @if($enStock)
                                            <div class="sl-stock-status">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00AEEF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Commandable en magasins
                                            </div>
                                        @else
                                            <div class="sl-stock-status" style="color: #999;">
                                                <span style="font-size:12px;">✖</span> Indisponible
                                            </div>
                                        @endif
                                    @endif

                                    @if($adresse)
                                        <p class="sl-address">{{ $adresse->ville }}, {{ $adresse->code_postal }}</p>
                                    @endif
                                </div>

                                <div class="sl-card-action">
                                    <form action="{{ route('magasin.definir') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id_magasin" value="{{ $magasin->id_magasin }}">
                                        <button type="submit" class="btn-skew-black">
                                            <span class="btn-content"><span class="arrow">▶</span> CHOISIR</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- VUE 2 : CARTE (Manquait dans votre fichier) --}}
                <div id="view-map" style="display: none; height: 100%; width: 100%;">
                    <div id="sl-map" style="height: 100%; width: 100%; background: #eee;"></div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- 3. INJECTION JS --}}
<script>
    window.magasinsData = @json($jsonMagasins);
</script>