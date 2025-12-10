<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use App\Models\LignePanier; 
use App\Models\Panier; 
use App\Models\Commande; 

class PaymentController extends Controller
{
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

    public function paymentPaypal()
    {
        $totalPanier = $this->calculerTotalPanier();

        if ($totalPanier <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $totalFormatted = number_format($totalPanier, 2, '.', '');

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

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
        } else {
            return redirect()->route('paypal.cancel');
        }
    }

    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            
            $panier = Panier::where('id_client', Auth::id())->first();
            
            if ($panier) {
                // Supprimer les lignes du panier
                LignePanier::where('id_panier', $panier->id_panier)->delete();
            }

            // Optionnel : Créer la commande dans la table 'commandes'
            // $commande = new Commande();
            // $commande->id_panier = $panier->id_panier;
            // $commande->save();

            return redirect()->route('home')->with('success', 'Paiement validé ! Merci pour votre achat.');
        } else {
            return redirect()->route('paypal.cancel');
        }
    }

    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Paiement annulé.');
    }
}
