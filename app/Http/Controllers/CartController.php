<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Affiche le panier
    public function index()
    {
        $cart = Session::get('cart', []);
        
        // Calcul du total
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // On retourne la vue 'panier.blade.php'
        return view('panier', compact('cart', 'total'));    
    }

    // Ajoute un item
    public function add(Request $request, $reference)
    {
        // Validation
        $request->validate([
            'taille' => 'required|string', 
            'quantity' => 'required|integer|min:1'
        ]);

        $velo = VarianteVelo::with('photos')->findOrFail($reference);
        
        $cart = Session::get('cart', []);

        // Clé unique (Référence + Taille)
        $cartKey = $reference . '-' . $request->taille;

        // --- MODIFICATION ICI ---
        // On récupère la photo principale, sinon la première dispo, sinon NULL
        // Si c'est NULL, la vue affichera le placeholder "No Image"
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
                'image' => $photoUrl
            ];
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Vélo ajouté au panier !');
    }

    // Supprime un item
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