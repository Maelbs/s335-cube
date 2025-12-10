<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Auth;
use App\Models\LignePanier;
use App\Models\LigneCommande;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\CodePromo;

class PaymentController extends Controller
{
    public function paymentShow()
    {
        $client = Auth::user();
        $adresses = $client->adressesLivraison()->get();
        return view('commande', compact('client', 'adresses'));
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

    private function insertionCommande($idAdresse, $typePaiement)
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
            'id_type_livraison' => 1,
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
        $request->validate([
            'id_adresse' => 'required|exists:adresse,id_adresse', 
        ]);

        $total = $this->calculerTotalPanier();
    
        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
    
        $totalFormatted = number_format($total, 2, '.', '');
        $idAdresse = $request->id_adresse; 
    
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
    
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.success', ['id_adresse' => $idAdresse]),
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
            
            $commande = $this->insertionCommande($idAdresse, 'Paypal');
            
            if (!$commande) {
                return redirect()->route('cart.index')->with('error', 'Erreur lors de la création de la commande.');
            }

            $panier = Panier::where('id_client', Auth::id())->first();
            if ($panier) {
                LignePanier::where('id_panier', $panier->id_panier)->delete();
            }

            return redirect()->route('client.commandes.show', $commande -> id_commande)->with('success', 'Paiement PayPal validé ! Merci pour votre achat.');
        } 
        
        return redirect()->route('paypal.cancel')->with('error', 'Le paiement PayPal a échoué.');
    }

    public function paymentStripe(Request $request)
    {
        $request->validate([
            'id_adresse' => 'required|exists:adresse,id_adresse',
        ]);

        $total = $this->calculerTotalPanier();

        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $idAdresse = $request->id_adresse;

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Achat panier Cube',
                    ],
                    'unit_amount' => intval($total * 100), 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&id_adresse=' . $idAdresse,
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

        $commande = $this->insertionCommande($idAdresse, 'CB');

        if (!$commande) {
            return redirect()->route('cart.index')->with('error', 'Erreur lors de la création de la commande.');
        }

        $panier = Panier::where('id_client', Auth::id())->first();

        if ($panier) {
            LignePanier::where('id_panier', $panier->id_panier)->delete();
        }

        return redirect()->route('client.commandes.show', $commande -> id_commande)->with('success', 'Paiement Stripe validé !');
    }

    public function cancelPayment()
    {
        return redirect()->route('cart.index')->with('error', 'Vous avez annulé le paiement.');
    }
}