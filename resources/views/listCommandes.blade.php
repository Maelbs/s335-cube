<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listCommandes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">  
</head>
<body>
    @include('layouts.header')

<div class="dashboard-container">
    @include('layouts.sideBar')
    <main class="main-content scrollable">
    <div class="cube-container">
    <div class="cube-header">
        <h1 class="cube-title">MES COMMANDES</h1>
        <p class="cube-subtitle">
            Retrouvez l'historique complet et le suivi de vos achats
        </p>
    </div>

        @if($commandes->isEmpty())
            <div class="cube-empty">
                <p>Vous n'avez pas encore passé de commande.</p>
                <a href="{{ url('/') }}" class="cube-btn-details" style="margin-top:20px;">Découvrir le shop</a>
            </div>
        @else
            <table class="cube-table">
                <thead>
                    <tr>
                        <th class="cube-th">N° Commande</th>
                        <th class="cube-th">Date</th>
                        <th class="cube-th">Prix total</th>
                        <th class="cube-th">Paiement</th>
                        <th class="cube-th">État</th>
                        <th class="cube-th">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commandes as $commande)
                        <tr class="cube-row">
                            <td class="cube-td" data-label="N° Commande">
                                <span class="cube-id">#{{ $commande->id_commande }}</span>
                            </td>

                            <td class="cube-td" data-label="Date">
                                <span class="cube-date">
                                    {{ $commande->date_commande ? $commande->date_commande->format('d/m/Y') : '-' }}
                                </span>
                            </td>

                            <td class="cube-td" data-label="Prix total">
                                <span class="cube-price">
                                    {{ number_format($commande->montant_total_commande, 2, ',', ' ') }} €
                                </span>
                            </td>

                            <td class="cube-td" data-label="Paiement">
                                <span class="cube-payment">
                                    {{ ($commande->type_paiement && $commande->type_paiement !== 'None') ? $commande->type_paiement : '-' }}
                                </span>
                            </td>

                            <td class="cube-td" data-label="État">
                                @php
                                    $statusRaw = strtolower($commande->statut_livraison ?? '');
                                    
                                    $statusClass = match($statusRaw) {
                                        'livre', 'livré', 'domicile' => 'status-livre',
                                        'livré à domicile' => 'status-domicile',
                                        'annule', 'annulé' => 'status-annule',
                                        'expedie', 'expédié' => 'status-expedie',
                                        'prepare', 'preparé', 'valide', 'cree' => 'status-prepare',
                                        default => 'status-unknown',
                                    };

                                    $statusLabel = match($statusRaw) {
                                        'livre', 'domicile' => 'LIVRÉ',
                                        'expedie' => 'EXPÉDIÉ',
                                        'prepare' => 'EN PRÉPARATION',
                                        'valide' => 'VALIDÉ',
                                        'annule' => 'ANNULÉ',
                                        default => strtoupper($commande->statut_livraison ?? 'Inconnu')
                                    };
                                @endphp
                                
                                <span class="cube-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="cube-td" data-label="Facture">
                                <a href="{{  url('/commandes') . '/' . $commande->id_commande . '/' . 'facture' }}" 
                                target="_blank" 
                                title="Télécharger la facture PDF"
                                style="text-decoration: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0099ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                        <text x="13" y="21" font-size="6" font-weight="bold" fill="#0099ff" stroke="none">PDF</text>
                                    </svg>
                                </a>
                            </td>

                            <td class="cube-td">
                                <a href="{{ url('/commandes') . '/' . $commande->id_commande }}" class="cube-btn-details">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</div>
</div>
</body>
</html>
