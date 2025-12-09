<div class="form-group">
    <label for="livraison">Type de livraison</label>
    <select name="livraison" id="livraison" class="form-control" required>
        <option value="magasin">Livraison en magasin</option>
        <option value="domicile">Livraison à domicile</option>
    </select>
</div>

<div id="magasin-section" class="form-group" style="display: none;">
    <label for="magasin_id">Choisir un magasin</label>
    <select name="magasin_id" id="magasin_id" class="form-control">
        <option value="">Sélectionnez un magasin</option>
        @foreach($magasins as $magasin)
            <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
        @endforeach
    </select>
</div>

<div id="domicile-section" class="form-group" style="display: none;">
    <label for="adresse_id">Choisir une adresse de livraison</label>
    <select name="adresse_id" id="adresse_id" class="form-control">
        <option value="">Sélectionnez une adresse</option>
        @foreach($adresses as $adresse)
            <option value="{{ $adresse->id }}">{{ $adresse->adresse }}</option>
        @endforeach
    </select>
</div>
