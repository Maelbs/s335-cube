<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" name="description" content="Site non officiel de cube">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité - CUBE BIKES France</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styleBody.css') }}">

    <style>
        @font-face {
            font-family: 'Damas Font';
            src: url('../font/font.woff2');
            font-display: swap;
        }
        body {
            background-color: #ffffff !important;
            color: #333;
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

        .privacy-page-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 160px 40px 80px 40px;
            font-family: 'Damas Font', sans-serif;
        }

        .breadcrumb {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        .breadcrumb a { text-decoration: none; color: #666; }
        .breadcrumb span { margin: 0 5px; }

        h1.privacy-main-title {
            text-align: left !important;
            font-size: 55px;
            font-weight: 900;
            font-style: italic;
            text-transform: uppercase;
            line-height: 1;
            color: #000;
            margin-bottom: 40px;
            margin-top: 0;
        }

        .privacy-intro p {
            font-family: Arial, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin-bottom: 15px;
            text-align: justify;
        }

        h2.privacy-section-title {
            text-align: center !important;
            font-size: 36px;
            font-weight: 900;
            font-style: italic;
            text-transform: uppercase;
            color: #000;
            margin-top: 70px;
            margin-bottom: 30px;
        }

        h3.privacy-subtitle {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            color: #000;
            margin-top: 30px;
            margin-bottom: 15px;
            text-align: left;
        }

        ul.privacy-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 20px;
        }
        ul.privacy-list li {
            font-family: Arial, sans-serif;
            font-size: 15px;
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
            color: #444;
        }
        ul.privacy-list li::before {
            content: "-";
            position: absolute;
            left: 0;
            font-weight: bold;
            color: #000;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border: 1px solid #eee;
        }
        table.privacy-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        table.privacy-table th {
            background: #f9f9f9;
            padding: 15px;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
        }
        table.privacy-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .contact-box {
            background: #f4f4f4;
            padding: 20px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-top: 20px;
        }
        .contact-box a {
            color: #00b0f0;
            font-weight: bold;
        }

        .simple-footer {
            background: #111;
            color: #fff;
            padding: 40px;
            text-align: center;
            text-transform: uppercase;
            font-size: 14px;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .privacy-page-container { padding-top: 120px; padding-left: 20px; padding-right: 20px; }
            h1.privacy-main-title { font-size: 32px; }
            h2.privacy-section-title { font-size: 26px; }
        }
    </style>
</head>
<body>
    @include('layouts.header')

    <main class="privacy-page-container">
        
        <div class="breadcrumb">
            <a href="/">ACCUEIL</a> <span>&rsaquo;</span> POLITIQUE DE Protection des Données Personnelles ET gestions des Cookies
        </div>

        <h1 class="privacy-main-title">
            POLITIQUE DE Protection des Données Personnelles ET <br> GESTION DES COOKIES
        </h1>

        <div class="privacy-intro">
            <p>
                Chez CUBE BIKES France, la confiance est au cœur de la relation que nous entretenons avec vous.
                Cette page a pour but de vous expliquer en toute transparence comment fonctionnent les cookies sur notre site et comment nous protégeons vos données.
            </p>
        </div>

        <h2 class="privacy-section-title">1. COMPRENDRE LES COOKIES : DÉFINITION ET USAGE</h2>

        <h3 class="privacy-subtitle">QU'EST-CE QU'UN COOKIE ?</h3>
        <div class="privacy-intro">
            <p>
                Un cookie est un petit fichier texte déposé sur votre appareil (ordinateur, tablette ou smartphone) lorsque vous visitez notre site web.
                Il permet à notre site de mémoriser certaines de vos actions et préférences pour une durée déterminée.
            </p>
        </div>

        <h3 class="privacy-subtitle">À QUOI NOUS SERVENT LES COOKIES ?</h3>
        <div class="privacy-intro">
            <p>Conformément aux recommandations de la CNIL, nous vous informons de l'utilité précise de chaque type de cookie avant de vous demander votre accord :</p>
        </div>

        <ul class="privacy-list">
            <li><strong>Cookies Essentiels (Techniques) :</strong> Ils sont indispensables au fonctionnement du site. Ils permettent par exemple votre authentification à votre compte client ou la mémorisation de votre panier de commande. Vous ne pouvez pas les refuser, car le site ne fonctionnerait pas sans eux.</li>
            <li><strong>Cookies de Statistiques de Fréquentation :</strong> Ils nous aident à comprendre comment les visiteurs utilisent le site (pages les plus vues, temps passé) pour en améliorer l'ergonomie.</li>
            <li><strong>Cookies Partenaires (Publicitaires et Sociaux) :</strong> Ils permettent à des partenaires identifiés de vous proposer des contenus adaptés ou de partager votre activité sur les réseaux sociaux.</li>
            <li><strong>Cookies de Préférences :</strong> Ils permettent de mémoriser certaines préférences comme la langue ou la région.</li>
        </ul>

        <h3 class="privacy-subtitle">VOTRE CONTRÔLE SUR LES COOKIES</h3>
        <div class="privacy-intro">
            <p>
                Nous respectons votre liberté de choix :
                Consentement clair : Lors de votre arrivée, un bandeau vous demande clairement d'ACCEPTER ou de PERSONALISER les cookies non essentiels.
                En cliquant sur personnaliser vous avez la possibilité aussi de <strong>TOUT REFUSER</strong>.
            </p>
        </div>

        <h2 class="privacy-section-title">2. POLITIQUE DE PROTECTION DES DONNÉES PERSONNELLES</h2>

        <h3 class="privacy-subtitle">QUI COLLECTE VOS DONNÉES ?</h3>
        <div class="contact-box">
            Le responsable du traitement de vos données est : CUBE BIKES FRANCE (Société Planet Fun S.A.)<br>
            Adresse : Rond-Point de la République, ZI les 4 Chevaliers, 17180 Périgny<br>
            Contact : <a href="mailto:service-clients@cubebike.fr">service-clients@cubebike.fr</a>
        </div>

        <h3 class="privacy-subtitle">QUELLES DONNÉES COLLECTONS-NOUS ?</h3>
        <div class="privacy-intro">
            <p>Nous collectons uniquement les données strictement nécessaires à notre activité, notamment :</p>
        </div>
        <ul class="privacy-list">
            <li><strong>Identité :</strong> Nom, prénom.</li>
            <li><strong>Contact :</strong> L’adresse postale pour la livraison ou la localisation de revendeurs, courriel, numéro de téléphone.</li>
            <li><strong>Données techniques :</strong> Numéro de série de votre vélo, pour l'enregistrement de garantie.</li>
        </ul>

        <h3 class="privacy-subtitle">QUAND COLLECTONS-NOUS VOS DONNÉES ?</h3>
        <ul class="privacy-list">
            <li><strong>Collecte Directe :</strong> Lorsque vous remplissez un formulaire (contact, inscription), créez un compte ou effectuez un achat en ligne.</li>
            <li><strong>Collecte Indirecte :</strong> Avec nos Revendeurs avec l’activation d’une garantie ou lors de pubs sur les réseaux sociaux.</li>
        </ul>

        <h3 class="privacy-subtitle">POURQUOI UTILISONS-NOUS VOS DONNÉES ?</h3>
        <div class="privacy-intro">
            <p>Vos données sont traitées pour des objectifs précis :</p>
        </div>
        <div class="table-container">
            <table class="privacy-table">
                <thead>
                    <tr>
                        <th>Objectif</th>
                        <th>Base Légale</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gestion des commandes : Livraison, facturation et suivi client.</td>
                        <td>L'exécution du contrat</td>
                    </tr>
                    <tr>
                        <td>Service Après-Vente : Gestion des garanties sur vos cadres et composants Cube.</td>
                        <td>L'exécution du contrat</td>
                    </tr>
                    <tr>
                        <td>Communication : Envoi de newsletters sur nos nouveautés.</td>
                        <td>Votre consentement</td>
                    </tr>
                    <tr>
                        <td>Amélioration : Statistiques anonymes pour améliorer nos services.</td>
                        <td>L'intérêt légitime</td>
                    </tr>
                    <tr>
                        <td>Sécurité : Pour assurer la sécurité du site et prévenir la fraude.</td>
                        <td>L'intérêt légitime</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="privacy-subtitle">QUI A ACCÈS À VOS DONNÉES ?</h3>
        <div class="privacy-intro">
            <p>
                Vos données ne sont jamais vendues. Elles sont transmises uniquement aux destinataires suivants :
                Nos services internes (support client, logistique).
                Nos sous-traitants techniques (hébergeur web, transporteurs pour la livraison).
            </p>
        </div>

        <h3 class="privacy-subtitle">COMBIEN DE TEMPS CONSERVONS-NOUS VOS DONNÉES ?</h3>
        <ul class="privacy-list">
            <li><strong>Données client (compte actif) :</strong> 3 ans après le dernier contact.</li>
            <li><strong>Données de facturation :</strong> 10 ans car cela est une obligation légal comptable.</li>
            <li><strong>Cookies :</strong> 13 mois maximum après leur dépôt.</li>
        </ul>

        <h2 class="privacy-section-title">3. VOS DROITS</h2>

        <div class="privacy-intro">
            <p>
                Conformément au RGPD et à la loi Informatique et Libertés, vous disposez des droits suivants sur vos données :
                Droit d'accès, de rectification ou d'effacement.
                Droit à la limitation du traitement ou à la portabilité de vos données.
                Droit d'opposition (refuser que l’on utiliser vos données).
            </p>
        </div>

        <div class="contact-box">
            <h3 style="margin-top:0; font-size: 16px; text-transform: uppercase;">Comment exercer vos droits ?</h3>
            <p>Contactez notre Délégué à la Protection des Données (DPO) :</p>
            <ul style="margin-top: 10px;">
                <li>Par email : <strong><a href="mailto:leo.morard@etu.univ-smb.fr">leo.morard@etu.univ-smb.fr</a></strong></li>
                <li>Par courrier : CUBE Bikes France - Service Données Personnelles - Rond-Point de la République, ZI les 4 Chevaliers, 17180 Périgny</li>
            </ul>
            <p style="margin-top: 15px; font-size: 12px; color: #666;">
                Si vous estimez que vos droits ne sont pas respectés, vous avez le droit d’introduire une réclamation auprès de la CNIL sur le site cnil.fr.
            </p>
        </div>

        <div style="text-align: center; margin-top: 50px; color: #999; font-size: 12px;">
            Dernière mise à jour : 1 décembre 2025
        </div>

    </main>

    @include('layouts.footer')

</body>
</html>