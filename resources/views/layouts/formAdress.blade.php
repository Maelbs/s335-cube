<div id="address-section">
    <h2 class="h4 font-weight-bold text-uppercase mb-4" style="border-bottom: 2px solid #333; padding-bottom: 10px; text-align: left;">
        Livraison
    </h2>

    <section class="form-fields">
        <div class="form-group">
            <label class="required font-weight-bold" for="rue">Rue</label>
            <input class="form-control" name="rue" type="text" value="{{ old('rue', $user->adresse->rue ?? '') }}" id="rue" required placeholder="Ex: 10 rue de la paix...">
            @error('rue') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="required font-weight-bold" for="zipcode">Code postal</label>
            <input class="form-control" name="zipcode" type="text" value="{{ old('zipcode') }}" id="zipcode" readonly required style="background-color: #e9ecef;">
            @error('code postal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="required font-weight-bold" for="country">Pays</label>
            <input class="form-control" name="country" type="text" value="{{ old('country') }}" id="country" readonly required style="background-color: #e9ecef;">
            @error('pays') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="required font-weight-bold" for="city">Ville</label>
            <input class="form-control" name="city" type="text" value="{{ old('city') }}" id="city" readonly required style="background-color: #e9ecef;">
            @error('ville') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </section>

    <div class="form-group mt-3 custom-toggle-container">
        <label class="custom-toggle-container" for="use_same_address" style="width: 100%;">
            <div class="toggle-switch">
                <input type="checkbox" name="use_same_address" id="use_same_address" value="1" {{ old('use_same_address', '1') == '1' ? 'checked' : '' }}>
                <span class="slider"></span>
            </div>
            <span class="toggle-label">Utiliser aussi comme adresse de facturation</span>
        </label>
    </div>

    <div id="billing-address-wrapper">
        <h2 class="h4 font-weight-bold text-uppercase mb-4 mt-4" style="border-bottom: 2px solid #333; padding-bottom: 10px; text-align: left;">
            Facturation
        </h2>
        <section class="form-fields">
            <div class="form-group">
                <label class="required font-weight-bold" for="billing_rue">Rue</label>
                <input class="form-control" name="billing_rue" type="text" value="{{ old('billing_rue') }}" id="billing_rue" placeholder="Ex: 10 rue de la paix...">
            </div>

            <div class="form-group">
                <label class="required font-weight-bold" for="billing_zipcode">Code postal</label>
                <input class="form-control" name="billing_zipcode" type="text" value="{{ old('billing_zipcode') }}" id="billing_zipcode" style="background-color: #e9ecef;" readonly>
            </div>

            <div class="form-group">
                <label class="required font-weight-bold" for="billing_country">Pays</label>
                <input class="form-control" name="billing_country" type="text" value="{{ old('billing_country') }}" id="billing_country" style="background-color: #e9ecef;" readonly>
            </div>

            <div class="form-group">
                <label class="required font-weight-bold" for="billing_city">Ville</label>
                <input class="form-control" name="billing_city" type="text" value="{{ old('billing_city') }}" id="billing_city" style="background-color: #e9ecef;" readonly>
            </div>
        </section>
    </div>
</div>

<footer class="form-footer mt-5">
    <button class="btn btn-primary btn-valider w-100 py-3 text-uppercase" type="submit">
        {{ $submitText ?? 'Continuer' }}
    </button>
</footer>

<script src="{{ asset('js/facturation.js') }}" defer></script>