<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aide & FAQ | Cube Bikes</title>

    {{-- Polices et Icônes comme sur votre autre page --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,600;0,800;1,800&display=swap" rel="stylesheet">

    {{-- CSS du Header --}}
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    {{-- CSS Spécifique pour la FAQ --}}
    <style>
        /* On remet la police spécifique que tu voulais pour le corps de page */
        @font-face {
            font-family: 'Damas Font';
            src: url('../font/font.woff2'); /* Assurez-vous que le chemin est bon depuis /aide */
        }

        body {
            font-family: 'Damas Font', sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
        }

        .faq-wrapper {
            max-width: 900px;
            margin: 140px auto 60px; /* Marge top pour compenser le header fixed */
            padding: 0 20px;
        }

        .faq-intro {
            text-align: center;
            margin-bottom: 60px;
        }

        .faq-intro h1 {
            font-size: 48px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #333;
        }

        .faq-intro p {
            font-size: 18px;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .faq-section {
            margin-bottom: 40px;
        }

        .faq-section h2 {
            font-size: 24px;
            font-weight: 800;
            color: #00b0f0; /* Bleu CUBE */
            text-transform: uppercase;
            text-align: left;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }

        /* Style de l'accordéon */
        details {
            background: #fff;
            border: 1px solid #e5e5e5;
            margin-bottom: 10px;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        details:hover {
            border-color: #ccc;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        details[open] {
            border-color: #00b0f0;
            box-shadow: 0 4px 15px rgba(0,176,240,0.1);
        }

        summary {
            padding: 18px 25px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            color: #333;
            position: relative;
            list-style: none; /* Cache la flèche par défaut */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        /* L'icône + / - */
        summary::after {
            content: '+';
            font-size: 20px;
            color: #00b0f0;
            font-weight: 900;
            transition: transform 0.3s ease;
        }

        details[open] summary::after {
            content: '-';
            transform: rotate(180deg);
            color: #333;
        }

        .faq-content {
            padding: 0 25px 25px 25px;
            color: #555;
            font-size: 15px;
            line-height: 1.6;
            border-top: 1px solid transparent;
            animation: slideDown 0.3s ease-out;
        }

        details[open] .faq-content {
            border-top: 1px solid #f0f0f0;
        }

        .faq-content ul {
            margin-top: 10px;
            padding-left: 20px;
            list-style-type: disc;
        }

        .faq-content li {
            margin-bottom: 8px;
        }

        .faq-content strong {
            color: #333;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Lien contact en bas */
        .faq-contact {
            text-align: center;
            margin-top: 60px;
            padding: 40px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        
        /* Bouton contact */
        .btn-contact {
            display: inline-block;
            background-color: #00b0f0;
            color: #fff;
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .btn-contact:hover {
            background-color: #008cb0;
        }
    </style>
</head>

<body>

    @include('layouts.header')

    <div class="faq-wrapper">

        {{-- EN-TÊTE --}}
        <div class="faq-intro">
            <h1>Aide & FAQ - CUBE France</h1>
            <p>Bienvenue dans l'univers CUBE.<br>
            Vous avez fait le choix d'une mobilité plus douce et durable, et nous sommes là pour vous accompagner.</p>
        </div>

        {{-- SECTION 1 --}}
        <div class="faq-section">
            <h2>1. Choisir son vélo électrique</h2>

            <details>
                <summary>Comment trouver le vélo idéal rapidement ?</summary>
                <div class="faq-content">
                    Utilisez nos filtres ! Dans la section "Vélos Électriques", filtrez par "Usage" (Ville, Vélotaf, Sport) ou par "Prix". Cela vous permet de voir uniquement les modèles adaptés à votre trajet quotidien.
                </div>
            </details>

            <details>
                <summary>Comment être sûr de choisir la bonne taille ?</summary>
                <div class="faq-content">
                    Pour un confort optimal lors de vos trajets quotidiens, la taille est primordiale. Sur chaque fiche produit, cliquez sur le lien "Quelle est ma taille de cadre ?". Il vous suffit de renseigner votre taille et votre entrejambe (en cm) pour obtenir la recommandation idéale. Si vous êtes entre deux tailles, l'outil vous l'indiquera pour vous guider.
                </div>
            </details>

            <details>
                <summary>Je cherche un vélo pour mes trajets domicile-travail, comment m'assurer de l'autonomie ?</summary>
                <div class="faq-content">
                    L'autonomie dépend de nombreux facteurs (poids, dénivelé, mode d'assistance). Pour éviter la panne sèche, consultez l'Assistant d'Autonomie Bosch disponible sur les fiches de nos vélos électriques. Il vous permet de simuler votre parcours pour choisir la batterie adaptée à votre usage.
                </div>
            </details>

            <details>
                <summary>Vendez-vous des casques ou des vêtements de pluie ?</summary>
                <div class="faq-content">
                    Non. CUBE France se concentre désormais exclusivement sur la vente de vélos (musculaires et électriques) et d'accessoires techniques (pièces détachées, antivols, éclairages, sacoches). Nous ne proposons plus d'équipements du cycliste (textile, chaussures, casques).
                </div>
            </details>

            <details>
                <summary>Que signifient les statuts de disponibilité ?</summary>
                <div class="faq-content">
                    <ul>
                        <li><strong>Disponible en ligne :</strong> Le vélo est en stock, vous pouvez le commander immédiatement.</li>
                        <li><strong>Disponible en magasin :</strong> Le vélo est en stock chez un partenaire. Vous pouvez voir la liste des magasins pour l'acheter sur place.</li>
                        <li><strong>Commandable en magasin :</strong> Le vélo n'est pas en stock immédiat, mais un revendeur peut le commander pour vous.</li>
                    </ul>
                </div>
            </details>
        </div>

        {{-- SECTION 2 --}}
        <div class="faq-section">
            <h2>2. Commande et Paiement</h2>

            <details>
                <summary>Quels sont les moyens de paiement acceptés ?</summary>
                <div class="faq-content">
                    Vous pouvez régler votre commande en toute sécurité via :
                    <ul>
                        <li><strong>Carte Bancaire :</strong> Visa, Mastercard (paiement sécurisé).</li>
                        <li><strong>PayPal :</strong> En utilisant votre compte existant.</li>
                    </ul>
                </div>
            </details>

            <details>
                <summary>Puis-je payer en plusieurs fois ?</summary>
                <div class="faq-content">
                    Non, le paiement de la commande s'effectue au comptant (en une seule fois) lors de la validation du panier sur le site.
                </div>
            </details>

            <details>
                <summary>Que se passe-t-il si mon paiement échoue ?</summary>
                <div class="faq-content">
                    En cas de refus bancaire (CB), vous disposez de 2 tentatives supplémentaires. Au-delà, ou si le paiement PayPal n'est pas validé sous 5 minutes, la commande est automatiquement annulée.
                </div>
            </details>

            <details>
                <summary>J'ai un code promo, comment l'utiliser ?</summary>
                <div class="faq-content">
                    Si vous possédez un code de réduction, vous devez le saisir dans le champ "Code Promo" situé dans le récapitulatif de votre panier, juste avant de valider votre commande.
                </div>
            </details>

            <details>
                <summary>Que se passe-t-il si mon paiement par carte est refusé ?</summary>
                <div class="faq-content">
                    En cas d'échec de paiement par carte bancaire, notre système sécurisé vous permet de réaliser jusqu'à 2 nouvelles tentatives (avec la même carte ou une autre). Si le paiement échoue au bout de ces 3 essais, la commande sera automatiquement annulée.
                </div>
            </details>

            <details>
                <summary>J'ai choisi PayPal mais je n'ai pas finalisé le paiement tout de suite, ma commande est-elle validée ?</summary>
                <div class="faq-content">
                    Attention, une fois la commande validée sur le site, vous disposez de 5 minutes pour effectuer le règlement sur l'interface PayPal. Passé ce délai, la commande est automatiquement annulée par notre système.
                </div>
            </details>
        </div>

        {{-- SECTION 3 --}}
        <div class="faq-section">
            <h2>3. Livraison et Réception</h2>

            <details>
                <summary>Puis-je me faire livrer mon vélo à domicile ?</summary>
                <div class="faq-content">
                    Non. Pour garantir votre sécurité, tous nos vélos nécessitent un montage et un réglage par un professionnel. La livraison s'effectue donc exclusivement en Click & Collect chez l'un de nos revendeurs partenaires. Vous choisissez votre magasin lors de la commande, et vous serez averti dès que votre vélo sera prêt à rouler.
                </div>
            </details>

            <details>
                <summary>Combien coûte la livraison en magasin ?</summary>
                <div class="faq-content">
                    La livraison en "Click & Collect magasin" est gratuite pour toute commande supérieure à 50 €.
                </div>
            </details>

            <details>
                <summary>J'ai commandé seulement des accessoires (antivol, lumières), dois-je aller en magasin ?</summary>
                <div class="faq-content">
                    Pas obligatoirement. Si votre panier ne contient que des accessoires, vous pouvez choisir la livraison Express à domicile. Des frais de port s'appliqueront en fonction du montant de la commande.
                </div>
            </details>

            <details>
                <summary>Quels sont les délais de retrait ?</summary>
                <div class="faq-content">
                    Une fois le vélo reçu par le magasin et préparé, vous recevez une notification. Vous avez alors 10 jours ouvrés pour venir le récupérer muni de votre pièce d'identité.
                </div>
            </details>

            <details>
                <summary>Combien de temps ai-je pour récupérer mon vélo en magasin ?</summary>
                <div class="faq-content">
                    Dès réception de la notification (email/SMS) vous informant que votre vélo est prêt, vous disposez de 10 jours ouvrés pour venir le retirer. Si vous ne pouvez pas venir dans ce délai, le magasin contactera notre service livraison pour convenir d'une nouvelle date. Sans nouvelles de votre part après cette relance, la commande sera annulée et le vélo retourné.
                </div>
            </details>
        </div>

        {{-- SECTION 4 --}}
        <div class="faq-section">
            <h2>4. Votre Espace Client et SAV</h2>

            <details>
                <summary>Comment retrouver mes factures et suivre ma commande ?</summary>
                <div class="faq-content">
                    Tout se passe dans votre compte client. La rubrique "Mes Commandes" vous permet de suivre l'état de votre achat (Validé, Expédié, Livré) et de télécharger vos factures.
                </div>
            </details>

            <details>
                <summary>Avez-vous un programme d'enregistrement de vélo ?</summary>
                <div class="faq-content">
                    Oui, et nous vous le recommandons ! Dans la rubrique "Mes Vélos" de votre compte, vous pouvez enregistrer votre modèle. Cela facilite le suivi en cas de besoin technique ou de garantie.
                </div>
            </details>

            <details>
                <summary>Comment contacter le service client ?</summary>
                <div class="faq-content">
                    <ul>
                        <li><strong>Une question rapide ?</strong> Utilisez notre Chatbot disponible en bas de page pour une réponse immédiate.</li>
                        <li><strong>Un problème avec une commande ?</strong> Utilisez le formulaire de contact dans votre espace client ou sur la page de détail de la commande.</li>
                    </ul>
                </div>
            </details>

            <details>
                <summary>Je souhaite retourner un accessoire, comment faire ?</summary>
                <div class="faq-content">
                    Vous pouvez effectuer une demande de retour directement depuis le détail de votre commande. Un formulaire sera transmis à notre SAV. Pour les vélos récupérés en magasin, le retour se gère directement avec le revendeur.
                </div>
            </details>

            <details>
                <summary>Comment sont protégées mes données personnelles et mon mot de passe ?</summary>
                <div class="faq-content">
                    Nous prenons la sécurité très au sérieux. Votre mot de passe est chiffré selon les normes strictes recommandées par l'ANSSI (Agence nationale de la sécurité des systèmes d'information). De plus, nous respectons scrupuleusement le RGPD pour la protection de vos données personnelles.
                </div>
            </details>
        </div>

        <div class="faq-contact">
            <h3>Vous ne trouvez pas votre réponse ?</h3>
            <p>Notre équipe est à votre disposition.</p>
           
            <a href="{{ route('contact') }}" class="btn-contact">Nous contacter</a>
        </div>

    </div>

    {{-- Script pour le header --}}
    <script src="{{ asset('js/header.js') }}"></script>

</body>

</html>