<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mes informations | Cube Bikes</title>
    
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inscription.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">      

    <style>
        body {
            background: #f1f1f1 !important;
            overflow-y: auto !important; 
        }
        
        .dashboard-form-override {
            margin: 0 !important;
            max-width: 100% !important;
            box-shadow: none !important;
            background-color: #fff !important; 
            padding: 40px !important;
        }

        .form-footer .text-connexion, .form-footer .description {
            display: none;
        }

        .error-border {
            border: 2px solid red;
        }

        .error-text {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @include('layouts.header')

    <div class="dashboard-container">
        @include('layouts.sideBar')
        <main class="main-content">
            
            <div class="breadcrumb">
                <a href="{{ route('profil') }}" style="text-decoration: none; color: inherit;">MON COMPTE</a> 
                <span class="separator">></span> 
                MODIFIER MES INFORMATIONS
            </div>

            <form class="js-customer-form needs-validation card card-account cube-validate-form customer-form dashboard-form-override" action="{{ route('profil.update') }}" id="customer-form" method="POST" novalidate> 
                @csrf
                @method('PUT') 
                <h2 class="h3 font-weight-bold font-normal mb-4" style="color: #000;">ÉDITER MON PROFIL</h2>

                <section class="form-fields">
                    
                    <div class="form-group lastname">
                        <label class="required" for="lastname">Nom</label>
                        <input class="form-control @error('lastname') error-border @enderror"
                            name="lastname" type="text" 
                            value="{{ old('lastname', $client->nom_client) }}" 
                            id="lastname" required autocomplete="family-name">
                        @error('lastname') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group firstname">
                        <label class="required" for="firstname">Prénom</label>
                        <input class="form-control @error('firstname') error-border @enderror"
                            name="firstname" type="text" 
                            value="{{ old('firstname', $client->prenom_client) }}" 
                            id="firstname" required autocomplete="given-name">
                        @error('firstname') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group email">
                        <label class="required" for="email">E-mail</label>
                        <input class="form-control @error('email') error-border @enderror" 
                            name="email" type="email" 
                            value="{{ old('email', $client->email_client) }}" 
                            id="email" required autocomplete="email">
                        @error('email') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group password">
                        <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') error-border @enderror" placeholder="Nouveau mot de passe">
                        @error('password') 
                            <div class="error-text">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="form-group password">
                        <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirmer le mot de passe">
                    </div>

                    <div class="form-group phone">
                        <label class="required" for="tel">Téléphone</label>
                        <input class="form-control @error('tel') error-border @enderror" name="tel"
                            type="tel" value="{{ old('tel', $client->tel) }}" 
                            id="tel" placeholder="0612345678" required>
                        @error('tel') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group birthday">
                        <label class="required" for="birthday">Date de naissance</label>
                        <input class="form-control @error('birthday') error-border @enderror"
                            name="birthday" type="date" 
                            value="{{ old('birthday', optional($client->date_naissance)->format('Y-m-d')) }}" 
                            id="birthday" required>
                        @error('birthday') <div class="error-text">{{ $message }}</div> @enderror
                    </div>

                </section>

                <footer class="form-footer" style="margin-top: 40px; text-align: left; padding: 0;">
                    <button class="btn-valider" type="submit" style="width: auto; min-width: 300px;">
                        <span class="double-arrows double-arrows--white">Enregistrer les modifications</span>
                    </button>
                    
                    <a href="{{ route('profil') }}" style="display: inline-block; margin-left: 20px; color: #666; text-decoration: underline;">
                        Annuler
                    </a>
                </footer>

            </form>

        </main>
    </div>

    <script src="{{ asset('js/header.js') }}" defer></script>
    <script src="{{ asset('js/inscription.js') }}" defer></script>
</body>
</html>