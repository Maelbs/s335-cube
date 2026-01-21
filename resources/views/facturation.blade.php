<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - CUBE Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}">
    <link rel="stylesheet" href="{{ asset('css/facturation.css') }}">
</head>

<body>
    @include('layouts.header')

    <section id="wrapper" class="container" style="margin-top: 10rem">
        <div class="row justify-content-center">
            <div id="content-wrapper" class="col-12 col-lg-8">
                <section class="register-form text-center">
                    <form class="js-customer-form needs-validation card card-account text-left" id="customer-form"
                        action="{{ route('facturation.send') }}" method="POST" autocomplete="off">
                        @csrf

                        @include('layouts.formAdress')
                    </form>
                </section>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/header.js') }}" defer></script>
    <script src="{{ asset('js/facturation.js') }}" defer></script>

</body>

</html>