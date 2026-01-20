<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LignePanier;
use App\Models\LigneCommande;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\CodePromo;
use App\Models\MagasinPartenaire;
use App\Models\VarianteVelo;
use App\Models\Adresse;

class PaymentController extends Controller
{

    public function paymentShow()
    {
        $client = Auth::user();
        $adresses = $client->adressesLivraison()->get();

  
        $magasin = MagasinPartenaire::with('adresses')->find($client->id_magasin);


        $panier = Panier::where('id_client', $client->id_client)->first();
        
        $contientVelo = false;
        if ($panier) {
            $contientVelo = LignePanier::where('id_panier', $panier->id_panier)
                ->whereIn('reference', VarianteVelo::select('reference'))
                ->exists();
        }

        return view('commande', compact('client', 'adresses', 'magasin', 'contientVelo'));
    }


    private function getOrCreateAddress(Request $request)
    {
        $mode = $request->input('delivery_mode');

        // Retrait en magasin
        if ($mode === 'magasin') {
            
            return null;
        }

      
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

        // Création de l'adresse
        $adresse = new Adresse();
        $adresse->rue = $request->rue;
        $adresse->code_postal = $request->zipcode;
        $adresse->ville = $request->city;
        $adresse->pays = $request->country;
        $adresse->save();

        // Liaison au client
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
        // Si une adresse précise a été résolue (Livraison Domicile)
        if ($adresseId) {
            return [
                'id_adresse' => $adresseId,
                'id_type_livraison' => 1 
            ];
        }

       
        $client = Auth::user();
        

        $idMagasin = $client->id_magasin;
        
       
        if (!$idMagasin && session()->has('id_magasin_choisi')) {
            $idMagasin = session('id_magasin_choisi');
        }

        if ($idMagasin) {
            $magasin = MagasinPartenaire::with('adresses')->find($idMagasin);
     
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

        if (!$panier) return 0;

        $panierItems = LignePanier::where('id_panier', $panier->id_panier)->with(['article'])->get();
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

        return max($subTotal - $discountAmount, 0);
    }

  
    public function paymentPaypal(Request $request)
    {
        try {
            $idAdresseResolue = $this->getOrCreateAddress($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        $total = $this->calculerTotalPanier();
        if ($total <= 0) return redirect()->route('cart.index')->with('error', 'Panier vide.');

      
        $infos = $this->getInfosLivraison($idAdresseResolue);
        
        if (!$infos['id_adresse']) {
            return redirect()->back()->with('error', 'Impossible de déterminer l\'adresse de livraison (ou magasin introuvable).');
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.success', ['id_adresse' => $infos['id_adresse']]),
                "cancel_url" => route('paypal.cancel'),
            ],
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "EUR",
                    "value" => number_format($total, 2, '.', '')
                ]
            ]]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') return redirect()->away($link['href']);
            }
        }

        return redirect()->route('paypal.cancel')->with('error', 'Erreur PayPal.');
    }

    public function successPaypal(Request $request)
    {
        $request->validate(['id_adresse' => 'required', 'token' => 'required']);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $idAdresse = $request->query('id_adresse');
           
            $typeLivraison = 1; 
      
            
            return $this->finalizeOrder($idAdresse, 'Paypal');
        }

        return redirect()->route('paypal.cancel');
    }


    public function paymentStripe(Request $request)
    {
        try {
            $idAdresseResolue = $this->getOrCreateAddress($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        $total = $this->calculerTotalPanier();
        if ($total <= 0) return redirect()->route('cart.index')->with('error', 'Panier vide.');

        // Récupération infos finales
        $infos = $this->getInfosLivraison($idAdresseResolue);

        if (!$infos['id_adresse']) {
            return redirect()->back()->with('error', 'Impossible de déterminer l\'adresse de livraison. Veuillez sélectionner un magasin ou une adresse valide.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Commande Cube Bikes'],
                    'unit_amount' => intval($total * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            // On passe l'ID adresse dans les métadonnées pour la sécurité
            'metadata' => [
                'id_adresse_livraison' => $infos['id_adresse'],
                'id_client' => Auth::id(),
                'type_livraison' => $infos['id_type_livraison']
            ],
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function successStripe(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if(!$sessionId) {
            return redirect()->route('cart.index')->with('error', 'Session invalide.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($sessionId);

        // On récupère les infos stockées dans les métadonnées
        $idAdresse = $session->metadata->id_adresse_livraison;
        $typeLivraison = $session->metadata->type_livraison; // Optionnel si on veut le stocker

        return $this->finalizeOrder($idAdresse, 'CB', $typeLivraison);
    }


    private function finalizeOrder($idAdresse, $modePaiement, $typeLivraison = 1)
    {
        $clientId = Auth::id();
        $total = $this->calculerTotalPanier();
        $panier = Panier::where('id_client', $clientId)->first();

        if (!$panier) return redirect()->route('cart.index');

        // Création de la commande
        $commande = Commande::create([
            'id_adresse' => $idAdresse,
            'id_client' => $clientId,
            'id_type_livraison' => $typeLivraison,
            'id_panier' => $panier->id_panier,
            'date_commande' => now(),
            'montant_total_commande' => $total,
            'cout_livraison' => 0,
            'type_paiement' => $modePaiement,
            'statut_livraison' => 'cree', 
        ]);

        // Transfert des lignes
        $panierItems = LignePanier::where('id_panier', $panier->id_panier)->with('article')->get();

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

        // Finalisation Code Promo
        $this->finaliserCodePromo($clientId);

        // Suppression Panier
        LignePanier::where('id_panier', $panier->id_panier)->delete();
        
        // Nettoyage Session
        session()->forget('id_magasin_choisi');

        return redirect()->route('client.commandes.show', $commande->id_commande)
            ->with('success', 'Paiement ' . $modePaiement . ' validé avec succès !');
    }

    public function cancelPayment()
    {
        return redirect()->route('cart.index')->with('error', 'Paiement annulé.');
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