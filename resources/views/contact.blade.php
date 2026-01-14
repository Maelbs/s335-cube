<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous | CUBE Bikes France</title>

    
    {{-- CSS du Header existant --}}
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    <style>
        /* --- STYLE SPÉCIFIQUE CONTACT --- */
        @font-face {
            font-family: 'Damas Font';
            src: url('../font/font.woff2');
            font-display: swap;
        }

        header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e5e5;
            color: #000000 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .logo img {
            filter: brightness(0) !important;
        }
        
        body {
            font-family: 'Damas Font', sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
        }

        .contact-container {
            max-width: 1100px;
            margin: 140px auto 60px; /* Marge pour le header fixed */
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1.5fr; /* 2 Colonnes : Infos / Formulaire */
            gap: 50px;
        }

        /* --- COLONNE GAUCHE : INFOS --- */
        .contact-info {
            background-color: #f8f9fa;
            padding: 40px;
            border-radius: 8px;
            height: fit-content;
        }

        .contact-info h2 {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 30px;
            color: #333;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .info-icon {
            font-size: 22px;
            color: #00b0f0;
            margin-right: 15px;
            margin-top: 3px;
            width: 30px;
        }

        .info-content h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-content p {
            margin: 0;
            color: #666;
            font-size: 15px;
            line-height: 1.5;
        }

        /* --- COLONNE DROITE : FORMULAIRE --- */
        .contact-form h1 {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            color: #444;
            font-size: 14px;
            text-transform: uppercase;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Segoe UI', sans-serif; /* Plus lisible pour écrire */
            font-size: 15px;
            transition: border-color 0.3s;
            background: #fdfdfd;
        }

        .form-input:focus, .form-textarea:focus {
            border-color: #00b0f0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 176, 240, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            background-color: #00b0f0;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background-color: #008cb0;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 176, 240, 0.3);
        }

        /* --- MESSAGES ALERTE --- */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 5px solid #28a745;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
                margin-top: 120px;
            }
            .contact-info {
                order: 2; /* Infos en bas sur mobile */
            }
            .contact-form {
                order: 1;
            }
        }
    </style>
</head>
<body>

    @include('layouts.header')

    <div class="contact-container">

        {{-- COLONNE GAUCHE : INFOS --}}
        <div class="contact-info">
            <h2>Nos Coordonnées</h2>

            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="info-content">
                    <h3>Siège Social</h3>
                    <p>7 avenue des Romains<br>74000 Annecy, France</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                <div class="info-content">
                    <h3>Email</h3>
                    
                    <p>support@cube-france.fr</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                <div class="info-content">
                    <h3>Téléphone</h3>
                    <p>06 47 96 17 19</p>
                    <p>Lun - Ven : 09h00 - 17h00</p>
                </div>
            </div>

            <div style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
                <p style="font-size: 14px; color: #777;">
                    <em>Note : Pour les questions techniques ou le SAV, merci de privilégier le contact direct avec votre revendeur agréé CUBE.</em>
                </p>
            </div>
        </div>

        {{-- COLONNE DROITE : FORMULAIRE --}}
        <div class="contact-form">
            <h1>Contactez-nous</h1>
            <p class="subtitle">Une question ? Un projet ? Remplissez le formulaire ci-dessous, notre équipe vous répondra dans les plus brefs délais.</p>

            {{-- MESSAGE DE SUCCÈS --}}
            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- FORMULAIRE --}}
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf {{-- Très important pour la sécurité Laravel --}}

                <div class="form-group">
                    <label for="nom" class="form-label">Nom complet *</label>
                    <input type="text" id="nom" name="nom" class="form-input" placeholder="Votre nom" value="{{ old('nom') }}" required>
                    @error('nom') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Adresse Email *</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="exemple@email.com" value="{{ old('email') }}" required>
                    @error('email') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="sujet" class="form-label">Sujet de votre demande *</label>
                    <select id="sujet" name="sujet" class="form-input" required>
                        <option value="" disabled selected>-- Sélectionnez un sujet --</option>
                        <option value="Information Produit">Information sur un vélo / accessoire</option>
                        <option value="Commande">Question sur ma commande</option>
                        <option value="Partenariat">Demande de partenariat / collaboration</option>
                        <option value="Autre">Autre demande</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Votre message *</label>
                    <textarea id="message" name="message" class="form-textarea" placeholder="Dites-nous en plus..." required>{{ old('message') }}</textarea>
                    @error('message') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn-submit">
                    Envoyer le message <i class="fa-solid fa-paper-plane"></i>
                </button>

            </form>
        </div>

    </div>

    <script src="{{ asset('js/header.js') }}"></script>

</body>
</html>