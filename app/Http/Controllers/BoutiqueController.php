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
use Illuminate\Support\Facades\DB;
 
class BoutiqueController extends Controller
{
    public function index(Request $request, $type, $cat_id = null, $sub_id = null, $model_id = null)
    {
        $isAccessoire = ($type === 'Accessoires');
        $isSearchMode = $request->filled('search');
        $titrePage = $isAccessoire ? "TOUS LES ACCESSOIRES" : "TOUS LES VÉLOS " . strtoupper($type) . "S";

        // Initialisation des variables de filtres
        $availableMateriaux = collect();
        $availableMillesimes = collect();
        $availableFourches = collect();
        $availableTailles = collect();
        $availableBatteries = collect();
        $availableCouleurs = collect();
 
        $hierarchyTitle = "CATÉGORIES";
        $hierarchyItems = collect();
        $hierarchyLevel = 'root';
       
        $countOnline = 0;
        $countStore = 0;
        $articles = null; // Variable générique remplaçant $velos
        // =================================================================
        // BRANCHE 1 : ACCESSOIRES (Table 'accessoire')
        // =================================================================
        if ($isAccessoire) {
            // On charge 'inventaires.taille' pour le filtre taille et 'categorie'
            $query = Accessoire::query()->with(['parent', 'categorie', 'parent.photos', 'inventaires.taille']);

            // --- 1. RECHERCHE ---
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
                $titrePage = "RÉSULTATS ACCESSOIRES : " . strtoupper($request->search);
            } 
            // --- 2. NAVIGATION ---
            else {
                // ... (Code de navigation existant : model_id, sub_id, cat_id) ...
                // Je ne répète pas tout le bloc de navigation ici pour faire court, 
                // gardez votre logique existante pour $hierarchyItems, etc.
                if ($model_id) { 
                    $query->where('id_categorie_accessoire', $model_id);
                    $titrePage = (CategorieAccessoire::find($model_id)->nom_categorie_accessoire ?? '');
                    // ... logique existante ...
                } elseif ($sub_id) {
                    // ... logique existante ...
                    $currCat = CategorieAccessoire::with('enfants')->find($sub_id);
                    if ($currCat) {
                        $ids = $currCat->enfants->pluck('id_categorie_accessoire');
                        $ids->push($currCat->id_categorie_accessoire);
                        $query->whereIn('id_categorie_accessoire', $ids);
                        $titrePage = $currCat->nom_categorie_accessoire;
                    }
                    // ... suite logique existante ...
                } elseif ($cat_id) {
                    // ... logique existante ...
                    $currCat = CategorieAccessoire::with('enfants')->find($cat_id);
                    if ($currCat) {
                        $ids = $currCat->enfants->pluck('id_categorie_accessoire');
                        $ids->push($currCat->id_categorie_accessoire);
                        $query->whereIn('id_categorie_accessoire', $ids);
                        $titrePage = $currCat->nom_categorie_accessoire;
                    }
                    // ... suite logique existante ...
                } else {
                    // ... logique existante ...
                }
            }

            // --- 3. FILTRES SPÉCIFIQUES ACCESSOIRES ---

            // NOUVEAU : Filtre Matériaux
            if ($request->filled('materiaux')) {
                $query->whereIn('materiau', $request->materiaux);
            }

            // Filtre Taille (existant)
            if ($request->filled('tailles')) {
                $query->whereHas('inventaires.taille', function($q) use ($request) {
                    $q->whereIn('taille', $request->tailles);
                });
            }

            // Filtres Dispo (existant - correction précédente)
            if ($request->filled('dispo_ligne')) {
                $query->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0));
            }
            if ($request->filled('dispo_magasin')) {
                $query->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0));
            }

            // Compteurs (existant)
            $countOnline = (clone $query)->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0))->count();
            $countStore = (clone $query)->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0))->count();

            // Prix & Tri (existant)
            if ($request->filled('prix_min')) $query->where('prix', '>=', $request->prix_min);
            if ($request->filled('prix_max')) $query->where('prix', '<=', $request->prix_max);
            // ... (Logique de tri existante) ...
            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'price_asc': $query->orderBy('prix', 'asc'); break;
                    case 'price_desc': $query->orderBy('prix', 'desc'); break;
                    case 'name_asc': $query->orderBy('nom_article', 'asc'); break;
                    case 'name_desc': $query->orderBy('nom_article', 'desc'); break;
                    default: $query->orderBy('prix', 'asc'); break;
                }
            } else {
                $query->orderBy('prix', 'asc');
            }

            // --- SIDEBAR DATA (Données pour les filtres) ---
            
            // NOUVEAU : Récupération des matériaux pour Accessoires
            // On réutilise la variable $availableMateriaux pour la vue
            $availableMateriaux = Accessoire::select('materiau')
                ->distinct()
                ->whereNotNull('materiau')
                ->where('materiau', '!=', '')
                ->orderBy('materiau')
                ->pluck('materiau'); // Renvoie une Collection de strings

            // Récupération des tailles (existant)
            $availableTailles = Taille::whereHas('ArticleInventaire.articles.accessoire')
                ->distinct()
                ->orderBy('id_taille')
                ->get();

            $maxPrice = Accessoire::max('prix');
            $articles = $query->paginate(15)->withQueryString();
        }
 
        // =================================================================
        // BRANCHE 2 : VÉLOS (Table 'variante_velo')
        // =================================================================
        else {
            $dbType = ($type === 'Electrique') ? 'electrique' : 'musculaire';
            
            // Note: VarianteVelo hérite de Article, donc il hérite de la relation 'inventaires'
            $query = VarianteVelo::query()
                ->with(['parent', 'modele', 'couleur', 'parent.photos', 'batterie', 'fourche', 'inventaires.taille', 'inventaires.magasins']);
            

            // --- 1. RECHERCHE FLOU VÉLOS ---
            if ($isSearchMode) {
                $search = strtolower($request->search);
                $term = '%' . $search . '%';
 
                $query->where(function($group) use ($term, $search) {
                    $group->whereHas('modele', function($q) use ($term, $search) {
                        $q->whereRaw('LOWER(nom_modele) LIKE ?', [$term])
                          ->orWhereRaw('levenshtein(LOWER(nom_modele)::text, ?::text) <= 1', [$search]);
                    })
                    ->orWhereHas('parent', function($q) use ($term, $search) {
                        $q->whereRaw('LOWER(nom_article) LIKE ?', [$term])
                          ->orWhereRaw('levenshtein(LOWER(nom_article)::text, ?::text) <= 1', [$search]);
                    })
                    ->orWhereHas('modele.categorie', function($q) use ($term, $search) {
                        $q->where(function($subQ) use ($term, $search) {
                            $subQ->whereRaw('LOWER(nom_categorie) LIKE ?', [$term])
                                 ->orWhereRaw('levenshtein(LOWER(nom_categorie)::text, ?::text) <= 1', [$search]);
                        })
                        ->orWhereHas('parent', function($pQ) use ($term, $search) {
                            $pQ->whereRaw('LOWER(nom_categorie) LIKE ?', [$term])
                               ->orWhereRaw('levenshtein(LOWER(nom_categorie)::text, ?::text) <= 1', [$search]);
                        });
                    });
                });
                $titrePage = "RÉSULTATS VÉLOS : " . strtoupper($request->search);

            }

            // --- 2. NAVIGATION ---
            else {
                $query->whereHas('modele', fn($q) => $q->where('type_velo', $dbType));
               
                if ($model_id) {
                    $query->where('id_modele', $model_id);
                    $titrePage = "Modèle : " . (Modele::find($model_id)->nom_modele ?? '');
                    $hierarchyTitle = "AUTRES MODÈLES"; $hierarchyLevel = 'model';
                    $hierarchyItems = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
                } elseif ($sub_id) {
                    $query->whereHas('modele', fn($q) => $q->where('id_categorie', $sub_id));
                    $titrePage = CategorieVelo::find($sub_id)->nom_categorie ?? '';
                    $hierarchyTitle = "MODÈLES"; $hierarchyLevel = 'model';
                    $hierarchyItems = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
                } elseif ($cat_id) {
                    $currCat = CategorieVelo::with('enfants')->find($cat_id);
                    if ($currCat) {
                        $ids = $currCat->enfants->pluck('id_categorie'); $ids->push($currCat->id_categorie);
                        $query->whereHas('modele', fn($q) => $q->whereIn('id_categorie', $ids));
                        $titrePage = $currCat->nom_categorie;
                        $hierarchyTitle = "SOUS-CATÉGORIES"; $hierarchyLevel = 'sub';
                        $hierarchyItems = $currCat->enfants->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
                    }
                } else {
                    $hierarchyTitle = "CATÉGORIES"; $hierarchyLevel = 'root';
                    $hierarchyItems = CategorieVelo::whereNull('cat_id_categorie')->whereHas('enfants.modeles', fn($q) => $q->where('type_velo', $dbType))->orderBy('nom_categorie')->get()->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
                }
            }

            // --- FILTRES SIMPLES ---
            if ($request->filled('couleurs')) $query->whereIn('id_couleur', $request->couleurs);
            if ($request->filled('materiaux')) $query->whereHas('modele', fn($q) => $q->whereIn('materiau_cadre', $request->materiaux));
            if ($request->filled('millesimes')) $query->whereHas('modele', fn($q) => $q->whereIn('millesime_modele', $request->millesimes));
            if ($request->filled('fourches')) $query->whereIn('id_fourche', $request->fourches);
            if ($request->filled('batteries')) $query->whereIn('id_batterie', $request->batteries);

            // --- 3. FILTRES STOCKS COMPLEXES (Via Inventaire) ---
            $hasSize = $request->filled('tailles');
            $hasOnline = $request->filled('dispo_ligne');
            $hasStore = $request->filled('dispo_magasin');

            // --- A. CALCUL DES COMPTEURS (AVANT d'appliquer les filtres de dispo) ---
            // On clone la requête de base (qui a déjà les filtres de catégorie, prix, etc. mais PAS encore la dispo)
            $queryForCounts = clone $query;

            // Calcul du nombre de vélos dispos EN LIGNE (en tenant compte de la taille si sélectionnée)
            $countOnline = (clone $queryForCounts)->whereHas('inventaires', function($q) use ($request, $hasSize) {
                // Si une taille est choisie, on ne compte que si CETTE taille est dispo
                if ($hasSize) {
                    $q->whereHas('taille', fn($t) => $t->whereIn('taille', $request->tailles));
                }
                $q->where('quantite_stock_en_ligne', '>', 0);
            })->count();

            // Calcul du nombre de vélos dispos EN MAGASIN (en tenant compte de la taille si sélectionnée)
            $countStore = (clone $queryForCounts)->whereHas('inventaires', function($q) use ($request, $hasSize) {
                if ($hasSize) {
                    $q->whereHas('taille', fn($t) => $t->whereIn('taille', $request->tailles));
                }
                $q->whereHas('magasins', fn($m) => $m->where('quantite_stock_magasin', '>', 0));
            })->count();


            // --- B. APPLICATION DES FILTRES SUR LA LISTE DES RÉSULTATS ---
            if ($hasSize) {
                // Cas avec Taille : On filtre les vélos qui ont la taille X ET le stock demandé pour cette taille
                $query->whereHas('inventaires', function($q) use ($request, $hasOnline, $hasStore) {
                    $q->whereHas('taille', fn($t) => $t->whereIn('taille', $request->tailles));
                    
                    if ($hasOnline) $q->where('quantite_stock_en_ligne', '>', 0);
                    if ($hasStore) $q->whereHas('magasins', fn($m) => $m->where('quantite_stock_magasin', '>', 0));
                });
            } else {
                // Cas sans Taille : On regarde globalement si le vélo est dispo
                if ($hasOnline) $query->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0));
                if ($hasStore) $query->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0));
            }

            // Prix & Tri
            if ($request->filled('prix_min')) $query->where('prix', '>=', $request->prix_min);
            if ($request->filled('prix_max')) $query->where('prix', '<=', $request->prix_max);
 
            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'price_asc': $query->orderBy('prix', 'asc'); break;
                    case 'price_desc': $query->orderBy('prix', 'desc'); break;
                    case 'name_asc': $query->orderBy('nom_article', 'asc'); break;
                    case 'name_desc': $query->orderBy('nom_article', 'desc'); break;
                    default: $query->orderBy('prix', 'asc'); break;
                }
            } else {
                $query->orderBy('prix', 'asc');
            }

            // --- SIDEBAR DATA (CORRECTION DU BUG Article::modele()) ---
            $filterQuery = Modele::query();
            if (!$isSearchMode) $filterQuery->where('type_velo', $dbType);
            
            $availableCouleurs = Couleur::orderBy('nom_couleur')->get();
            $availableMateriaux = (clone $filterQuery)->select('materiau_cadre')->distinct()->whereNotNull('materiau_cadre')->pluck('materiau_cadre');
            $availableMillesimes = (clone $filterQuery)->select('millesime_modele')->distinct()->orderBy('millesime_modele', 'desc')->pluck('millesime_modele');
            $availableFourches = Fourche::whereHas('varianteVelos.modele', fn($q) => !$isSearchMode ? $q->where('type_velo', $dbType) : $q)->orderBy('nom_fourche')->get();

            // ICI: On passe de ArticleInventaire -> Articles -> VarianteVelo -> Modele
            $availableTailles = Taille::whereHas('ArticleInventaire.articles.varianteVelo.modele', function($q) use ($isSearchMode, $dbType) {
                if (!$isSearchMode) $q->where('type_velo', $dbType);
            })->orderBy('id_taille')->distinct()->get();
            
            if ($type === 'Electrique' || $isSearchMode) $availableBatteries = Batterie::orderBy('capacite_batterie', 'asc')->get();
            $maxPrice = VarianteVelo::max('prix');
            

            $articles = $query->paginate(15)->withQueryString();
        }
 
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