<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser l'inscription - CUBE Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}">
    <link rel="stylesheet" href="{{ asset('css/facturation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<style>
    #title {
        justify-self: center
    }
</style>
<body>
    @include('layouts.header')

    <section id="wrapper" class="container" style="margin-top: 10rem">
        <div class="row justify-content-center">
            <div id="content-wrapper" class="col-12 col-lg-8">
                <section class="register-form text-center">

                    

                    @if ($errors->any())
                        <div class="alert alert-danger text-left">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="needs-validation card card-account text-left" 
                          action="{{ route('client.save_profile') }}" 
                          method="POST" 
                          autocomplete="off">
                        @csrf
                        <h2 class="mb-4" id="title">Bienvenue {{ Auth::user()->prenom_client }} !</h2>
                        <p class="mb-5">Pour finaliser votre compte, merci de renseigner vos informations de livraison ainsi que votre numéro de télephone.</p><br>

                        <div class="form-group mb-4">
                            <label class="required font-weight-bold" for="tel">Numéro de téléphone</label>
                            <input class="form-control" name="tel" type="tel" value="{{ old('tel') }}" id="tel" required placeholder="06 12 34 56 78">
                             @error('tel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="required font-weight-bold" for="date_naissance">Date de naissance</label>
                            <input class="form-control" name="date_naissance" type="date" 
                                value="{{ old('date_naissance', Auth::user()->date_naissance ? Auth::user()->date_naissance->format('Y-m-d') : '') }}" 
                                id="date_naissance" required>
                            @error('date_naissance') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <br>

                        @include('layouts.formAdress', ['submitText' => 'Terminer l\'inscription'])

                    </form>

                </section>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
    </body>
</html>