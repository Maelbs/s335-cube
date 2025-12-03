<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB; // INDISPENSABLE


class CartController extends Controller
{
    // --- AFFICHAGE AVEC CALCUL DE STOCK RÉEL ---
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $key => &$item) {
            $total += $item['price'] * $item['quantity'];

            // 1. Nettoyage de la taille (ex: "XL (181-195)" -> "XL")
            $tailleLabel = explode(' ', trim($item['taille']))[0];

            // 2. Requête SQL : Stock Web + Somme des Stocks Magasins
            $stockInfo = DB::table('article_inventaire as vvi')
                ->join('taille as t', 'vvi.id_taille', '=', 't.id_taille')
                ->leftJoin('inventaire_magasin as im', 'vvi.id_article_inventaire', '=', 'im.id_article_inventaire')
                ->select(DB::raw('
                    (vvi.quantite_stock_en_ligne + COALESCE(SUM(im.quantite_stock_magasin), 0)) as total_stock
                '))
                ->where('vvi.reference', $item['reference'])
                ->where('t.taille', $tailleLabel)
                ->groupBy('vvi.id_article_inventaire', 'vvi.quantite_stock_en_ligne')
                ->first();

            // 3. Injection du stock dans l'item du panier
            $item['max_stock'] = $stockInfo ? $stockInfo->total_stock : 0;
        }
        unset($item); // Sécurité PHP

        return view('panier', compact('cart', 'total'));
    }

    // --- AJOUT (TON CODE D'ORIGINE) ---
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

        if (isset($cart[$cartKey])) {
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

        // Calcul totaux pour JSON
        $cartTotal = 0;
        $cartCount = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté !',
                'product' => [
                    'name' => $velo->nom_article,
                    'price' => $velo->prix,
                    'image' => $photoUrl ? (filter_var($photoUrl, FILTER_VALIDATE_URL) ? $photoUrl : asset('storage/' . $photoUrl)) : 'https://placehold.co/200x150?text=No+Image',
                    'taille' => $request->taille,
                    'qty' => $request->quantity
                ],
                'cart' => [
                    'count' => $cartCount,
                    'subtotal' => $cartTotal,
                    'total' => $cartTotal
                ]
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Vélo ajouté au panier !');
    }

    public function addAccessoire(Request $request, $reference)
    {
        // 1. Validation (Pas de taille requise pour un accessoire)
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // 2. Récupération de l'accessoire
        // On assume que le modèle Accessoire a la relation 'photos' (comme VarianteVelo)
        $accessoire = Accessoire::with('photos')->where('reference', $reference)->firstOrFail();

        // 3. Gestion du panier (Session)
        $cart = Session::get('cart', []);

        // Clé simple car pas de taille
        $cartKey = $reference;

        // Image
        $photo = $accessoire->photos->where('est_principale', true)->first() ?? $accessoire->photos->first();
        $photoUrl = $photo ? $photo->url_photo : null;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'reference' => $accessoire->reference,
                'name' => $accessoire->nom_article,
                'price' => $accessoire->prix,
                'taille' => 'Unique', // Taille par défaut pour l'affichage
                'quantity' => $request->quantity,
                'image' => $photoUrl,
                'type' => 'accessoire'
            ];
        }

        Session::put('cart', $cart);

        // Calculs totaux pour la modale AJAX
        $cartTotal = 0;
        $cartCount = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }

        // Réponse JSON pour le JavaScript
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Accessoire ajouté !',
                'product' => [
                    'name' => $accessoire->nom_article,
                    'price' => $accessoire->prix,
                    'image' => $photoUrl ? $photoUrl : 'https://placehold.co/200x150?text=No+Image', // Si l'URL est complète en BDD
                    'taille' => 'Unique',
                    'qty' => $request->quantity
                ],
                'cart' => [
                    'count' => $cartCount,
                    'subtotal' => $cartTotal,
                    'total' => $cartTotal
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Accessoire ajouté au panier !');
    }

    // --- SUPPRESSION (TON CODE D'ORIGINE) ---
    public function remove($key)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Article retiré.');
    }

    // --- NOUVEAU : MISE A JOUR QUANTITE (AJAX) ---
    public function updateQuantity(Request $request)
    {
        $id = $request->input('id');
        $quantity = $request->input('quantity');

        $cart = Session::get('cart');

        if (isset($cart[$id])) {
            // Sauvegarde en session : Empêche la réinitialisation si on supprime une autre ligne
            $cart[$id]['quantity'] = $quantity;
            Session::put('cart', $cart);

            // Recalcul du total global pour mettre à jour l'affichage
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'newTotal' => number_format($total, 2, ',', ' ')
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}