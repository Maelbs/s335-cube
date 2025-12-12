<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes adresses | Cube Bikes</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listAdresses.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @include('layouts.header')

    <div class="dashboard-container">
        @include('layouts.sideBar')
        
        <main class="main-content scrollable">
            <div class="cube-container">
                
                <div class="cube-header">
                    <h1 class="cube-title">MES ADRESSES</h1>
                    <p class="cube-subtitle">
                        Vos coordonnées de facturation et de livraison.
                    </p>
                    <div style="margin-top: 30px;">
                        <a href="{{ route('adresses.create.show') }}" class="cube-btn-primary" style="text-decoration: none; display: inline-block; padding: 12px 30px;">
                            + Ajouter une adresse
                        </a>
                    </div>
                </div>

                <table class="cube-table">
                    <thead>
                        <tr>
                            <th class="cube-th">Type</th>
                            <th class="cube-th">Adresse</th>
                            <th class="cube-th">Ville</th>
                            <th class="cube-th">Code Postal</th>
                            <th class="cube-th">Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        {{-- 1. ADRESSE DE FACTURATION (EN PREMIER) --}}
                        @if($adresseFacturation)
                            <tr class="cube-row" style="border-left: 4px solid #ffd700;"> {{-- Colonne Type avec TAG --}}
                                <td class="cube-td" data-label="Type">
                                    <span class="cube-tag tag-billing">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> Facturation
                                    </span>
                                </td>

                                <td class="cube-td" data-label="Adresse">
                                    <span class="cube-payment">
                                        {{ $adresseFacturation->rue }}
                                        @if($adresseFacturation->complement) <br><small>({{ $adresseFacturation->complement }})</small> @endif
                                    </span>
                                </td>

                                <td class="cube-td" data-label="Ville">
                                    <span class="cube-price" style="font-size: 1rem;">
                                        {{ strtoupper($adresseFacturation->ville) }}
                                    </span>
                                </td>

                                <td class="cube-td" data-label="CodePostal">
                                    <span class="cube-price" style="font-size: 1rem;">
                                        {{ $adresseFacturation->code_postal }}
                                    </span>
                                </td>

                                <td class="cube-td" data-label="Edit">
                                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                        <a href="{{ route('adresses.edit', $adresseFacturation->id_adresse) }}" class="cube-btn-details" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        {{-- 2. ADRESSES DE LIVRAISON (BOUCLE) --}}
                        @foreach($adressesLivraison as $adresse)
                            <tr class="cube-row">
                                
                                {{-- Colonne Type avec TAG --}}
                                <td class="cube-td" data-label="Type">
                                    <span class="cube-tag tag-shipping">
                                        <i class="fa-solid fa-truck"></i> Livraison
                                    </span>
                                </td>

                                <td class="cube-td" data-label="Adresse">
                                    <span class="cube-payment">
                                        {{ $adresse->rue }}
                                        @if($adresse->complement) <br><small>({{ $adresse->complement }})</small> @endif
                                    </span>
                                </td>

                                <td class="cube-td" data-label="Ville">
                                    <span class="cube-price" style="font-size: 1rem;">
                                        {{ strtoupper($adresse->ville) }}
                                    </span>
                                </td>

                                <td class="cube-td" data-label="CodePostal">
                                    <span class="cube-price" style="font-size: 1rem;">
                                        {{ $adresse->code_postal }}
                                    </span>
                                </td>

                                <td class="cube-td" data-label="Edit">
                                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                        <a href="{{ route('adresses.edit', $adresse->id_adresse) }}" class="cube-btn-details">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                @if(!$adresseFacturation && $adressesLivraison->isEmpty())
                     <div class="cube-empty">
                        <p>Aucune adresse enregistrée.</p>
                     </div>
                @endif

            </div>
        </main>
    </div>
</body>
</html>