<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Article;
use App\Models\Panier;
use App\Models\LignePanier;
use App\Models\Taille;
use App\Models\CodePromo;
class PanierController extends Controller
{
    public function index()
    {
        
        $cart = [];
        $subTotal = 0;
        $discountAmount = 0;
        $promoCode = null;
        $total = 0;

        
        if (Auth::check()) {
            $panier = Panier::firstOrCreate(
                ['id_client' => Auth::id()],
                ['date_creation' => now(), 'montant_total_panier' => 0]
            );

            
            $lignes = LignePanier::with(['article'])
                ->where('id_panier', $panier->id_panier)
                ->get();

            foreach ($lignes as $ligne) {
                $article = $ligne->article;
                if (!$article)
                    continue;

                $stockMax = $this->calculerStockMax($ligne->reference, $ligne->taille_selectionnee);
                $key = $ligne->reference . '-' . $ligne->taille_selectionnee;

                $cart[$key] = [
                    'reference' => $ligne->reference,
                    'name' => $article->nom_article,
                    'price' => (float) $article->prix,
                    'quantity' => $ligne->quantite_article,
                    'taille' => $ligne->taille_selectionnee,
                    'image' => $this->getImageLocale($article->reference),
                    'max_stock' => $stockMax
                ];
            }

            
            if ($panier->code_promo) {
                $promo = CodePromo::find($panier->code_promo);
                if ($promo) {
                    $promoCode = $promo->id_codepromo;
                    
                }
            }

        } else {
            
            $cart = Session::get('cart', []);

            
            foreach ($cart as $key => &$item) {
                $item['image'] = $this->getImageLocale($item['reference']);
                $item['max_stock'] = $this->calculerStockMax($item['reference'], $item['taille'] ?? 'Non renseigné');
            }
            unset($item);
            Session::put('cart', $cart);

            
            $sessionPromo = Session::get('promo');
            if ($sessionPromo) {
                $promoCode = $sessionPromo['code'];
            }
        }

        
        foreach ($cart as $item) {
            $subTotal += $item['price'] * $item['quantity'];
        }

        
        if ($promoCode) {
            $promoObj = CodePromo::find($promoCode);
            if ($promoObj) {
                $discountAmount = $subTotal * $promoObj->pourcentage;
            }
        }

        
        $total = $subTotal - $discountAmount;

       
        return view('panier', compact('cart', 'total', 'subTotal', 'discountAmount', 'promoCode'));
    }

