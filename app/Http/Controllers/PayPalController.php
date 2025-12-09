<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use App\Models\LignePanier; // Assure-toi que ce modèle existe
use App\Models\Panier; // Ajout du modèle Panier
use App\Models\Commande; // Optionnel : si tu veux créer la commande après

class PayPalController extends Controller
{
    private function calculerTotalPanier()
    {
        $userId = Auth::id();
        
        // On récupère les lignes du panier de l'utilisateur connecté
        // On charge aussi les relations 'article' pour avoir les prix
        $panierItems = LignePanier::whereHas('panier', function ($query) use ($userId) {
            $query->where('id_client', $userId);
        })
        ->with(['article']) // Chargement des articles
        ->get();

        $total = 0;

        foreach ($panierItems as $item) {
            $prixUnitaire = 0;

            // Vérification si l'article existe et récupération du prix
            if ($item->article) {
                $prixUnitaire = $item->article->prix;
            }

            // Calcul : Prix * Quantité
            $total += $prixUnitaire * $item->quantite_article; // Utilisation de 'quantite_article'
        }

        return $total;
    }

    public function payment()
    {
        $totalPanier = $this->calculerTotalPanier();

        // Si le total est à 0, on redirige avec une erreur
        if ($totalPanier <= 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // Formater le montant pour Paypal (2 décimales)
        $totalFormatted = number_format($totalPanier, 2, '.', '');

        // Créer la commande PayPal
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

        // Si la commande a été créée avec succès, on redirige vers Paypal
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
            
            // Récupérer le panier de l'utilisateur
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
