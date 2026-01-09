<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Vélo - CUBE</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/addModele.css') }}">
    <style>
        /* Styles mis à jour pour la grille de stock */
        .checkbox-grid {
            display: grid;
            /* On élargit un peu les colonnes pour faire tenir le champ nombre */
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 15px;
            margin-top: 5px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .checkbox-item {
            display: flex;
            flex-direction: column; /* Taille au dessus, input en dessous */
            gap: 5px;
            padding: 5px;
            border: 1px solid transparent;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .checkbox-item:hover {
            background: #fff;
            border-color: #eee;
        }
        .checkbox-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95em;
            cursor: pointer;
        }
        .qty-input {
            width: 100%;
            padding: 4px;
            font-size: 0.9em;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        /* Griser l'input quand désactivé */
        .qty-input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .text-muted { color: #888; font-size: 0.85em; }
        .hidden { display: none !important; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Mise en vente d'un Vélo</h1>
        
        @if ($errors->any())
            <div class="error-container">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('commercial.store.velo') }}" method="POST">
            @csrf

            {{-- SECTION 1 : IDENTIFICATION --}}
            <div class="form-row">
                <div class="form-col">
                    <label for="reference" class="form-label">Référence (Optionnel)</label>
                    <input type="text" name="reference" id="reference" class="form-input" 
                           placeholder="Laisser vide pour auto-génération" 
                           maxlength="6" 
                           pattern="\d{6}" 
                           value="{{ old('reference') }}">
                </div>
                <div class="form-col">
                    <label for="nom_article" class="form-label">Nom Commercial *</label>
                    <input type="text" name="nom_article" id="nom_article" class="form-input" placeholder="Ex: Cube Pi-pop Rouge" value="{{ old('nom_article') }}" required>
                </div>
            </div>

            {{-- SECTION 2 : PRIX ET POIDS --}}
            <div class="form-row">
                <div class="form-col">
                    <label for="prix" class="form-label">Prix (€) *</label>
                    <input type="number" step="0.01" name="prix" id="prix" class="form-input" placeholder="0.00" value="{{ old('prix') }}" required>
                </div>
                <div class="form-col">
                    <label for="poids" class="form-label">Poids (kg) *</label>
                    <input type="number" step="0.1" name="poids" id="poids" class="form-input" placeholder="0.0" value="{{ old('poids') }}" required>
                </div>
            </div>

            <hr>

            {{-- SECTION 3 : CONFIGURATION TECHNIQUE --}}
            <div class="form-row">
                <div class="form-col">
                    <label for="id_modele" class="form-label">Modèle Parent *</label>
                    <select name="id_modele" id="id_modele" class="form-select" required>
                        <option value="" disabled selected>Choisir un modèle...</option>
                        @foreach($modeles as $modele)
                            <option value="{{ $modele->id_modele }}" data-type="{{ strtolower(trim($modele->type_velo)) }}">
                                {{ $modele->nom_modele }} ({{ $modele->millesime_modele }}) - {{ ucfirst($modele->type_velo) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-col">
                    <label for="id_couleur" class="form-label">Couleur *</label>
                    <select name="id_couleur" id="id_couleur" class="form-select" required>
                        <option value="" disabled selected>Choisir une couleur...</option>
                        @foreach($couleurs as $couleur)
                            <option value="{{ $couleur->id_couleur }}">{{ $couleur->nom_couleur }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="id_fourche" class="form-label">Fourche *</label>
                    <select name="id_fourche" id="id_fourche" class="form-select" required>
                        <option value="" disabled selected>Choisir une fourche...</option>
                        @foreach($fourches as $fourche)
                            <option value="{{ $fourche->id_fourche }}">{{ $fourche->nom_fourche }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-col hidden" id="batterie-wrapper">
                    <label for="id_batterie" class="form-label">Batterie (Si VAE)</label>
                    <select name="id_batterie" id="id_batterie" class="form-select">
                        <option value="" selected>Aucune / Non applicable</option>
                        @foreach($batteries as $batterie)
                            <option value="{{ $batterie->id_batterie }}">
                                {{ $batterie->capacite_batterie }} Wh
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>

            {{-- SECTION 4 : STOCK ET TAILLES (MODIFIÉ) --}}
            <div class="form-group">
                <label class="form-label">Tailles & Stocks *</label>
                <div class="checkbox-grid">
                    @foreach($tailles as $taille)
                        <div class="checkbox-item">
                            <div class="checkbox-header">
                                <input type="checkbox" 
                                       id="taille_{{ $taille->id_taille }}" 
                                       name="tailles[]" 
                                       value="{{ $taille->id_taille }}"
                                       onchange="toggleQtyInput({{ $taille->id_taille }})">
                                
                                <label for="taille_{{ $taille->id_taille }}">
                                    <strong>{{ $taille->taille }}</strong>
                                    @if($taille->taille_min) 
                                        <span class="text-muted" style="font-size:0.7em">({{ $taille->taille_min }}-{{ $taille->taille_max }})</span> 
                                    @endif
                                </label>
                            </div>

                            <input type="number" 
                                   id="qty_{{ $taille->id_taille }}"
                                   name="stock[{{ $taille->id_taille }}]" 
                                   class="qty-input" 
                                   placeholder="Qté" 
                                   min="0" 
                                   value="0"
                                   disabled>
                        </div>
                    @endforeach
                </div>
                <small style="color: #666; margin-top:5px; display:block;">
                    Cochez une taille pour activer la saisie du stock. Si la quantité reste à 0, la taille ne sera pas créée.
                </small>
            </div>

            {{-- DESCRIPTION --}}
            <div class="form-group">
                <label for="description" class="form-label">Résumé technique *</label>
                <textarea name="description" id="description" class="form-textarea" rows="4" required>{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Mettre en vente</button>
        </form>
        
        <div class="but-annuler">
            <a href="{{ route('commercial.dashboard') }}" class="link-annuler">Annuler</a>
        </div>
    </div>

    {{-- SCRIPTS JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. GESTION BATTERIE
            const selectModele = document.getElementById('id_modele');
            const divBatterie = document.getElementById('batterie-wrapper');
            const selectBatterie = document.getElementById('id_batterie');

            function toggleBatterie() {
                if (selectModele.selectedIndex === -1 || selectModele.value === "") return;
                const typeVelo = selectModele.options[selectModele.selectedIndex].getAttribute('data-type');

                if (typeVelo === 'electrique') {
                    divBatterie.classList.remove('hidden');
                } else {
                    divBatterie.classList.add('hidden');
                    selectBatterie.value = ""; 
                }
            }
            selectModele.addEventListener('change', toggleBatterie);
            toggleBatterie();
        });

        // 2. GESTION STOCK PAR TAILLE (Appelé par onchange dans le HTML)
        function toggleQtyInput(idTaille) {
            const checkbox = document.getElementById('taille_' + idTaille);
            const inputQty = document.getElementById('qty_' + idTaille);

            if (checkbox.checked) {
                inputQty.disabled = false;
                inputQty.focus();
                // On met 1 par défaut si vide ou 0 pour aider l'utilisateur
                if(inputQty.value == 0) inputQty.value = 5; 
            } else {
                inputQty.disabled = true;
                inputQty.value = 0; // Reset si décoché
            }
        }
    </script>
</body>
</html>