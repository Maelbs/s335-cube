<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mon Compte | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        
        .info-bubble {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #00ecff, #00b4d8);
            color: #000 !important;
            border-radius: 50%;
            text-align: center;
            font-size: 10px;
            font-weight: 900;
            margin-left: 8px;
            cursor: help;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 236, 255, 0.3);
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            vertical-align: middle;
            font-style: normal !important;
            text-transform: none !important;
            letter-spacing: normal !important;
        }

        .info-bubble::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 150%; 
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            width: 220px;
            padding: 12px 16px;
            background-color: rgba(15, 15, 15, 0.98); 
            color: #ffffff !important;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.15);
            pointer-events: none;
            visibility: hidden; opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: sans-serif !important;
            font-size: 12px !important;
            font-weight: 400 !important;
            line-height: 1.4 !important;
            text-transform: none !important;
            text-align: left;
            white-space: normal;
        }

        .info-bubble::before {
            content: "";
            position: absolute;
            bottom: 130%; left: 50%;
            transform: translateX(-50%);
            border-width: 7px;
            border-style: solid;
            border-color: rgba(15, 15, 15, 0.98) transparent transparent transparent;
            visibility: hidden; opacity: 0;
            z-index: 9999;
        }

        .info-bubble:hover::after, .info-bubble:hover::before {
            visibility: visible; opacity: 1; transform: translateX(-50%) translateY(0);
        }

        @keyframes bubble-pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 236, 255, 0.5); }
            70% { box-shadow: 0 0 0 6px rgba(0, 236, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 236, 255, 0); }
        }
        .info-bubble { animation: bubble-pulse 2s infinite; }

        .card-header h2 { display: flex; align-items: center; }


        
    .modal-overlay {
            display: none; 
            position: fixed;
            z-index: 9999; 
            left: 0;
            top: 0;     
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8); 
            backdrop-filter: blur(4px); 
        }

        .modal-box {
            background-color: #222; 
            color: #fff;
            margin: 10% auto;
            padding: 25px;
            border: 1px solid ; 
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
            font-family: sans-serif;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #aaa;
            font-size: 24px;
            cursor: pointer;
            font-weight: bold;
        }
        .close-btn:hover { color: #fff; }

        .data-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #444;
        }

        .btn-suppression {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #ff4d4d;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }       
        .btn-suppression:hover { background-color: #ff3333; }


        .card-clickable {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .card-clickable:hover {
            transform: scale(1.02);
            border-color: #00ecff;
        }
    </style>
</head>
<body>
    @include('layouts.header')

    <div class="dashboard-container">
        @include('layouts.sideBar')
        <main class="main-content @yield('scroll')">
            
            <div class="breadcrumb">
                ACCUEIL <span class="separator">></span> VOTRE COMPTE
            </div>

            <div class="dashboard-grid">
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>MON PROFIL</h2>
                        <p class="subtitle">{{ $client->prenom_client }} {{ $client->nom_client }}</p>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-group">
                            <label>Nom :</label>
                            <span>{{ strtoupper($client->nom_client) }}</span>
                        </div>
                        <div class="info-group">
                            <label>Prénom :</label>
                            <span>{{ ucfirst($client->prenom_client) }}</span>
                        </div>
                        <div class="info-group">
                            <label>E-mail :</label>
                            <span>{{ $client->email_client }}</span>
                        </div>
                        
                        <a href="{{ route('profil.update.form') }}" class="card-action">Modifier mes informations</a>                    
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>
                            MES COMMANDES
                            <span class="info-bubble" data-tooltip="Retrouvez ici l'historique de vos achats et téléchargez vos factures au format PDF.">?</span>
                        </h2>
                    </div>
                    <div class="card-body centered-content">
                        <a href="/commandes" class="card-arrow-btn">➜ Voir toutes mes commandes</a>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h2>
                            MES VÉLOS
                            <span class="info-bubble" data-tooltip="L'enregistrement de votre vélo facilite le suivi technique et la prise en charge de la garantie.">?</span>
                        </h2>
                        <p class="subtitle">Vous n'avez pas enregistré de vélo</p>
                    </div>
                    <div class="card-body centered-content">
                        <a href="#" class="text-link">Enregistrer un vélo</a>
                    </div>
                </div>

               <div class="dashboard-card card-clickable" onclick="openModal()">
                    <div class="card-body centered-content flex-col">
                        <i class="fa-solid fa-user-lock icon-large" style="margin-bottom:10px;"></i>
                            <h3>
                            MES DONNÉES PERSONNELLES
                            <span class="info-bubble" data-tooltip="Cliquez pour voir vos données stockées">?</span>
                            </h3>
                         <p style="font-size: 0.8em; color: gray; margin-top:5px;">Cliquez ici pour consulter</p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="rgpdModal" class="modal-overlay">
    <div class="modal-box">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        
        <h2 style="color: #00ecff; margin-top: 0;">Mes Données</h2>
        <p style="font-size: 0.9em; margin-bottom: 20px; color: #ccc;">
            Voici les informations stockées vous concernant :
        </p>

        <div class="data-list">
            <div class="data-row">
                <span>Nom :</span>
                <strong>{{ $client->nom_client ?? 'N/A' }}</strong>
            </div>
            <div class="data-row">
                <span>Prénom :</span>
                <strong>{{ $client->prenom_client ?? 'N/A' }}</strong>
            </div>
            <div class="data-row">
                <span>Email :</span>
                <strong>{{ $client->email_client ?? 'N/A' }}</strong>
            </div>
            <div class="data-row">
                <span>Téléphone :</span>
                <strong>{{ $client->tel ?? 'N/A' }}</strong>
            </div>
            <div class="data-row">
                <span>Date Inscription :</span>
                <strong>{{ $client->date_inscription ?? 'N/A' }}</strong>
            </div>
        </div>

        <a href="mailto:admin@cube-bikes.fr?subject=Demande suppression données (Client {{ $client->id_client }})&body=Je souhaite supprimer mes données personnelles..." class="btn-suppression">
            <i class="fa-solid fa-trash"></i> Demander la suppression
        </a>
    </div>
</div>

    <script>
        function openModal() {
            document.getElementById('rgpdModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('rgpdModal').style.display = 'none';
        }

        
        window.onclick = function(event) {
            var modal = document.getElementById('rgpdModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>

    <script src="{{ asset('js/header.js') }}" defer></script>
</body>
</html>