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
        
        $panierItems = LignePanier::whereHas('panier', function ($query) use ($userId) {
            $query->where('id_client', $userId);
        })
        ->with(['article']) 
        ->get();

        $total = 0;

        foreach ($panierItems as $item) {
            $prixUnitaire = 0;

            if ($item->article) {
                $prixUnitaire = $item->article->prix;
            }

            $total += $prixUnitaire * $item->quantite_article; 
        }

        return $total;
    }

    private function insertionCommande($idAdresse)
    {
        $clientId = Auth::id();
        $total = $this->calculerTotalPanier();
        $panier = Panier::where('id_client', $clientId)->first();
    
        $commande = Commande::create([
            'id_adresse' => $idAdresse,
            'id_client' => $clientId,
            'id_type_livraison' => 1,
            'id_panier' => $panier->id_panier,
            'date_commande' => now(),
            'montant_total_commande' => $total,
            'cout_livraison' => 0,
            'statut_livraison' => 'cree',
        ]);
    
        $panierItems = LignePanier::where('id_panier', $panier->id_panier)->with(['article']) ->get();
    
        foreach ($panierItems as $item) {
            LigneCommande::create([
                'id_commande' => $commande->id_commande,
                'reference' => $item->article->reference,
                'quantite_article_commande' => $item->quantite_article,
                'prix_unitaire_article' => $item->article->prix,
                'taille_selectionnee' => $item->taille_selectionnee,  
            ]);
        }
        return $commande;
    }
    
    public function paymentPaypal(Request $request) 
    {
        $request->validate([
            'id_adresse' => 'required|exists:adresse,id_adresse',
        ]);
    
        $idAdresse = $request->id_adresse;
        
        $commande = $this->insertionCommande($idAdresse);
    
        $total = $this->calculerTotalPanier();
    
        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }
    
        $totalFormatted = number_format($total, 2, '.', ''); 
    
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
    
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('paypal.success'),
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
    
        return redirect()->route('paypal.cancel');
    }
    

    public function successPaypal(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            
            $panier = Panier::where('id_client', Auth::id())->first();
            
            if ($panier) {
                LignePanier::where('id_panier', $panier->id_panier)->delete();
            }

            return redirect()->route('home')->with('success', 'Paiement validé ! Merci pour votre achat.');
        } 
        else 
        {
            return redirect()->route('paypal.cancel');
        }
    }

    public function paymentStripe(Request $request)
    {
        $request->validate([
            'id_adresse' => 'required|exists:adresse,id_adresse',
        ]);
    
        $idAdresse = $request->id_adresse;
        
        $commande = $this->insertionCommande($idAdresse);

        $total = $this->calculerTotalPanier();

        if ($total <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Achat panier Cubve',
                    ],
                    'unit_amount' => intval($total * 100), 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function successStripe()
    {
        $panier = Panier::where('id_client', Auth::id())->first();

        if ($panier) {
            LignePanier::where('id_panier', $panier->id_panier)->delete();
        }

        return redirect()->route('home')->with('success', 'Paiement Stripe validé !');
    }

    public function cancelPayment()
    {
        $commande = Commande::where('id_client', Auth::id())->latest()->first();
    
        if ($commande) {
            LigneCommande::where('id_commande', $commande->id_commande)->delete();
    
            $commande->delete();
    
            return redirect()->route('cart.index')->with('error', 'Paiement annulé et commande supprimée.');
        }
    
        return redirect()->route('cart.index')->with('error', 'Aucune commande à annuler.');
    }
}
