<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CUBE Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <style>
        @font-face {
            font-family: 'Damas Font';
            src: url('../font/font.woff2');
        }

        html {
            font-family: 'Damas Font', sans-serif;
        }

        .btn-next {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .btn-next:hover {
            background-color: #555;
        }

        #address-section {
            animation: fadeIn 0.5s;
        }

        .suggestion-adresse {
            position: absolute;
            border: none;
            z-index: 99999;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            display: none;
            top: 75px
        }

        .card-account {
            margin: 0 auto !important;
        }
    </style>
</head>

<body>
    @include('layouts.header')

    <section id="wrapper" class="container" style="margin-top: 10rem">
        <div class="row">
            <div id="content-wrapper" class="col-12 px-0">
                <section class="register-form text-center">

                    {{-- <h1 class="h3 register-form__title mt-5">Adresse Facturation</h1> --}}

                    @if ($errors->any())
                        <div class="alert alert-danger" style="color: red; text-align: left; margin-bottom: 20px;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        class="js-customer-form needs-validation card card-account text-left cube-validate-form customer-form"
                        id="customer-form" action="{{ route('facturation.send') }}" method="POST" autocomplete="off">
                        @csrf

                        <div id="address-section">
                            <h2 class="h3 font-weight-bold font-normal mt-4 mb-4">MON ADRESSE</h2>

                            <section class="form-fields">
                                {{-- Rue --}}
                                <div class="form-group">
                                    <label class="required" for="rue">Rue</label>
                                    <input class="form-control" name="rue" type="text" value="{{ old('rue') }}" id="rue"
                                        required>
                                </div>

                                {{-- Code Postal --}}
                                <div class="form-group">
                                    <label class="required" for="zipcode">Code postal</label>
                                    <input class="form-control" name="zipcode" type="text" value="{{ old('zipcode') }}"
                                        id="zipcode" required readonly>
                                </div>

                                {{-- Ville --}}
                                <div class="form-group">
                                    <label class="required" for="city">Ville</label>
                                    <input class="form-control" name="city" type="text" value="{{ old('city') }}"
                                        id="city" required readonly>
                                </div>

                                {{-- Pays --}}
                                <div class="form-group">
                                    <label class="required" for="country">Pays</label>
                                    <input class="form-control" name="country" type="text" value="{{ old('country') }}"
                                        id="country" required readonly>
                                </div>
                            </section>

                            <footer class="form-footer mt-4">
                                <button
                                    class="btn btn-primary btn-primary--red form-control-submit ml-md-3 btn-valider "
                                    type="submit">
                                    <span class="double-arrows double-arrows--white">Valider</span>
                                </button>
                            </footer>
                        </div>

                    </form>
                </section>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rueInput = document.getElementById('rue');
            const zipcodeInput = document.getElementById('zipcode');
            const cityInput = document.getElementById('city');
            const countryInput = document.getElementById('country');

            const suggestionBox = document.createElement('div');
            suggestionBox.classList.add('suggestion-adresse');

            rueInput.parentNode.style.position = 'relative';
            rueInput.parentNode.appendChild(suggestionBox);

            let timeout = null;

            rueInput.addEventListener('input', function () {
                const query = rueInput.value;
                clearTimeout(timeout);

                if (query.length < 3) {
                    suggestionBox.style.display = 'none';
                    return;
                }

                timeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5&autocomplete=1`);
                        const data = await response.json();

                        suggestionBox.innerHTML = '';

                        if (data.features && data.features.length > 0) {
                            data.features.forEach(item => {
                                const props = item.properties;
                                const label = props.label;

                                const div = document.createElement('div');
                                div.textContent = label;
                                div.style.backgroundColor = "white";
                                div.style.padding = '10px';
                                div.style.cursor = 'pointer';
                                div.style.borderBottom = '1px solid #eee';

                                div.addEventListener('mouseenter', () => div.style.backgroundColor = '#f0f0f0');
                                div.addEventListener('mouseleave', () => div.style.backgroundColor = 'white');

                                div.addEventListener('click', function () {
                                    rueInput.value = props.name;
                                    zipcodeInput.value = props.postcode;
                                    cityInput.value = props.city;
                                    countryInput.value = 'France';

                                    suggestionBox.style.display = 'none';
                                });

                                suggestionBox.appendChild(div);
                            });
                            suggestionBox.style.display = 'block';
                        } else {
                            suggestionBox.style.display = 'none';
                        }
                    } catch (error) {
                        console.error("Erreur API Adresse:", error);
                    }
                }, 300);
            });

        });
    </script>





</body>

</html>