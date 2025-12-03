<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // ... (méthode index inchangée) ...
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('panier', compact('cart', 'total'));    
    }

    // Ajoute un item
    public function add(Request $request, $reference)
    {
        $request->validate([
            'taille' => 'required|string', 
            'quantity' => 'required|integer|min:1'
        ]);

        $velo = VarianteVelo::with(['modele', 'photos'])->findOrFail($reference);
        
        $cart = Session::get('cart', []);
        $cartKey = $reference . '-' . $request->taille;

        $photo = $velo->photos->where('est_principale', true)->first() ?? $velo->photos->first();
        $photoUrl = $photo ? $photo->url_photo : null;

        if(isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'reference' => $velo->reference,
                'name' => $velo->nom_article,
                'price' => $velo->prix,
                'taille' => $request->taille,
                'quantity' => $request->quantity,
                'image' => $photoUrl,
                'model_name' => $velo->modele->nom_modele ?? ''
            ];
        }

        Session::put('cart', $cart);

        // Calcul du total du panier pour la modale
        $cartTotal = 0;
        $cartCount = 0;
        foreach($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }

        // SI LA REQUÊTE EST EN AJAX (JSON)
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté !',
                'product' => [
                    'name' => $velo->nom_article,
                    'price' => $velo->prix,
                    'image' => $photoUrl ? asset('storage/' . $photoUrl) : 'https://placehold.co/200x150?text=No+Image',
                    'taille' => $request->taille,
                    'qty' => $request->quantity
                ],
                'cart' => [
                    'count' => $cartCount,
                    'subtotal' => $cartTotal,
                    'total' => $cartTotal // + frais de port si besoin
                ]
            ]);
        }

        // Fallback classique si pas de JS
        return redirect()->route('cart.index')->with('success', 'Vélo ajouté au panier !');
    }

    // ... (méthode remove inchangée) ...
    public function remove($key)
    {
        $cart = Session::get('cart', []);
        if(isset($cart[$key])) {
            unset($cart[$key]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Article retiré.');
    }
}