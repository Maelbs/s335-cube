<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $commande->id_commande }}</title>
    <link rel="stylesheet" href="css/invoice.css">
</head>
<body>

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/logo-cube.png') }}" alt="CUBE Logo" class="logo-img">
                </td>
                <td class="details-cell">
                    <div class="invoice-title">FACTURE</div>
                    <div class="invoice-number">N° #{{ $commande->id_commande }}</div>
                    <div class="invoice-date">
                        Date : {{ $commande->date_commande ? $commande->date_commande->format('d/m/Y') : now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="addresses-table">
        <tr>
            <td class="address-box">
                <span class="box-label">Facturé à</span>
                @if($commande->client->adresseFacturation)
                    <strong>{{ $commande->client->prenom_client }} {{ $commande->client->nom_client }}</strong><br>
                    {{ $commande->client->adresseFacturation->rue }}<br>
                    {{ $commande->client->adresseFacturation->code_postal }} {{ $commande->client->adresseFacturation->ville }}<br>
                    {{ $commande->client->adresseFacturation->pays }}
                @else
                    <strong>{{ $commande->client->prenom_client }} {{ $commande->client->nom_client }}</strong><br>
                    (Adresse identique livraison)
                @endif
                <br>
                <div style="margin-top:5px; font-size:11px;">{{ $commande->client->email_client }}</div>
            </td>
            
            <td class="spacer-cell"></td>

            <td class="address-box right">
                <span class="box-label">Livré à</span>
                @if($commande->adresse)
                    <strong>{{ $commande->client->prenom_client }} {{ $commande->client->nom_client }}</strong><br>
                    {{ $commande->adresse->rue }}<br>
                    {{ $commande->adresse->code_postal }} {{ $commande->adresse->ville }}<br>
                    {{ $commande->adresse->pays }}
                @else
                    Adresse de livraison non spécifiée
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th width="15%">Réf.</th>
                <th width="45%">Désignation</th>
                <th width="10%" class="center">Qté</th>
                <th width="15%" class="right">P.U.</th>
                <th width="15%" class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->articles as $article)
            <tr>
                <td class="item-ref">{{ trim($article->reference) }}</td>
                <td>
                    <span class="item-name">{{ $article->nom_article }}</span>
                    @if($article->pivot->taille_selectionnee && $article->pivot->taille_selectionnee !== 'Non renseigné')
                        <div class="item-meta">Taille : {{ $article->pivot->taille_selectionnee }}</div>
                    @endif
                </td>
                <td class="center">{{ $article->pivot->quantite_article_commande }}</td>
                <td class="right">{{ number_format($article->pivot->prix_unitaire_article, 2, ',', ' ') }} €</td>
                <td class="right" style="font-weight:bold;">
                    {{ number_format($article->pivot->quantite_article_commande * $article->pivot->prix_unitaire_article, 2, ',', ' ') }} €
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td class="label">Sous-total HT :</td>
                <td class="value">{{ number_format($commande->montant_total_commande / 1.2, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td class="label">TVA (20%) :</td>
                <td class="value">{{ number_format($commande->montant_total_commande / 1.2 * 0.2, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td class="label">Frais de port :</td>
                <td class="value">0,00 €</td>
            </tr>
            <tr class="grand-total-row">
                <td class="label" style="color:#000;">TOTAL TTC :</td>
                <td>{{ number_format($commande->montant_total_commande, 2, ',', ' ') }} €</td>
            </tr>
        </table>
        <div style="clear:both;"></div>
    </div>

    <div class="footer">
        CUBE France SAS &bull; Capital de 100 000 € &bull; SIRET 123 456 789 00012<br>
        Merci de votre confiance. Keep riding.
    </div>

</body>
</html>