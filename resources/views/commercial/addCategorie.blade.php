<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/addCategorie.css') }}">
</head>
    <body>
        <div class="form-container">
            <h1 class="form-title">Nouvelle Sous-Catégorie</h1>
            @if ($errors->any())
                <div class="error-container">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('commercial.store.categorie') }}" method="POST">
                @csrf

                {{-- 1. TYPE D'ARTICLE --}}
                <div class="form-group">
                    <label for="type_article" class="form-label">Type de produit concerné</label>
                    <select name="type_article" id="type_article" class="form-select" required>
                        <option value="" disabled selected>Choisir un type...</option>
                        <option value="Musculaire">Vélos Musculaires</option>
                        <option value="Electrique">Vélos Électriques (VAE)</option>
                        <option value="Accessoires">Accessoires</option>
                    </select>
                </div>

                {{-- 2. CATÉGORIE PARENTE --}}
                <div class="form-group">
                    <label for="parent_id" class="form-label">Catégorie Parente</label>
                    <select name="parent_id" id="parent_id" class="form-select" required disabled>
                        <option value="">Sélectionnez d'abord un type</option>
                    </select>
                    <small style="color:#888; display:block; margin-top:5px;">
                        Exemple : Choisir "VTT" pour créer "Tout-Suspendu".
                    </small>
                </div>

                {{-- 3. NOM --}}
                <div class="form-group">
                    <label for="nom_categorie" class="form-label">Nom de la nouvelle catégorie</label>
                    <input type="text" name="nom_categorie" id="nom_categorie" class="form-input" placeholder="Ex: Tout-Suspendu, Casques, Eclairage..." required>
                </div>

                <button type="submit" class="btn-submit">Ajouter la catégorie</button>
            </form>

            <a href="{{ route('commercial.dashboard') }}" class="back-link">Annuler</a>
        </div>
    </body>
</html>




<script>
    // On reçoit les parents VELOS (communs) et ACCESSOIRES
    const dataVelos = @json($parentsVelos);
    const dataAccessoires = @json($parentsAccessoires);

    const selectType = document.getElementById('type_article');
    const selectParent = document.getElementById('parent_id');

    selectType.addEventListener('change', function() {
        // Reset du menu
        selectParent.innerHTML = '<option value="" disabled selected>Choisir une catégorie parente...</option>';
        selectParent.disabled = false;

        let optionsData = [];

        // Logique corrigée : Musculaire et Electrique partagent les mêmes catégories racines
        if (this.value === 'Musculaire' || this.value === 'Electrique') {
            optionsData = dataVelos;
        } else if (this.value === 'Accessoires') {
            optionsData = dataAccessoires;
        }

        // Remplissage du menu
        optionsData.forEach(cat => {
            let id, nom;
            
            if (this.value === 'Accessoires') {
                id = cat.id_categorie_accessoire;
                nom = cat.nom_categorie_accessoire;
            } else {
                id = cat.id_categorie;
                nom = cat.nom_categorie;
            }

            let option = document.createElement('option');
            option.value = id;
            option.textContent = nom;
            selectParent.appendChild(option);
        });
    });
</script>