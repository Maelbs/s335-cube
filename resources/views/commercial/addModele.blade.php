<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un modèle</title>
    <link rel="stylesheet" href="{{ asset('css/commercial/addModele.css') }}">
</head>
<body>
    <div class="form-container">
    <h1 class="form-title">Nouveau Modèle de Vélo</h1>
    
    @if ($errors->any())
        <div class="error-container">
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('commercial.store.modele') }}" method="POST">
        @csrf

        {{-- LIGNE 1 : CLASSIFICATION --}}
        <div class="form-row">
            {{-- 1. TYPE DE VÉLO --}}
            <div class="form-col">
                <label for="type_velo" class="form-label">1. Type de Vélo</label>
                <select name="type_velo" id="type_velo" class="form-select" required>
                    <option value="" disabled selected>Choisir...</option>
                    <option value="musculaire">Musculaire</option>
                    <option value="electrique">Électrique (VAE)</option>
                </select>
            </div>

            {{-- 2. CATÉGORIE RACINE --}}
            <div class="form-col">
                <label for="root_category" class="form-label">2. Famille</label>
                <select id="root_category" class="form-select" disabled>
                    <option value="">En attente du type...</option>
                </select>
            </div>

            {{-- 3. SOUS-CATÉGORIE --}}
            <div class="form-col">
                <label for="sub_category_id" class="form-label">3. Catégorie</label>
                <select name="sub_category_id" id="sub_category_id" class="form-select" required disabled>
                    <option value="">En attente de la famille...</option>
                </select>
            </div>
        </div>

        <hr>

        {{-- LIGNE 2 : INFOS MODÈLE --}}
        <div class="form-group">
            <label for="nom_modele" class="form-label">Nom du Modèle</label>
            <input type="text" name="nom_modele" id="nom_modele" class="form-input" placeholder="Ex: Stereo Hybrid 140 HPC" required>
        </div>

        <div class="form-row">
            <div class="form-col">
                <label for="millesime" class="form-label">Millésime (Année)</label>
                <input type="text" name="millesime" id="millesime" class="form-input" placeholder="Ex: 2025" maxlength="4" required>
            </div>
            <div class="form-col">
                <label for="materiau" class="form-label">Matériau Cadre</label>
                <input type="text" name="materiau" id="materiau" class="form-input" placeholder="Ex: Carbone C:62, Aluminium..." required>
            </div>
        </div>

        {{-- DESCRIPTION --}}
        <div class="form-group">
            <label for="description" class="form-label">Description commerciale</label>
            <textarea name="description" id="description" class="form-textarea" rows="5" placeholder="Description détaillée du modèle qui apparaîtra sur la fiche produit..." required></textarea>
        </div>

        <button type="submit" class="btn-submit">Créer le Modèle</button>
    </form>
    
        <div class="but-annuler">
            <a href="{{ route('commercial.dashboard') }}" class="link-annuler">Annuler</a>
        </div>
    </div>
</body>
</html>





<script>
   
    const categoriesData = @json($categoriesVelos);

    const selectType = document.getElementById('type_velo');
    const selectRoot = document.getElementById('root_category');
    const selectSub  = document.getElementById('sub_category_id');

 
    selectType.addEventListener('change', function() {
      
        selectRoot.disabled = false;
        selectRoot.innerHTML = '<option value="" disabled selected>Choisir une famille...</option>';
        
       
        selectSub.disabled = true;
        selectSub.innerHTML = '<option value="">En attente de la famille...</option>';

     
        categoriesData.forEach((cat, index) => {
            let option = document.createElement('option');
            option.value = cat.id_categorie; 
            option.text = cat.nom_categorie;
            option.dataset.index = index;
            selectRoot.add(option);
        });
    });

  
    selectRoot.addEventListener('change', function() {
      
        selectSub.disabled = false;
        selectSub.innerHTML = '<option value="" disabled selected>Choisir une catégorie...</option>';

 
        const selectedIndex = this.options[this.selectedIndex].dataset.index;
        const selectedFamily = categoriesData[selectedIndex];

        if(selectedFamily.enfants && selectedFamily.enfants.length > 0) {
            selectedFamily.enfants.forEach(child => {
                let option = document.createElement('option');
                option.value = child.id_categorie;
                option.text = child.nom_categorie;
                selectSub.add(option);
            });
        } else {
            let option = document.createElement('option');
            option.text = "Pas de sous-catégorie";
            selectSub.add(option);
            selectSub.disabled = true;
        }
    });
</script>