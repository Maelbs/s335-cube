<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/listCommandes.css') }}">
</head>

<body>


    @include('layouts.header')

    <div class="cube-container">

        <a href="{{ route('client.commandes') }}" class="cube-back-link">Retour à mes commandes</a>

        <div class="cube-header" style="text-align: left; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1 class="cube-title" style="font-size: 2.5rem;">COMMANDE <span
                            style="color: var(--cube-red);">#{{ $commande->id_commande }}</span></h1>
                    <p class="cube-subtitle" style="margin-top: 5px;">
                        Passée le {{ $commande->date_commande ? $commande->date_commande->format('d/m/Y') : '-' }}
                    </p>
                </div>

                @php
                    $statusRaw = strtolower($commande->statut_livraison ?? '');
                    $statusClass = match ($statusRaw) {
                        'livre', 'livré', 'domicile' => 'status-livre',
                        'annule' => 'status-annule',
                        'expedie' => 'status-expedie',
                        default => 'status-prepare',
                    };
                    $statusLabel = match ($statusRaw) {
                        'livre', 'domicile' => 'LIVRÉ',
                        'expedie' => 'EXPÉDIÉ',
                        'annule' => 'ANNULÉ',
                        default => strtoupper($commande->statut_livraison ?? 'EN COURS')
                    };
                @endphp
                <span class="cube-badge {{ $statusClass }}" style="font-size: 1rem; padding: 10px 20px;">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div class="cube-details-grid">

            <div class="cube-card">
                <h2 class="cube-card-title">Articles commandés</h2>

                @foreach($commande->articles as $article)
                    <div class="cube-item-row">
                        @php
                            $refClean = trim($article->reference);
                            $isAccessoire = strlen($refClean) === 5;
                            $dossier = $isAccessoire ? 'ACCESSOIRES' : 'VELOS';
                            $cheminImage = 'images/' . $dossier . '/' . $refClean . '/image_1.jpg';
                            $imageFinale = file_exists(public_path($cheminImage)) ? asset($cheminImage) : 'https://placehold.co/100x80?text=No+Image';
                        @endphp

                        <img src="{{ $imageFinale }}" alt="{{ $article->nom_article }}" class="cube-item-image">

                        <div class="cube-item-details">
                            <span class="cube-item-name">{{ $article->nom_article }}</span>
                            <span class="cube-item-meta">
                                Réf: {{ $article->reference }}
                                @if($article->pivot->taille_selectionnee && $article->pivot->taille_selectionnee !== 'Non renseigné')
                                    | Taille: <strong>{{ $article->pivot->taille_selectionnee }}</strong>
                                @endif
                            </span>
                            <span class="cube-item-meta">
                                Qté: {{ $article->pivot->quantite_article_commande }}
                            </span>
                        </div>

                        <div class="cube-item-price">
                            {{ number_format($article->pivot->quantite_article_commande * $article->pivot->prix_unitaire_article, 2, ',', ' ') }}
                            €
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <div class="cube-card" style="margin-bottom: 30px;">
                    <h2 class="cube-card-title">Récapitulatif</h2>

                    <div class="cube-summary-row">
                        <span>Sous-total</span>
                        <span>{{ number_format($commande->montant_total_commande, 2, ',', ' ') }} €</span>
                    </div>

                    <div class="cube-summary-total">
                        <span>TOTAL</span>
                        <span>{{ number_format($commande->montant_total_commande, 2, ',', ' ') }} €</span>
                    </div>

                    <div style="margin-top: 20px; font-size: 0.9rem; color: #777;">
                        TVA incluse
                    </div>
                </div>

                <div class="cube-card" style="margin-bottom: 30px;">
                    <h2 class="cube-card-title">Informations</h2>

                    <div class="cube-info-block">
                        <span class="cube-info-label">Adresse de livraison</span>
                        <div class="cube-info-value">
                            @if($commande->adresse)

                                @if($commande->id_type_livraison == 2)
                                    <strong>
                                        <i class="fa-solid fa-shop"></i>
                                        {{ $commande->adresse->magasins->first()->nom_magasin ?? 'Magasin Partenaire' }}
                                    </strong><br>
                                @else
                                    {{ $commande->client->prenom_client }} {{ $commande->client->nom_client }}<br>
                                @endif

                                {{ $commande->adresse->rue }}<br>
                                {{ $commande->adresse->code_postal }} {{ $commande->adresse->ville }}<br>
                                {{ $commande->adresse->pays }}

                            @else
                                <span style="color:red">Adresse introuvable</span>
                            @endif
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                    <div class="cube-info-block">
                        <span class="cube-info-label">Adresse de facturation</span>
                        <div class="cube-info-value">
                            @if($commande->client->adresseFacturation)
                                {{ $commande->client->prenom_client }} {{ $commande->client->nom_client }}<br>
                                {{ $commande->client->adresseFacturation->rue }}<br>
                                {{ $commande->client->adresseFacturation->code_postal }}
                                {{ $commande->client->adresseFacturation->ville }}<br>
                                {{ $commande->client->adresseFacturation->pays }}
                            @else
                                <span style="color:#999; font-style: italic;">Identique à la livraison</span>
                            @endif
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                    <div class="cube-info-block">
                        <span class="cube-info-label">Moyen de paiement</span>
                        <div class="cube-info-value">
                            {{ $commande->type_paiement ?? 'Non défini' }}
                        </div>
                    </div>
                </div>

                <div>
                    <a href="{{ url('/commandes') . '/' . $commande->id_commande . '/' . 'facture' }}"
                        class="cube-btn-action cube-btn-primary">
                        <span style="margin-right: 10px">📄</span> Télécharger la facture
                    </a>

                    <a href="{{ url('/commandes') }}" class="cube-btn-action cube-btn-outline">
                        Retourner un article
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>

</html>