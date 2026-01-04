<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Import du Validator
use App\Models\LignePanier;
use App\Models\LigneCommande;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\CodePromo;
use App\Models\MagasinPartenaire;
use App\Models\VarianteVelo;
use App\Models\Adresse; // IMPORTANT : Import du modèle Adresse

class PaymentController extends Controller
{
    public function paymentShow()
    {
        $client = Auth::user();
        $adresses = $client->adressesLivraison()->get();

        $magasin = MagasinPartenaire::with('adresses')->find($client->id_magasin);


        $contientVelo = LignePanier::where('id_panier', Panier::where('id_client', Auth::id())->value('id_panier'))
            ->whereIn('reference', VarianteVelo::select('reference'))
            ->exists();

        return view('commande', compact('client', 'adresses', 'magasin', 'contientVelo'));
    }

    private function getOrCreateAddress(Request $request)
    {
        $mode = $request->input('delivery_mode');
        if ($mode === 'magasin')
            return null;

        $contientVelo = LignePanier::where('id_panier', Panier::where('id_client', Auth::id())->value('id_panier'))
            ->whereIn('reference', VarianteVelo::select('reference'))
            ->exists();
        if ($contientVelo && session()->has('id_magasin_choisi'))
            return null;

        if ($request->id_adresse && $request->id_adresse !== 'new') {
            $request->validate(['id_adresse' => 'exists:adresse,id_adresse']);

            return $request->id_adresse;
        }

        $validator = Validator::make($request->all(), [
            'rue' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'nom_destinataire' => 'nullable|string|max:100',
            'prenom_destinataire' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $adresse = new Adresse();
        $adresse->rue = $request->rue;
        $adresse->code_postal = $request->zipcode;
        $adresse->ville = $request->city;
        $adresse->pays = $request->country;
        $adresse->save();

        $client = Auth::user();

        $pivotData = [
            'nom_destinataire' => $request->filled('nom_destinataire') ? $request->nom_destinataire : null,
            'prenom_destinataire' => $request->filled('prenom_destinataire') ? $request->prenom_destinataire : null,
        ];

        $client->adressesLivraison()->attach($adresse->id_adresse, $pivotData);

        return $adresse->id_adresse;
    }

    private function getInfosLivraison($adresseId)
    {
        if ($adresseId) {
            return [
                'id_adresse' => $adresseId,
                'id_type_livraison' => 1
            ];
        }

        if (session()->has('id_magasin_choisi')) {
            $magasin = MagasinPartenaire::with('adresses')->find(session('id_magasin_choisi'));
            $adresseMagasin = $magasin ? $magasin->adresses->first() : null;

            if ($adresseMagasin) {
                return [
                    'id_adresse' => $adresseMagasin->id_adresse,
                    'id_type_livraison' => 2
                ];
            }
        }

        return [
            'id_adresse' => null,
            'id_type_livraison' => 1
        ];
    }

    private function calculerTotalPanier()
    {
        $userId = Auth::id();
        $panier = Panier::where('id_client', $userId)->first();

        if (!$panier) {
            return 0;
        }

        $panierItems = LignePanier::where('id_panier', $panier->id_panier)
            ->with(['article'])
            ->get();
        $subTotal = 0;

        foreach ($panierItems as $item) {
            $prixUnitaire = $item->article ? $item->article->prix : 0;
            $subTotal += $prixUnitaire * $item->quantite_article;
        }

        $discountAmount = 0;
        if ($panier->code_promo) {
            $promo = CodePromo::find($panier->code_promo);
            if ($promo) {
                $discountAmount = $subTotal * $promo->pourcentage;
            }
        }

        $totalFinal = $subTotal - $discountAmount;
        return max($totalFinal, 0);
    }

    private function insertionCommande($idAdresse, $typePaiement, $idTypeLivraison = 1)
    {
        $clientId = Auth::id();
        $total = $this->calculerTotalPanier();
        $panier = Panier::where('id_client', $clientId)->first();

        if (!$panier) {
            return null;
        }

        $commande = Commande::create([
            'id_adresse' => $idAdresse,
            'id_client' => $clientId,
            'id_type_livraison' => $idTypeLivraison,
            'id_panier' => $panier->id_panier,
            'date_commande' => now(),
            'montant_total_commande' => $total,
            'cout_livraison' => 0,
            'type_paiement' => $typePaiement,
            'statut_livraison' => 'cree',
        ]);

        $panierItems = LignePanier::where('id_panier', $panier->id_panier)->with(['article'])->get();

        foreach ($panierItems as $item) {
            if ($item->article) {
                LigneCommande::create([
                    'id_commande' => $commande->id_commande,
                    'reference' => $item->article->reference,
                    'quantite_article_commande' => $item->quantite_article,
                    'prix_unitaire_article' => $item->article->prix,
                    'taille_selectionnee' => $item->taille_selectionnee,
                ]);
            }
        }
        return $commande;
    }

    public function paymentPaypal(Request $request)
    {
        // 1. Récupération ou Création de l'adresse
        try {
            $idAdresseResolue = $this->getOrCreateAddress($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        // 2. Vérification du total
        $total = $this->calculerTotalPanier();
        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $totalFormatted = number_format($total, 2, '.', '');

        // 3. Récupération infos finales (Type livraison + ID final)
        $infosLivraison = $this->getInfosLivraison($idAdresseResolue);
        // On utilisera cet ID pour le retour success
        $idAdresseFinale = $infosLivraison['id_adresse'];

        // 4. Appel API PayPal
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.success', ['id_adresse' => $idAdresseFinale]),
                "cancel_url" => route('paypal.cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => $totalFormatted
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('paypal.cancel')->with('error', 'Erreur lors de la création du paiement PayPal.');
    }

    public function successPaypal(Request $request)
    {
        $request->validate([
            'id_adresse' => 'required|exists:adresse,id_adresse',
            'token' => 'required'
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            $idAdresse = $request->query('id_adresse');

            $infosLivraison = $this->getInfosLivraison($idAdresse);

            $commande = $this->insertionCommande($idAdresse, 'Paypal', $infosLivraison['id_type_livraison']);

            if (!$commande) {
                return redirect()->route('cart.index')->with('error', 'Erreur lors de la création de la commande.');
            }

            $this->finaliserCodePromo(Auth::id());

            $panier = Panier::where('id_client', Auth::id())->first();
            if ($panier) {
                LignePanier::where('id_panier', $panier->id_panier)->delete();
            }

            session()->forget('id_magasin_choisi');

            return redirect()->route('client.commandes.show', $commande->id_commande)
                ->with('success', 'Paiement PayPal validé ! Merci pour votre achat.');
        }

        return redirect()->route('paypal.cancel')->with('error', 'Le paiement PayPal a échoué.');
    }

    public function paymentStripe(Request $request)
    {
        // 1. Récupération ou Création de l'adresse
        try {
            $idAdresseResolue = $this->getOrCreateAddress($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        // 2. Vérification total
        $total = $this->calculerTotalPanier();

        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // 3. Infos Livraison
        $infosLivraison = $this->getInfosLivraison($idAdresseResolue);
        $idAdresseFinale = $infosLivraison['id_adresse'];

        // 4. Session Stripe
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Achat panier Cube',
                        ],
                        'unit_amount' => intval($total * 100),
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&id_adresse=' . $idAdresseFinale,
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function successStripe(Request $request)
    {
        $request->validate([
            'id_adresse' => 'required|exists:adresse,id_adresse',
            'session_id' => 'required'
        ]);

        $idAdresse = $request->query('id_adresse');

        $infosLivraison = $this->getInfosLivraison($idAdresse);

        $commande = $this->insertionCommande($idAdresse, 'CB', $infosLivraison['id_type_livraison']);

        if (!$commande) {
            return redirect()->route('cart.index')->with('error', 'Erreur lors de la création de la commande.');
        }

        $this->finaliserCodePromo(Auth::id());

        $panier = Panier::where('id_client', Auth::id())->first();
        if ($panier) {
            LignePanier::where('id_panier', $panier->id_panier)->delete();
        }

        session()->forget('id_magasin_choisi');

        return redirect()->route('client.commandes.show', $commande->id_commande)
            ->with('success', 'Paiement Stripe validé !');
    }

    public function cancelPayment()
    {
        return redirect()->route('cart.index')->with('error', 'Vous avez annulé le paiement.');
    }

    private function finaliserCodePromo($clientId)
    {
        $panier = Panier::where('id_client', $clientId)->first();
        $client = Auth::user();

        if ($panier && $panier->code_promo) {

            if (!$client->codesPromoUtilises()->where('utilisation_code_promo.id_codepromo', $panier->code_promo)->exists()) {
                $client->codesPromoUtilises()->attach($panier->code_promo);
            }
            $panier->code_promo = null;
            $panier->save();
        }
    }
}