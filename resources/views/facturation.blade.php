<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CUBE Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}">
    <link rel="stylesheet" href="{{ asset('css/facturation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>
    @include('layouts.header')

    <section id="wrapper" class="container" style="margin-top: 10rem">
        <div class="row justify-content-center">
            <div id="content-wrapper" class="col-12 col-lg-8">
                <section class="register-form text-center">

                    @if ($errors->any())
                        <div class="alert alert-danger" style="text-align: left;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="js-customer-form needs-validation card card-account text-left" id="customer-form"
                        action="{{ route('facturation.send') }}" method="POST" autocomplete="off">
                        @csrf

                        <div id="address-section">

                            <h2 class="h4 font-weight-bold text-uppercase mb-4"
                                style="border-bottom: 2px solid #333; padding-bottom: 10px; text-align: left;">
                                Livraison
                            </h2>

                            <section class="form-fields">
                                <div class="form-group">
                                    <label class="required font-weight-bold" for="rue">Rue</label>
                                    <input class="form-control" name="rue" type="text" value="{{ old('rue') }}" id="rue"
                                    required placeholder="Ex: 10 rue de la paix...">
                                </div>

                                <div class="form-group">
                                    <label class="required font-weight-bold" for="zipcode">Code postal</label>
                                    <input class="form-control" name="zipcode" type="text" value="{{ old('zipcode') }}"
                                        id="zipcode" readonly required ly style="background-color: #e9ecef;">
                                </div>

                                <div class="form-group">
                                    <label class="required font-weight-bold" for="country">Pays</label>
                                    <input class="form-control" name="country" type="text" value="{{ old('country') }}"
                                        id="country" readonly required ly style="background-color: #e9ecef;">
                                </div>

                                <div class="form-group">
                                    <label class="required font-weight-bold" for="city">Ville</label>
                                    <input class="form-control" name="city" type="text" value="{{ old('city') }}"
                                        id="city" readonly required ly style="background-color: #e9ecef;">
                                </div>
                            </section>

                            <div class="form-group mt-3 custom-toggle-container">
                                <label class="custom-toggle-container" for="use_same_address" style="width: 100%;">
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="use_same_address" id="use_same_address" value="1"
                                            {{ old('use_same_address', '1') == '1' ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </div>
                                    <span class="toggle-label">Utiliser aussi comme adresse de facturation</span>
                                </label>
                            </div>

                            <div id="billing-address-wrapper">
                                <h2 class="h4 font-weight-bold text-uppercase mb-4 mt-4"
                                    style="border-bottom: 2px solid #333; padding-bottom: 10px; text-align: left;">
                                    Facturation
                                </h2>
                                <section class="form-fields">
                                    <div class="form-group">
                                        <label class="required font-weight-bold" for="billing_rue">Rue</label>
                                        <input class="form-control" name="billing_rue" type="text"
                                            value="{{ old('billing_rue') }}" id="billing_rue"
                                            placeholder="Ex: 10 rue de la paix...">
                                    </div>

                                    <div class="form-group">
                                        <label class="required font-weight-bold" for="billing_zipcode">Code postal</label>
                                        <input class="form-control" name="billing_zipcode" type="text"
                                            value="{{ old('billing_zipcode') }}" id="billing_zipcode"
                                            style="background-color: #e9ecef;" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="required font-weight-bold" for="billing_country">Pays</label>
                                        <input class="form-control" name="billing_country" type="text"
                                            value="{{ old('billing_country') }}" id="billing_country"
                                            style="background-color: #e9ecef;" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label class="required font-weight-bold" for="billing_city">Ville</label>
                                        <input class="form-control" name="billing_city" type="text"
                                            value="{{ old('billing_city') }}" id="billing_city"
                                            style="background-color: #e9ecef;" readonly>
                                    </div>
                                </section>
                            </div>

                        </div>
                        <footer class="form-footer mt-5">
                            <button class="btn btn-primary btn-valider w-100 py-3 text-uppercase" type="submit">
                                Continuer
                            </button>
                        </footer>
                    </form>
                </section>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
    <script src="{{ asset('js/facturation.js') }}" defer></script>

</body>

</html>