    public function applyPromo(Request $request)
    {

        $code = trim($request->input('code_promo'));
    
   
        $promo = CodePromo::find($code);

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Ce code promo n\'existe pas.'
            ]);
        }

    
        if (Auth::check()) {
            /** @var \App\Models\Client $client */
            $client = Auth::user(); 

        
            if ($client->codesPromoUtilises()->where('utilisation_code_promo.id_codepromo', $promo->id_codepromo)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà utilisé ce code promo.'
                ]);
            }
        
        
            $panier = Panier::firstOrCreate(['id_client' => Auth::id()]);
            $panier->code_promo = $promo->id_codepromo;
            $panier->save();

        } else {
        
            session()->put('promo', [
                'code' => $promo->id_codepromo,
                'pourcentage' => $promo->pourcentage
            ]);
        }

    return response()->json([
        'success' => true,
        'message' => 'Code appliqué avec succès !'
    ]);
}
    public function add(Request $request, $reference)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'taille' => 'nullable|string',
        ]);

        $quantity = (int) $request->input('quantity');
        $taille = $this->normaliserTaille($request->input('taille'));

        $article = Article::find($reference);
        if (!$article) {
            return back()->withErrors('Article introuvable.');
        }

        $imgUrl = $this->getImageLocale($article->reference);
        $stockMax = $this->calculerStockMax($reference, $taille);

        if (Auth::check()) {
            $panier = Panier::firstOrCreate(
                ['id_client' => Auth::id()],
                ['date_creation' => now(), 'montant_total_panier' => 0]
            );

            $ligne = LignePanier::where('id_panier', $panier->id_panier)
                ->where('reference', $reference)
                ->where('taille_selectionnee', $taille)
                ->first();

            if ($ligne) {
                $newQty = $ligne->quantite_article + $quantity;
                if ($newQty > $stockMax)
                    $newQty = $stockMax;

                $ligne->quantite_article = $newQty;
                $ligne->save();
            } else {
                if ($quantity > $stockMax)
                    $quantity = $stockMax;

                LignePanier::create([
                    'id_panier' => $panier->id_panier,
                    'reference' => $reference,
                    'taille_selectionnee' => $taille,
                    'quantite_article' => $quantity
                ]);
            }
        } else {
            $cart = Session::get('cart', []);
            $key = $reference . '-' . $taille;

            $currentQty = 0;
            if (isset($cart[$key])) {
                $currentQty = $cart[$key]['quantity'];
            }

            $newQty = $currentQty + $quantity;
            if ($newQty > $stockMax)
                $newQty = $stockMax;

            if (!isset($cart[$key])) {
                $cart[$key] = [
                    'reference' => $reference,
                    'name' => $article->nom_article,
                    'price' => (float) $article->prix,
                    'quantity' => 0,
                    'taille' => $taille,
                    'image' => $imgUrl,
                    'max_stock' => $stockMax
                ];
            }

            $cart[$key]['quantity'] = $newQty;
            $cart[$key]['max_stock'] = $stockMax;

            Session::put('cart', $cart);
        }

        if ($request->wantsJson()) {
            $cartTotal = 0;
            $cartCount = 0;

            if (Auth::check()) {
                $panier = Panier::where('id_client', Auth::id())->first();
                if ($panier) {
                    $lignes = LignePanier::where('id_panier', $panier->id_panier)->with('article')->get();
                    $cartCount = $lignes->count();
                    foreach ($lignes as $l) {
                        $cartTotal += $l->article->prix * $l->quantite_article;
                    }
                }
            } else {
                $cart = Session::get('cart', []);
                $cartCount = count($cart);
                foreach ($cart as $item) {
                    $cartTotal += $item['price'] * $item['quantity'];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Article ajouté',
                'product' => [
                    'name' => $article->nom_article,
                    'price' => (float) $article->prix,
                    'image' => $imgUrl,
                    'taille' => $taille,
                    'qty' => $quantity
                ],
                'cart' => [
                    'count' => $cartCount,
                    'total' => $cartTotal
                ]
            ]);
        }

        return back()->with('success', 'Article ajouté au panier !');
    }

    public function remove(Request $request, $compositeKey)
    {
        [$ref, $taille] = $this->parseKey($compositeKey);

        if (Auth::check()) {
            $panier = Panier::where('id_client', Auth::id())->first();
            if ($panier) {
                LignePanier::where('id_panier', $panier->id_panier)
                    ->where('reference', $ref)
                    ->where('taille_selectionnee', $taille)
                    ->delete();
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$compositeKey])) {
                unset($cart[$compositeKey]);
                Session::put('cart', $cart);
            }
        }
        return back()->with('success', 'Article retiré du panier.');
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $compositeKey = $request->id;
        $qty = (int) $request->quantity;
        [$ref, $taille] = $this->parseKey($compositeKey);

        $stockMax = $this->calculerStockMax($ref, $taille);
        if ($qty > $stockMax)
            $qty = $stockMax;

        $newTotalPanier = 0;

        if (Auth::check()) {
            $panier = Panier::where('id_client', Auth::id())->first();
            if ($panier) {
                $ligne = LignePanier::where('id_panier', $panier->id_panier)
                    ->where('reference', $ref)
                    ->where('taille_selectionnee', $taille)
                    ->first();

                if ($ligne) {
                    $ligne->quantite_article = $qty;
                    $ligne->save();
                }

                $allLignes = LignePanier::where('id_panier', $panier->id_panier)->with('article')->get();
                foreach ($allLignes as $l) {
                    $newTotalPanier += $l->article->prix * $l->quantite_article;
                }
            }
        } else {
            $cart = Session::get('cart', []);
            if (isset($cart[$compositeKey])) {
                $cart[$compositeKey]['quantity'] = $qty;
                Session::put('cart', $cart);

                foreach ($cart as $item) {
                    $newTotalPanier += $item['price'] * $item['quantity'];
                }
            }
        }

        $formattedTotal = number_format($newTotalPanier, 2, ',', ' ');

        return response()->json([
            'success' => true,
            'newTotal' => $formattedTotal
        ]);
    }

    private function parseKey(string $key): array
    {
        $lastDash = strrpos($key, '-');
        if ($lastDash === false)
            return [$key, 'Non renseigné'];
        return [substr($key, 0, $lastDash), substr($key, $lastDash + 1)];
    }

    private function normaliserTaille($taille): string
    {
        if (empty($taille) || $taille === 'Unique' || $taille === 'null')
            return 'Non renseigné';
        return $taille;
    }

    private function getImageLocale($reference)
    {
        $ref = trim((string) $reference);
        $isAccessoire = strlen($ref) <= 5;
        $dossier = $isAccessoire ? 'ACCESSOIRES' : 'VELOS';
        $prefixLength = $isAccessoire ? 5 : 6;
        $refDossier = (strlen($ref) >= $prefixLength) ? substr($ref, 0, $prefixLength) : $ref;

        return asset('images/' . $dossier . '/' . $refDossier . '/image_1.jpg');
    }

    private function calculerStockMax($reference, $tailleNom)
    {
        $idTaille = null;
        if ($tailleNom && $tailleNom !== 'Non renseigné' && $tailleNom !== 'Unique') {
            $tailleObj = Taille::where('taille', $tailleNom)->first();
            if ($tailleObj) {
                $idTaille = $tailleObj->id_taille;
            }
        }

        $query = DB::table('article_inventaire')
            ->where('reference', $reference);

        if ($idTaille) {
            $query->where('id_taille', $idTaille);
        }

        $inventaires = $query->get();
        $totalStock = 0;

        foreach ($inventaires as $inv) {
            $stockWeb = $inv->quantite_stock_en_ligne ?? 0;
            $stockMagasins = DB::table('inventaire_magasin')
                ->where('id_article_inventaire', $inv->id_article_inventaire)
                ->sum('quantite_stock_magasin');

            $totalStock += ($stockWeb + $stockMagasins);
        }

        return $totalStock;
    }
}