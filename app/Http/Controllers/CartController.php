<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // --- 1. AFFICHAGE DU PANIER (CORRIGÉ AVEC LEFT JOIN) ---
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $key => &$item) {
            $total += $item['price'] * $item['quantity'];

            // On nettoie le label de la taille (ex: "XL (185-195)" devient "XL")
            // Si pas de taille (accessoire), on garde "Unique" ou vide
            $tailleLabel = isset($item['taille']) ? explode(' ', trim($item['taille']))[0] : null;

            // REQUÊTE STOCK : Utilisation de LEFT JOIN pour ne pas exclure les accessoires sans taille
            $query = DB::table('article_inventaire as vvi')
                ->leftJoin('inventaire_magasin as im', 'vvi.id_article_inventaire', '=', 'im.id_article_inventaire')
                ->select(DB::raw('
                    (vvi.quantite_stock_en_ligne + COALESCE(SUM(im.quantite_stock_magasin), 0)) as total_stock
                '))
                ->where('vvi.reference', $item['reference']);

            // Si c'est un vélo (donc on a une taille spécifique différente de "Unique")
            if ($tailleLabel && $tailleLabel !== 'Unique') {
                $query->join('taille as t', 'vvi.id_taille', '=', 't.id_taille')
                      ->where('t.taille', $tailleLabel);
            }

            $stockInfo = $query->groupBy('vvi.id_article_inventaire', 'vvi.quantite_stock_en_ligne')->first();

            // Si on ne trouve pas de ligne d'inventaire, on met 0 par sécurité, sinon on met le stock trouvé
            $item['max_stock'] = $stockInfo ? $stockInfo->total_stock : 0;
        }
        unset($item); // Bonnes pratiques PHP après un foreach par référence

        return view('panier', compact('cart', 'total'));
    }

    // --- 2. AJOUT VÉLO (Ton code d'origine conservé) ---
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

    // --- 3. AJOUT ACCESSOIRE (VERSION CORRIGÉE ET ROBUSTE) ---
    public function addAccessoire(Request $request, $reference)
    {
        // Validation simple
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Récupération Accessoire
        $accessoire = Accessoire::with('photos')->where('reference', $reference)->firstOrFail();

        // --- CALCUL DU STOCK RÉEL (Correction) ---
        
        // 1. On cherche la ligne d'inventaire correspondant à la référence.
        // On NE joint PAS la table taille ici pour éviter les erreurs si l'accessoire n'a pas d'ID taille.
        $inventaire = DB::table('article_inventaire')
                        ->where('reference', $reference)
                        ->select('id_article_inventaire', 'quantite_stock_en_ligne')
                        ->first();

        // Stock par défaut à 0 si introuvable
        $stockWeb = $inventaire ? $inventaire->quantite_stock_en_ligne : 0;
        $stockMagasins = 0;

        if ($inventaire) {
            // 2. Si on a trouvé l'article, on somme les stocks magasins
            $stockMagasins = DB::table('inventaire_magasin')
                                ->where('id_article_inventaire', $inventaire->id_article_inventaire)
                                ->sum('quantite_stock_magasin');
        }

        $stockTotal = $stockWeb + $stockMagasins;

        // --- VÉRIFICATION PANIER ---
        $cart = Session::get('cart', []);
        
        // Pour les accessoires, la clé est simplement la référence
        $cartKey = $reference; 
        $currentQtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;

        // Si la quantité demandée + ce qu'on a déjà dépasse le stock total
        if (($currentQtyInCart + $request->quantity) > $stockTotal) {
            
            // Si stock est 0, message spécifique
            if ($stockTotal <= 0) {
                $msg = "Cet article est actuellement en rupture de stock.";
            } else {
                $msg = "Stock insuffisant. Il ne reste que " . $stockTotal . " exemplaire(s) disponible(s).";
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // --- AJOUT AU PANIER ---
        $photo = $accessoire->photos->where('est_principale', true)->first() ?? $accessoire->photos->first();
        $photoUrl = $photo ? $photo->url_photo : null;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'reference' => $accessoire->reference,
                'name' => $accessoire->nom_article,
                'price' => $accessoire->prix,
                'taille' => 'Unique',
                'quantity' => $request->quantity,
                'image' => $photoUrl,
                'type' => 'accessoire',
                'max_stock' => $stockTotal 
            ];
        }

        Session::put('cart', $cart);

        // Calculs totaux JSON
        $cartTotal = 0;
        $cartCount = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Accessoire ajouté !',
                'product' => [
                    'name' => $accessoire->nom_article,
                    'price' => $accessoire->prix,
                    'image' => $photoUrl ? (filter_var($photoUrl, FILTER_VALIDATE_URL) ? $photoUrl : asset('storage/' . $photoUrl)) : 'https://placehold.co/200x150?text=No+Image',
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

    // --- 4. SUPPRESSION ---
    public function remove($key)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Article retiré.');
    }

    // --- 5. MISE À JOUR QUANTITÉ (AJAX) ---
    public function updateQuantity(Request $request)
    {
        $id = $request->input('id');
        $quantity = $request->input('quantity');

        $cart = Session::get('cart');

        if (isset($cart[$id])) {
            // Petite sécurité supplémentaire ici aussi
            $maxStock = $cart[$id]['max_stock'] ?? 999;
            if($quantity > $maxStock) {
                 return response()->json([
                    'success' => false, 
                    'message' => "Stock insuffisant (Max: $maxStock)"
                ], 400);
            }

            $cart[$id]['quantity'] = $quantity;
            Session::put('cart', $cart);

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