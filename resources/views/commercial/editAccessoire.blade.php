<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>modification | {{ $accessoire->reference }}</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/editAccessoire.css') }}">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Modifier Accessoire</h1>
        <p class="title">Réf : {{ $accessoire->reference }}</p>

        {{-- Affichage des erreurs --}}
        @if ($errors->any())
            <div class="error-container">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('commercial.article.update', $accessoire->reference) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- NOM ARTICLE --}}
            <div class="form-group">
                <label for="nom_article" class="form-label">Nom de l'accessoire</label>
                <input type="text" name="nom_article" id="nom_article" class="form-input" 
                    value="{{ old('nom_article', $accessoire->nom_article) }}" required>
            </div>

            <div class="form-row">
                {{-- PRIX --}}
                <div class="form-col">
                    <div class="form-group">
                        <label for="prix" class="form-label">Prix (€)</label>
                        <input type="number" step="0.01" name="prix" id="prix" class="form-input" 
                            value="{{ old('prix', $accessoire->prix) }}" required>
                    </div>
                </div>

                {{-- POIDS --}}
                <div class="form-col">
                    <div class="form-group">
                        <label for="poids" class="form-label">Poids (kg)</label>
                        <input type="number" step="0.1" name="poids" id="poids" class="form-input" 
                            value="{{ old('poids', $accessoire->poids) }}" required>
                    </div>
                </div>
            </div>

            {{-- MATERIAU --}}
            <div class="form-group">
                <label for="materiau" class="form-label">Matériau</label>
                <input type="text" name="materiau" id="materiau" class="form-input" 
                    value="{{ old('materiau', $accessoire->materiau) }}" required>
            </div>

            {{-- DESCRIPTION --}}
            <div class="form-group">
                <label for="description" class="form-label">Description / Résumé</label>
                <textarea name="description" id="description" class="form-textarea" rows="6" required>{{ old('description', $accessoire->parent->resume->contenu_resume ?? '') }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
        </form>

        <div class="but-annuler">
            <a href="{{ route('commercial.edit.article') }}" class="link-annuler">Annuler</a>
        </div>
    </div>
</body>
</html>

