<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aide & FAQ | Cube Bikes</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,600;0,800;1,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    <style>
        @font-face {
            font-family: 'Damas Font';
            src: url('../font/font.woff2');
        }

        body {
            font-family: 'Damas Font', sans-serif;
            background-color: #ffffff;
            color: #222;
            margin: 0;
            line-height: 1.5;
        }

        .faq-wrapper {
            max-width: 850px;
            margin: 160px auto 100px;
            padding: 0 25px;
        }

        .faq-intro {
            text-align: center;
            margin-bottom: 80px;
        }

        .faq-intro h1 {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            margin-bottom: 15px;
        }

        .faq-intro p {
            font-size: 18px;
            color: #777;
            max-width: 650px;
            margin: 0 auto;
        }

        .faq-section {
            margin-bottom: 60px;
        }

        .faq-section h2 {
            font-size: 20px;
            font-weight: 800;
            color: #00b0f0;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .faq-section h2::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
            margin-left: 20px;
        }

        /* Accordéon Style */
        details {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        details:last-child {
            border-bottom: none;
        }

        summary {
            padding: 22px 0;
            cursor: pointer;
            font-weight: 700;
            font-size: 17px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            list-style: none;
            outline: none;
        }

        summary::-webkit-details-marker { display: none; }

        /* L'icône flèche */
        summary::after {
            content: '\f078'; /* Chevron FontAwesome */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 14px;
            color: #ccc;
            transition: transform 0.3s, color 0.3s;
        }

        details[open] summary::after {
            transform: rotate(180deg);
            color: #00b0f0;
        }

        .faq-content {
            padding: 0 0 30px 0;
            color: #555;
            font-size: 16px;
            line-height: 1.8;
            animation: fadeIn 0.4s ease;
        }

        .faq-content ul {
            padding-left: 20px;
            margin-top: 15px;
            list-style-type: none;
        }

        .faq-content li {
            position: relative;
            margin-bottom: 12px;
            padding-left: 20px;
        }

        .faq-content li::before {
            content: "•";
            color: #00b0f0;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .dev-alert {
            display: inline-block;
            margin-top: 15px;
            background: #fff4f4;
            color: #d9534f;
            padding: 5px 15px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .faq-contact {
            text-align: center;
            margin-top: 40px;
            padding: 50px;
            background: #fafafa;
            border-radius: 15px;
        }

        .btn-contact {
            display: inline-block;
            background-color: #00b0f0;
            color: #fff;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            margin-top: 20px;
            transition: transform 0.2s, background 0.3s;
        }

        .btn-contact:hover {
            background-color: #000;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    @include('layouts.header')

    <div class="faq-wrapper">

        <div class="faq-intro">
            <h1>GUIDE D’UTILISATION - CUBE France</h1>
            <p>Bienvenue dans l'univers CUBE. Vous avez fait le choix d'une mobilité plus douce et durable, et nous sommes là pour vous accompagner.</p>
        </div>

        {{-- SECTION 1 --}}
        <div class="faq-section">
            <h2>1. Choisir son vélo électrique</h2>

            <details>
                <summary>Comment trouver le vélo idéal rapidement ?</summary>
                <div class="faq-content">
                    Utilisez nos filtres ! Dans la section "Vélos Électriques", filtrez par "Catégories" (VTT, ville & campagne, Gravel) ou par "Prix". Cela vous permet de voir uniquement les modèles adaptés à votre trajet quotidien.
                </div>
            </details>

            <details>
                <summary>Comment être sûr de choisir la bonne taille ?</summary>
                <div class="faq-content">
                    Pour un confort optimal lors de vos trajets quotidiens, la taille est primordiale. Sur chaque fiche produit, cliquez sur le lien "Quelle est ma taille de cadre ?". Il vous suffit de renseigner votre taille et votre longueur de pas (en cm) pour obtenir la recommandation idéale. Si vous êtes entre deux tailles, l'outil vous l'indiquera pour vous guider.
                </div>
            </details>

            <details>
                <summary>Je cherche un vélo pour mes trajets domicile-travail, comment m'assurer de l'autonomie ?</summary>
                <div class="faq-content">
                    L'autonomie dépend de nombreux facteurs (poids, dénivelé, mode d'assistance). Pour éviter la panne sèche, consultez la fiche technique sur l’assistance électrique disponible sur les fiches de nos vélos électriques.
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
                <summary>J'ai commandé seulement des accessoires, dois-je aller en magasin ?</summary>
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
                    Tout se passe dans votre compte client. La rubrique "Mes Commandes" vous permet de suivre l'état de votre achat (Préparé, Expédié, Livré, etc.) et de télécharger vos factures.
                </div>
            </details>

            <details>
                <summary>Avez-vous un programme d'enregistrement de vélo ?</summary>
                <div class="faq-content">
                    Oui, et nous vous le recommandons ! Dans la rubrique "Mes Vélos" de votre compte, vous pouvez enregistrer votre modèle. Cela facilite le suivi en cas de besoin technique ou de garantie.
                    <br><span class="dev-alert">La fonctionnalité est en cours de développement</span>
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

        {{-- SECTION 5 --}}
        <div class="faq-section">
            <h2>5. Conseil écoresponsable</h2>

            <details>
                <summary>Vous cherchez un vélo avec un impact écologique réduit ?</summary>
                <div class="faq-content">
                    Nous vous invitons à consulter les <strong>fiches techniques</strong> de nos modèles. Vous y trouverez le détail des matériaux et des composants utilisés. En privilégiant des vélos robustes et facilement réparables, vous agissez directement pour limiter votre empreinte carbone sur le long terme. S'il s'agit d'un vélo électrique, vous pouvez également vous informer sur la batterie dans la section assistance.
                </div>
            </details>

            <details>
                <summary>Comment prolonger la vie de mon vélo ?</summary>
                <div class="faq-content">
                    Dans la section <strong>Accessoires -> Entretien & réparation</strong>, vous avez accès à toutes les pièces détachées nécessaires. Entretenir et réparer son vélo plutôt que de le remplacer, c'est le geste le plus efficace pour la planète. Un vélo qui dure, c'est un vélo propre !
                </div>
            </details>

            <details>
                <summary>Pourquoi ne vendez-vous pas de vêtements ?</summary>
                <div class="faq-content">
                    En nous concentrant uniquement sur le matériel technique et en supprimant les collections de vêtements, nous limitons la surproduction textile. On se focalise sur l'essentiel : votre mobilité et la durabilité du matériel.
                </div>
            </details>

            <details>
                <summary>Pourquoi le Click & Collect ?</summary>
                <div class="faq-content">
                    En récupérant votre vélo chez un revendeur de proximité, vous soutenez le commerce local et réduisez les emballages inutiles. De plus, vous bénéficiez d'un montage professionnel qui garantit la longévité de votre matériel.
                </div>
            </details>
        </div>

        <div class="faq-contact">
            <h3>Vous ne trouvez pas votre réponse ?</h3>
            <p>Notre équipe est à votre disposition.</p>
            <a href="{{ route('contact') }}" class="btn-contact">Contactez-nous</a>
        </div>

    </div>

    <script src="{{ asset('js/header.js') }}"></script>

</body>

</html>