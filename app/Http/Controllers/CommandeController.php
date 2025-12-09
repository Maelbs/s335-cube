<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Panier;
use App\Models\LignePanier;
use App\Models\Article;
use App\Models\Commande;
use App\Models\AdresseLivraison;
use App\Models\Magasin;
use App\Models\Client;

class CommandeController extends Controller
{
    public function showCommande()
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour passer une commande.');
        }

        // Récupérer le panier de l'utilisateur connecté
        $panier = Panier::where('id_client', Auth::id())->first();

        // Si aucun panier trouvé
        if (!$panier) {
            return redirect()->route('panier')->with('error', 'Votre panier est vide.');
        }

        // Récupérer les lignes du panier (les produits) et calculer le total
        $lignes = LignePanier::with('article')->where('id_panier', $panier->id_panier)->get();
        $total = 0;

        $cart = [];
        foreach ($lignes as $ligne) {
            $article = $ligne->article;
            if ($article) {
                $cart[] = [
                    'name'     => $article->nom_article,
                    'quantity' => $ligne->quantite_article,
                    'price'    => (float) $article->prix,
                    'total'    => (float) $article->prix * $ligne->quantite_article,
                ];

                $total += $ligne->quantite_article * $article->prix;
            }
        }

        // Retourner la vue avec les détails du panier et le total
        return view('commande', compact('cart', 'total'));
    }

    // Affiche le formulaire pour choisir le type de livraison
    public function choisirLivraison()
    {
        // Récupérer les magasins disponibles (si livraison en magasin)
        $magasins = Magasin::all();

        // Récupérer les adresses de livraison du client (si livraison à domicile)
        $adresses = AdresseLivraison::where('id_client', Auth::id())->get();

        // Afficher la vue avec les magasins et adresses de livraison
        return view('commande.livraison', compact('magasins', 'adresses'));
    }

    // Affiche le formulaire pour le paiement
    public function choisirPaiement(Request $request)
    {
        // Valider que le type de livraison est sélectionné
        $request->validate([
            'livraison' => 'required|in:magasin,domicile',
            'adresse_id' => 'nullable|exists:adresse_livraison,id',
            'magasin_id' => 'nullable|exists:magasins,id'
        ]);

        // Récupérer le panier de l'utilisateur
        $cart = $this->getCartDetails();

        // Calculer le total
        $total = $this->calculateTotal($cart);

        // Afficher la vue de paiement avec les détails du panier et total
        return view('commande.paiement', compact('cart', 'total', 'request'));
    }

    // Effectue le paiement et crée la commande
    public function payer(Request $request)
    {
        // Validation des informations de paiement
        $request->validate([
            'paiement_method' => 'required|in:credit_card,paypal',
            'livraison' => 'required|in:magasin,domicile',
            'adresse_id' => 'nullable|exists:adresse_livraison,id',
            'magasin_id' => 'nullable|exists:magasins,id',
            'card_number' => 'nullable|string', // Pour simplifier, ajouter ici le contrôle pour les paiements par carte.
            // Ajoute d'autres validations de paiement selon ton processus
        ]);

        // Commencer une transaction pour s'assurer que tout se passe bien ou rien ne se fait
        DB::beginTransaction();

        try {
            // Récupérer le panier de l'utilisateur
            $cart = $this->getCartDetails();
            $total = $this->calculateTotal($cart);

            // Créer la commande
            $commande = new Commande();
            $commande->id_client = Auth::id();
            $commande->montant_total = $total;
            $commande->type_livraison = $request->livraison;
            $commande->mode_paiement = $request->paiement_method;
            $commande->adresse_livraison_id = $request->livraison == 'domicile' ? $request->adresse_id : null;
            $commande->magasin_id = $request->livraison == 'magasin' ? $request->magasin_id : null;
            $commande->status = 'en_attente'; // On marque la commande comme en attente
            $commande->save();

            // Enregistrer les lignes de commande
            foreach ($cart as $item) {
                $ligne = new LigneCommande();
                $ligne->id_commande = $commande->id_commande;
                $ligne->reference = $item['reference'];
                $ligne->quantite = $item['quantity'];
                $ligne->prix_unitaire = $item['price'];
                $ligne->prix_total = $item['total'];
                $ligne->save();
            }

            // Si tout est ok, valider la transaction
            DB::commit();

            // Vider le panier après la commande réussie
            if (Auth::check()) {
                Panier::where('id_client', Auth::id())->delete(); // Supprimer le panier de la base de données
            } else {
                Session::forget('cart'); // Vider la session pour les utilisateurs non connectés
            }

            return redirect()->route('commande.success')->with('success', 'Commande passée avec succès !');
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            
            // Supprimer la commande en cas d'échec
            if (isset($commande)) {
                $commande->delete(); // Supprimer la commande
            }

            return redirect()->route('commande.error')->with('error', 'Une erreur est survenue lors du paiement.');
        }
    }

    // Fonction pour récupérer les détails du panier
    private function getCartDetails()
    {
        $cart = [];

        if (Auth::check()) {
            // Si l'utilisateur est connecté, récupérer les lignes du panier en base de données
            $panier = Panier::where('id_client', Auth::id())->first();
            $lignes = LignePanier::with('article')
                ->where('id_panier', $panier->id_panier)
                ->get();

            foreach ($lignes as $ligne) {
                $article = $ligne->article;
                if ($article) {
                    $cart[] = [
                        'reference' => $ligne->reference,
                        'name' => $article->nom_article,
                        'price' => (float)$article->prix,
                        'quantity' => $ligne->quantite_article,
                        'total' => $ligne->quantite_article * $article->prix
                    ];
                }
            }
        } else {
            // Si l'utilisateur n'est pas connecté, récupérer les données du panier depuis la session
            $cart = Session::get('cart', []);
            foreach ($cart as $key => &$item) {
                $item['total'] = $item['quantity'] * $item['price'];
            }
        }

        return $cart;
    }

    // Fonction pour calculer le total du panier
    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['total'];
        }
        return $total;
    }

    public function finaliser(Request $request)
    {
        // Validation de la requête (en fonction de ce qui est passé par le formulaire)
        $request->validate([
            'livraison' => 'required|in:magasin,domicile',
            'adresse_id' => 'nullable|exists:adresse_livraison,id',
            'magasin_id' => 'nullable|exists:magasins,id',
            'paiement_method' => 'required|in:credit_card,paypal',
        ]);

        // Récupérer le panier de l'utilisateur
        $cart = $this->getCartDetails();
        $total = $this->calculateTotal($cart);

        // Commencer une transaction pour l'enregistrement de la commande
        DB::beginTransaction();

        try {
            // Créer la commande dans la base de données
            $commande = new Commande();
            $commande->id_client = Auth::id();
            $commande->montant_total = $total;
            $commande->type_livraison = $request->livraison;
            $commande->mode_paiement = $request->paiement_method;
            $commande->adresse_livraison_id = $request->livraison == 'domicile' ? $request->adresse_id : null;
            $commande->magasin_id = $request->livraison == 'magasin' ? $request->magasin_id : null;
            $commande->status = 'confirmée'; // On marque la commande comme confirmée
            $commande->save();

            // Enregistrer les lignes de commande
            foreach ($cart as $item) {
                $ligne = new LigneCommande();
                $ligne->id_commande = $commande->id_commande;
                $ligne->reference = $item['reference'];
                $ligne->quantite = $item['quantity'];
                $ligne->prix_unitaire = $item['price'];
                $ligne->prix_total = $item['total'];
                $ligne->save();
            }

            // Si tout est ok, valider la transaction
            DB::commit();

            // Vider le panier après la commande réussie
            if (Auth::check()) {
                Panier::where('id_client', Auth::id())->delete(); // Supprimer le panier de la base de données
            } else {
                Session::forget('cart'); // Vider la session pour les utilisateurs non connectés
            }

            // Retourner une réponse ou rediriger vers une page de succès
            return redirect()->route('commande.success')->with('success', 'Commande confirmée avec succès !');
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();

            // Supprimer la commande en cas d'échec
            if (isset($commande)) {
                $commande->delete(); // Supprimer la commande
            }

            // Rediriger l'utilisateur vers une page d'erreur
            return redirect()->route('commande.error')->with('error', 'Une erreur est survenue lors de la finalisation de la commande.');
        }
    }

}
