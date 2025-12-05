<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CategorieVelo; 
use App\Models\CategorieAccessoire; 
use App\Models\Panier;
use App\Models\LignePanier;

class HeaderComposer
{
    public function compose(View $view)
    {
        $menuVelo = CategorieVelo::whereNull('cat_id_categorie')
            ->whereHas('enfants.modeles', function (Builder $q) {
                $q->where('type_velo', 'musculaire');
            })
            ->with(['enfants' => function ($q) {
                $q->whereHas('modeles', function ($q2) {
                    $q2->where('type_velo', 'musculaire');
                })
                ->with(['modeles' => function ($q3) {
                    $q3->where('type_velo', 'musculaire');
                }]);
            }])
            ->get();

        $menuElec = CategorieVelo::whereNull('cat_id_categorie')
            ->whereHas('enfants.modeles', function (Builder $q) {
                $q->where('type_velo', 'electrique');
            })
            ->with(['enfants' => function ($q) {
                $q->whereHas('modeles', function ($q2) {
                    $q2->where('type_velo', 'electrique');
                })
                ->with(['modeles' => function ($q3) {
                    $q3->where('type_velo', 'electrique');
                }]);
            }])
            ->get();

        $categorieAccessoires = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')
            ->with('enfants.enfants')->get();

        $cartItems = [];
        $cartTotal = 0;

        if (Auth::check()) {
            $panier = Panier::where('id_client', Auth::id())->first();

            if ($panier) {
                $lignes = LignePanier::with(['article'])
                    ->where('id_panier', $panier->id_panier)
                    ->get();

                foreach ($lignes as $ligne) {
                    $article = $ligne->article;
                    if (!$article) continue;

                    $cartItems[] = [
                        'reference' => $ligne->reference,
                        'name'      => $article->nom_article,
                        'price'     => (float) $article->prix,
                        'quantity'  => $ligne->quantite_article,
                        'taille'    => $ligne->taille_selectionnee,
                        'image'     => $this->getImageLocale($article->reference),
                        'max_stock' => 10 
                    ];
                }
            }
        } else {
            $sessionData = Session::get('cart', []);
            foreach($sessionData as $key => $item) {
                $imgUrl = $this->getImageLocale($item['reference']);
                $cartItems[] = [
                    'reference' => $item['reference'],
                    'name'      => $item['name'],
                    'price'     => (float) $item['price'],
                    'quantity'  => $item['quantity'],
                    'taille'    => $item['taille'] ?? 'Non renseigné',
                    'image'     => $imgUrl,
                    'max_stock' => $item['max_stock'] ?? 10
                ];
            }
        }

        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }

        $view->with([
            'menuVelo'             => $menuVelo,
            'menuElec'             => $menuElec,
            'categorieAccessoires' => $categorieAccessoires,
            'cartItems'            => $cartItems,
            'cartTotal'            => $cartTotal,
        ]);
    }

    private function getImageLocale($reference)
    {
        $ref = trim((string)$reference);
        $isAccessoire = strlen($ref) <= 5;
        $dossier = $isAccessoire ? 'ACCESSOIRES' : 'VELOS';
        $prefixLength = $isAccessoire ? 5 : 6;
        $refDossier = (strlen($ref) >= $prefixLength) ? substr($ref, 0, $prefixLength) : $ref;

        return asset('images/' . $dossier . '/' . $refDossier . '/image_1.jpg');
    }
}