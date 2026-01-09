<?php

namespace App\Http\Controllers;

use App\Models\CategorieVelo;
use App\Models\CategorieAccessoire;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use App\Models\Couleur;
use App\Models\Modele;
use App\Models\Fourche;
use App\Models\Batterie;
use App\Models\Taille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BoutiqueController extends Controller
{
    public function index(Request $request, $type, $cat_id = null, $sub_id = null, $model_id = null)
    {
        $isAccessoire = ($type === 'Accessoires');
        $isSearchMode = $request->filled('search');
        
        // On crée une clé de cache unique pour cette page spécifique
        $cacheKey = "filters_{$type}_" . ($cat_id ?? 'all') . "_" . ($sub_id ?? 'all') . "_" . ($model_id ?? 'all') . "_" . ($isSearchMode ? 'search' : 'normal');
        
        $titrePage = $isAccessoire ? "TOUS LES ACCESSOIRES" : "TOUS LES VÉLOS " . strtoupper($type) . "S";

        // -------------------------------------------------------
        // 1. CONSTRUCTION DE LA REQUÊTE (PRODUITS) - Identique à avant
        // -------------------------------------------------------
        if ($isAccessoire) {
            $query = Accessoire::query()->with(['parent', 'categorie', 'parent.photos', 'inventaires.taille']);
            
            if ($isSearchMode) {
                $search = strtolower($request->search);
                $term = '%' . $search . '%';
                $query->where(function($g) use ($term, $search) {
                     $g->where(function($sub) use ($term, $search) {
                        $sub->whereRaw('LOWER(nom_article) LIKE ?', [$term])
                            ->orWhereRaw('levenshtein(LOWER(nom_article)::text, ?::text) <= 1', [$search]);
                    })
                    ->orWhereHas('categorie', function($q) use ($term, $search) {
                        $q->whereRaw('LOWER(nom_categorie_accessoire) LIKE ?', [$term])
                        ->orWhereRaw('levenshtein(LOWER(nom_categorie_accessoire)::text, ?::text) <= 1', [$search]);
                    });
                });
                $titrePage = "RÉSULTATS : " . strtoupper($request->search);
            } else {
                 if ($model_id) { 
                    $query->where('id_categorie_accessoire', $model_id);
                    $titrePage = (CategorieAccessoire::find($model_id)->nom_categorie_accessoire ?? '');
                } elseif ($sub_id) {
                    $currCat = CategorieAccessoire::with('enfants')->find($sub_id);
                    if ($currCat) {
                        $ids = $currCat->enfants->pluck('id_categorie_accessoire');
                        $ids->push($currCat->id_categorie_accessoire);
                        $query->whereIn('id_categorie_accessoire', $ids);
                        $titrePage = $currCat->nom_categorie_accessoire;
                    }
                } elseif ($cat_id) {
                    $currCat = CategorieAccessoire::with('enfants')->find($cat_id);
                    if ($currCat) {
                        $ids = $currCat->enfants->pluck('id_categorie_accessoire');
                        $ids->push($currCat->id_categorie_accessoire);
                        $query->whereIn('id_categorie_accessoire', $ids);
                        $titrePage = $currCat->nom_categorie_accessoire;
                    }
                }
            }
            if ($request->filled('materiaux')) $query->whereIn('materiau', $request->materiaux);

        } else {
            // PARTIE VÉLO
            $dbType = ($type === 'Electrique') ? 'electrique' : 'musculaire';
            
            $query = VarianteVelo::query()
                ->with(['parent', 'modele', 'couleur', 'parent.photos', 'batterie', 'fourche', 'inventaires.taille', 'inventaires.magasins']);

            if ($isSearchMode) {
                 $rawSearch = strtolower(trim($request->search));
                 $words = array_filter(explode(' ', $rawSearch));
                 foreach ($words as $word) {
                    $term = '%' . $word . '%';
                    $query->where(function($group) use ($term, $word) {
                        $group->whereHas('modele', function($q) use ($term, $word) {
                            $q->whereRaw('LOWER(nom_modele) LIKE ?', [$term])
                            ->orWhereRaw('levenshtein(LOWER(nom_modele)::text, ?::text) <= 2', [$word]);
                        })
                        ->orWhereHas('parent', function($q) use ($term, $word) {
                            $q->whereRaw('LOWER(nom_article) LIKE ?', [$term])
                            ->orWhereRaw('levenshtein(LOWER(nom_article)::text, ?::text) <= 2', [$word]);
                        });
                    });
                }
                $titrePage = "RÉSULTATS : " . strtoupper($request->search);
            } else {
                $query->whereHas('modele', fn($q) => $q->where('type_velo', $dbType));
                
                if ($model_id) {
                    $query->where('id_modele', $model_id);
                    $titrePage = "Modèle : " . (Modele::find($model_id)->nom_modele ?? '');
                } elseif ($sub_id) {
                    $query->whereHas('modele', fn($q) => $q->where('id_categorie', $sub_id));
                    $titrePage = CategorieVelo::find($sub_id)->nom_categorie ?? '';
                } elseif ($cat_id) {
                    $currCat = CategorieVelo::with('enfants')->find($cat_id);
                    if ($currCat) {
                        $ids = $currCat->enfants->pluck('id_categorie'); $ids->push($currCat->id_categorie);
                        $query->whereHas('modele', fn($q) => $q->whereIn('id_categorie', $ids));
                        $titrePage = $currCat->nom_categorie;
                    }
                }
            }
            
            // Filtres utilisateur
            if ($request->filled('couleurs')) $query->whereIn('id_couleur', $request->couleurs);
            if ($request->filled('materiaux')) $query->whereHas('modele', fn($q) => $q->whereIn('materiau_cadre', $request->materiaux));
            if ($request->filled('millesimes')) $query->whereHas('modele', fn($q) => $q->whereIn('millesime_modele', $request->millesimes));
            if ($request->filled('fourches')) $query->whereIn('id_fourche', $request->fourches);
            if ($request->filled('batteries')) $query->whereIn('id_batterie', $request->batteries);
        }

        // -------------------------------------------------------
        // 2. OPTIMISATION DES COMPTEURS (Stocks)
        // -------------------------------------------------------
        $hasSize = $request->filled('tailles');
        
        if ($hasSize) {
             $query->whereHas('inventaires.taille', fn($t) => $t->whereIn('taille', $request->tailles));
        }
        if ($request->filled('dispo_ligne')) {
             $query->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0));
        }
        if ($request->filled('dispo_magasin')) {
             $query->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0));
        }
        if ($request->filled('prix_min')) $query->where('prix', '>=', $request->prix_min);
        if ($request->filled('prix_max')) $query->where('prix', '<=', $request->prix_max);

        // Tri
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_desc': $query->orderBy('prix', 'desc'); break;
                case 'name_asc': $query->orderBy('nom_article', 'asc'); break;
                default: $query->orderBy('prix', 'asc'); break;
            }
        } else {
            $query->orderBy('prix', 'asc');
        }

        $countOnline = 0; 
        $countStore = 0;
        if(!$isSearchMode) {
             $countOnline = (clone $query)->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0))->count();
             $countStore = (clone $query)->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0))->count();
        }

        // -------------------------------------------------------
        // 3. RECUPERATION DES FILTRES (AVEC CACHE CORRIGÉ)
        // -------------------------------------------------------
        $filters = Cache::remember($cacheKey, 3600, function () use ($isAccessoire, $type, $isSearchMode, $sub_id, $cat_id, $model_id) {
            
            $data = [];
            
            if ($isAccessoire) {
                $data['materiaux'] = Accessoire::select('materiau')->distinct()->whereNotNull('materiau')->orderBy('materiau')->pluck('materiau');
                // Logique originale remise ici
                $data['tailles'] = Taille::whereHas('ArticleInventaire.articles.accessoire')
                    ->distinct()
                    ->orderBy('id_taille')
                    ->get();

                $data['hierarchyItems'] = collect();
                $data['hierarchyTitle'] = "CATÉGORIES";
                $data['hierarchyLevel'] = 'root';
                
            } else {
                $dbType = ($type === 'Electrique') ? 'electrique' : 'musculaire';
                
                // --- CORRECTION ICI : On réutilise tes requêtes d'origine pour ne choper que ce qui existe ---
                
            // 1. Couleurs (Filtrées pour n'afficher que celles qui ont des vélos correspondants)
            $data['couleurs'] = Couleur::whereHas('varianteVelos.modele', function($q) use ($isSearchMode, $dbType) {
                if (!$isSearchMode) {
                    $q->where('type_velo', $dbType);
                }
            })
            ->orderBy('nom_couleur')
            ->get();
                
                // 2. Fourches (Filtré par type de vélo)
                $data['fourches'] = Fourche::whereHas('varianteVelos.modele', fn($q) => !$isSearchMode ? $q->where('type_velo', $dbType) : $q)
                    ->orderBy('nom_fourche')
                    ->get();
                
                // 3. Batteries
                if ($type === 'Electrique' || $isSearchMode) {
                    $data['batteries'] = Batterie::orderBy('capacite_batterie', 'asc')->get();
                } else {
                    $data['batteries'] = collect();
                }
                
                // 4. Matériaux et Millésimes (Filtrés par type)
                $filterQuery = Modele::query();
                if (!$isSearchMode) $filterQuery->where('type_velo', $dbType);
                
                $data['materiaux'] = (clone $filterQuery)->select('materiau_cadre')->distinct()->whereNotNull('materiau_cadre')->pluck('materiau_cadre');
                $data['millesimes'] = (clone $filterQuery)->select('millesime_modele')->distinct()->orderBy('millesime_modele', 'desc')->pluck('millesime_modele');
                
                // 5. Tailles (LA CORRECTION PRINCIPALE EST LÀ)
                // On utilise ta logique "distinct" et "whereHas" pour éviter les doublons et les tailles inutiles
                $data['tailles'] = Taille::whereHas('ArticleInventaire.articles.varianteVelo.modele', function($q) use ($isSearchMode, $dbType) {
                        if (!$isSearchMode) $q->where('type_velo', $dbType);
                    })
                    ->distinct() // <--- Ce distinct est vital pour éviter "S, M, S, M"
                    ->orderBy('id_taille')
                    ->get();

                // Hiérarchie (Identique à avant)
                if ($model_id) {
                     $data['hierarchyTitle'] = "AUTRES MODÈLES"; $data['hierarchyLevel'] = 'model';
                     $data['hierarchyItems'] = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
                } elseif ($sub_id) {
                     $data['hierarchyTitle'] = "MODÈLES"; $data['hierarchyLevel'] = 'model';
                     $data['hierarchyItems'] = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
                } elseif ($cat_id) {
                     $currCat = CategorieVelo::with('enfants')->find($cat_id);
                     if($currCat) {
                        $data['hierarchyTitle'] = "SOUS-CATÉGORIES"; $data['hierarchyLevel'] = 'sub';
                        $data['hierarchyItems'] = $currCat->enfants->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
                     } else { $data['hierarchyItems'] = collect(); $data['hierarchyTitle']=""; $data['hierarchyLevel']=""; }
                } else {
                     $data['hierarchyTitle'] = "CATÉGORIES"; $data['hierarchyLevel'] = 'root';
                     $data['hierarchyItems'] = CategorieVelo::whereNull('cat_id_categorie')->whereHas('enfants.modeles', fn($q) => $q->where('type_velo', $dbType))->orderBy('nom_categorie')->get()->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
                }
            }
            return $data;
        });

        $availableMateriaux = $filters['materiaux'] ?? collect();
        $availableTailles = $filters['tailles'] ?? collect();
        $availableCouleurs = $filters['couleurs'] ?? collect();
        $availableFourches = $filters['fourches'] ?? collect();
        $availableBatteries = $filters['batteries'] ?? collect();
        $availableMillesimes = $filters['millesimes'] ?? collect();
        $hierarchyItems = $filters['hierarchyItems'] ?? collect();
        $hierarchyTitle = $filters['hierarchyTitle'] ?? "";
        $hierarchyLevel = $filters['hierarchyLevel'] ?? "";

        // Prix max (En cache aussi pour être cohérent)
        $maxPrice = Cache::remember('max_price_' . ($isAccessoire ? 'acc' : 'velo'), 3600, function () use ($isAccessoire) {
            return $isAccessoire ? Accessoire::max('prix') : VarianteVelo::max('prix');
        });

        $articles = $query->paginate(15)->withQueryString();

        return view('listArticle', compact(
            'articles', 'type', 'titrePage', 'maxPrice', 'isAccessoire',
            'availableCouleurs', 'availableMateriaux', 'availableMillesimes',
            'availableFourches', 'availableTailles', 'availableBatteries',
            'hierarchyTitle', 'hierarchyItems', 'hierarchyLevel',
            'cat_id', 'sub_id', 'model_id',
            'countOnline', 'countStore'
        ));
    }
}