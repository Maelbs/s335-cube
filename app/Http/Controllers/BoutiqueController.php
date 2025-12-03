<?php

namespace App\Http\Controllers;

use App\Models\CategorieVelo;
use App\Models\VarianteVelo;
use App\Models\Couleur;
use App\Models\Modele;
use App\Models\Fourche;
use App\Models\Batterie;
use App\Models\Taille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // N'oubliez pas d'importer DB

class BoutiqueController extends Controller
{
    public function index(Request $request, $type, $cat_id = null, $sub_id = null, $model_id = null)
    {
        $dbType = ($type === 'Electrique') ? 'electrique' : 'musculaire';

        // 1. INITIALISATION
        $query = VarianteVelo::query()
            ->with([
                'parent', 
                'modele', 
                'couleur', 
                'parent.photos', 
                'batterie', 
                'fourche', 
                'inventaires.taille', 
                'inventaires.magasins'
            ]);

        $isSearchMode = $request->filled('search');
        $titrePage = "TOUS LES VÉLOS " . strtoupper($type) . "S";

        // --- 2. RECHERCHE vs NAVIGATION (CORRIGÉ POUR TOLÉRANCE 1 CARACTÈRE) ---
        if ($isSearchMode) {
            $query->where(function($group) use ($request) {
                // On nettoie la recherche (minuscule)
                $search = strtolower($request->search);
                $term = '%' . $search . '%'; // Pour le LIKE classique
                
                // --- Logique Modèles ---
                $group->whereHas('modele', function($q) use ($term, $search) {
                    $q->where(function($sub) use ($term, $search) {
                        // 1. Recherche classique (bout de texte)
                        $sub->whereRaw('LOWER(nom_modele) LIKE ?', [$term])
                        // 2. OU Recherche floue (Levenshtein <= 1)
                        // Cela trouve "Sterio" pour "Stereo" ou "Gravle" pour "Gravel"
                           ->orWhereRaw('levenshtein(LOWER(nom_modele), ?) <= 1', [$search]);
                    });
                })
                
                // --- Logique Articles (Parents) ---
                ->orWhereHas('parent', function($q) use ($term, $search) {
                    $q->where(function($sub) use ($term, $search) {
                        $sub->whereRaw('LOWER(nom_article) LIKE ?', [$term])
                            ->orWhereRaw('levenshtein(LOWER(nom_article), ?) <= 1', [$search]);
                    });
                })
                
                // --- Logique Catégories ---
                ->orWhereHas('modele.categorie', function($q) use ($term, $search) {
                    $q->where(function($subQ) use ($term, $search) {
                        // Catégorie parent
                        $subQ->where(function($catQ) use ($term, $search) {
                             $catQ->whereRaw('LOWER(nom_categorie) LIKE ?', [$term])
                                  ->orWhereRaw('levenshtein(LOWER(nom_categorie), ?) <= 1', [$search]);
                        })
                        // Ou catégorie enfant (via parent relation)
                        ->orWhereHas('parent', function($pQ) use ($term, $search) {
                             $pQ->whereRaw('LOWER(nom_categorie) LIKE ?', [$term])
                                ->orWhereRaw('levenshtein(LOWER(nom_categorie), ?) <= 1', [$search]);
                        });
                    });
                });
            });
            $titrePage = "RÉSULTATS POUR : " . strtoupper($request->search);
        } else {
            // Navigation standard (inchangée)
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
                    $ids = $currCat->enfants->pluck('id_categorie');
                    $ids->push($currCat->id_categorie);
                    $query->whereHas('modele', fn($q) => $q->whereIn('id_categorie', $ids));
                    $titrePage = $currCat->nom_categorie;
                }
            }
        }

        // --- 3. FILTRES SIMPLES (Inchangé) ---
        if ($request->filled('prix_min')) $query->where('prix', '>=', $request->prix_min);
        if ($request->filled('prix_max')) $query->where('prix', '<=', $request->prix_max);
        if ($request->filled('couleurs')) $query->whereIn('id_couleur', $request->couleurs);
        if ($request->filled('materiaux')) $query->whereHas('modele', fn($q) => $q->whereIn('materiau_cadre', $request->materiaux));
        if ($request->filled('millesimes')) $query->whereHas('modele', fn($q) => $q->whereIn('millesime_modele', $request->millesimes));
        if ($request->filled('fourches')) $query->whereIn('id_fourche', $request->fourches);
        if ($request->filled('batteries')) $query->whereIn('id_batterie', $request->batteries);

        // --- 4. FILTRES COMPLEXES (Inchangé) ---
        $hasSize = $request->filled('tailles');
        $hasOnline = $request->filled('dispo_ligne');
        $hasStore = $request->filled('dispo_magasin');

        if ($hasSize) {
            $query->whereHas('inventaires', function($q) use ($request, $hasOnline, $hasStore) {
                $q->whereHas('taille', fn($t) => $t->whereIn('taille', $request->tailles));
                if ($hasOnline) $q->where('quantite_stock_en_ligne', '>', 0);
                if ($hasStore) $q->whereHas('magasins', fn($m) => $m->where('quantite_stock_magasin', '>', 0));
            });
        } else {
            if ($hasOnline) $query->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0));
            if ($hasStore) $query->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0));
        }

        // --- 5. COMPTEURS (Inchangé) ---
        $countOnline = (clone $query)->whereHas('inventaires', fn($q) => $q->where('quantite_stock_en_ligne', '>', 0))->count();
        $countStore = (clone $query)->whereHas('inventaires.magasins', fn($q) => $q->where('quantite_stock_magasin', '>', 0))->count();

        // --- 6. TRI (Inchangé) ---
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

        $velos = $query->paginate(15)->withQueryString();

        // --- 7. DATA SIDEBAR (Inchangé) ---
        $filterQuery = Modele::query();
        if (!$isSearchMode) $filterQuery->where('type_velo', $dbType);

        $availableMateriaux = (clone $filterQuery)->select('materiau_cadre')->distinct()->whereNotNull('materiau_cadre')->pluck('materiau_cadre');
        $availableMillesimes = (clone $filterQuery)->select('millesime_modele')->distinct()->orderBy('millesime_modele', 'desc')->pluck('millesime_modele');
        $availableFourches = Fourche::whereHas('varianteVelos.modele', fn($q) => !$isSearchMode ? $q->where('type_velo', $dbType) : $q)->orderBy('nom_fourche')->get();
        
        $availableTailles = Taille::whereHas('varianteVeloInventaire.varianteVelo.modele', function($q) use ($isSearchMode, $dbType) {
            if (!$isSearchMode) $q->where('type_velo', $dbType);
        })->orderBy('id_taille')->distinct()->get();

        $availableBatteries = collect();
        if ($type === 'Electrique' || $isSearchMode) {
            $availableBatteries = Batterie::orderBy('capacite_batterie', 'asc')->get();
        }
        $availableCouleurs = Couleur::orderBy('nom_couleur')->get();
        $maxPrice = VarianteVelo::max('prix');

        // Hiérarchie
        $hierarchyTitle = "CATÉGORIES"; $hierarchyItems = collect(); $hierarchyLevel = 'root';
        if (!$isSearchMode) {
             if ($model_id) {
                $hierarchyTitle = "AUTRES MODÈLES"; $hierarchyLevel = 'model';
                $hierarchyItems = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
            } elseif ($sub_id) {
                $hierarchyTitle = "MODÈLES"; $hierarchyLevel = 'model';
                $hierarchyItems = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
            } elseif ($cat_id) {
                $hierarchyTitle = "SOUS-CATÉGORIES"; $hierarchyLevel = 'sub';
                $hierarchyItems = CategorieVelo::where('cat_id_categorie', $cat_id)->orderBy('nom_categorie')->get()->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
            } else {
                $hierarchyTitle = "CATÉGORIES"; $hierarchyLevel = 'root';
                $hierarchyItems = CategorieVelo::whereNull('cat_id_categorie')->whereHas('enfants.modeles', fn($q) => $q->where('type_velo', $dbType))->orderBy('nom_categorie')->get()->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
            }
        }

        return view('listArticle', compact(
            'velos', 'type', 'titrePage', 'maxPrice',
            'availableCouleurs', 'availableMateriaux', 'availableMillesimes',
            'availableFourches', 'availableTailles', 'availableBatteries',
            'hierarchyTitle', 'hierarchyItems', 'hierarchyLevel',
            'cat_id', 'sub_id', 'model_id',
            'countOnline', 'countStore'
        ));
    }
}