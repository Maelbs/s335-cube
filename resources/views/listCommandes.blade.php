<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listCommandes.css') }}">
</head>
<body>
    @include('layouts.header')


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
                            {{-- Numéro --}}
                            <td class="cube-td" data-label="N° Commande">
                                <span class="cube-id">#{{ $commande->id_commande }}</span>
                            </td>

                            {{-- Date --}}
                            <td class="cube-td" data-label="Date">
                                <span class="cube-date">
                                    {{ $commande->date_commande ? $commande->date_commande->format('d/m/Y') : '-' }}
                                </span>
                            </td>

                            {{-- Prix --}}
                            <td class="cube-td" data-label="Prix total">
                                <span class="cube-price">
                                    {{ number_format($commande->montant_total_commande, 2, ',', ' ') }} €
                                </span>
                            </td>

                            {{-- Paiement --}}
                            <td class="cube-td" data-label="Paiement">
                                <span class="cube-payment">
                                    {{-- Si 'None', on affiche un tiret pour faire plus propre --}}
                                    {{ ($commande->type_paiement && $commande->type_paiement !== 'None') ? $commande->type_paiement : '-' }}
                                </span>
                            </td>

                            {{-- État (Logique de couleur améliorée) --}}
                            <td class="cube-td" data-label="État">
                                @php
                                    $statusRaw = strtolower($commande->statut_livraison ?? '');
                                    
                                    // Détermine la classe CSS
                                    $statusClass = match($statusRaw) {
                                        'livre', 'livré', 'domicile' => 'status-livre',
                                        'livré à domicile' => 'status-domicile',
                                        'annule', 'annulé' => 'status-annule',
                                        'expedie', 'expédié' => 'status-expedie',
                                        'prepare', 'preparé', 'valide', 'cree' => 'status-prepare',
                                        default => 'status-unknown',
                                    };

                                    // Formate le texte pour l'affichage
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

                            {{-- Bouton --}}
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
    </div>
</body>
</html>
