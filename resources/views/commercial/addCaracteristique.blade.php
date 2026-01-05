<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Caractéristiques - {{ $velo->nom_article }}</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/addModele.css') }}">
    <style>
        .specs-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Conteneur global pour la liste */
        .specs-list {
            background: #fff;
            padding: 20px;
            /* On affiche en grille : 2 colonnes pour gagner de la place, ou 1 seule si tu préfères */
            display: grid;
            grid-template-columns: 1fr; /* Une seule colonne de champs */
            gap: 15px; 
        }

        .spec-row {
            display: grid;
            grid-template-columns: 200px 1fr; /* Label fixe (200px) | Input (reste) */
            align-items: center;
            gap: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0; /* Petit trait de séparation discret */
        }

        .spec-label {
            font-size: 0.95em;
            color: #333;
            text-align: right;
            font-weight: 600;
        }

        .spec-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95em;
        }
        
        .spec-input:focus {
            border-color: #e30613;
            outline: none;
        }
    </style>
</head>
<body>
    <div class="form-container specs-container">
        
        <div style="margin-bottom: 20px;">
            <a href="{{ route('commercial.choix.caracteristique') }}" class="link-annuler">← Retour liste</a>
        </div>

        <h1 class="form-title">Fiche Technique : {{ $velo->nom_article }}</h1>
        <p style="text-align:center; color:#666; margin-bottom:20px;">
            Remplissez les valeurs. Laissez vide si inutile.
        </p>

        @if(session('error'))
            <div style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px;">{{ session('error') }}</div>
        @endif

        <form action="{{ route('commercial.store.caracteristique') }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="{{ $velo->reference }}">

            <div class="specs-list">
                {{-- BOUCLE UNIQUE SUR TOUTES LES CARACTÉRISTIQUES --}}
                @foreach($toutesLesCaracs as $carac)
                    <div class="spec-row">
                        <label class="spec-label" for="c_{{ $carac->id_caracteristique }}">
                            {{ $carac->nom_caracteristique }}
                        </label>
                        
                        <input type="text" 
                               class="spec-input" 
                               id="c_{{ $carac->id_caracteristique }}"
                               name="caracs[{{ $carac->id_caracteristique }}]"
                               placeholder="..."
                               value="{{ $valeursExistantes[$carac->id_caracteristique] ?? '' }}">
                    </div>
                @endforeach
            </div>

            <div class="form-row" style="margin-top: 30px; text-align: center;">
                <button type="submit" class="btn-submit" style="max-width: 300px;">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</body>
</html>