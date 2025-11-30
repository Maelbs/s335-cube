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

class BoutiqueController extends Controller
{
    public function index(Request $request, $type, $cat_id = null, $sub_id = null, $model_id = null)
    {
        // 1. Traduction Type URL -> BDD
        $dbType = ($type === 'Electrique') ? 'electrique' : 'musculaire';

        // 2. Initialisation
        $query = VarianteVelo::query()
            ->with(['parent', 'modele', 'couleur', 'parent.photos', 'batterie', 'fourche'])
            ->where('dispo_en_ligne', true);

        $isSearchMode = $request->filled('search');
        $currentCategory = null;

        // =========================================================
        // 3. LOGIQUE RECHERCHE vs NAVIGATION
        // =========================================================
        
        if ($isSearchMode) {
            // --- MODE RECHERCHE (GLOBALE) ---
            // On ignore le $dbType pour chercher dans TOUT le catalogue
            
            $query->where(function($group) use ($request) {
                $search = strtolower($request->search);
                $term = '%' . $search . '%';

                $group->whereHas('modele', function($q) use ($term) {
                    $q->whereRaw('LOWER(nom_modele) LIKE ?', [$term]);
                })
                ->orWhereHas('parent', function($q) use ($term) {
                    $q->whereRaw('LOWER(nom_article) LIKE ?', [$term]);
                })
                ->orWhereHas('modele.categorie', function($q) use ($term) {
                    $q->where(function($subQ) use ($term) {
                        $subQ->whereRaw('LOWER(nom_categorie) LIKE ?', [$term])
                            ->orWhereHas('parent', function($parentQ) use ($term) {
                                $parentQ->whereRaw('LOWER(nom_categorie) LIKE ?', [$term]);
                            });
                    });
                });
            });

            $titrePage = "RÉSULTATS POUR : " . strtoupper($request->search);

        } else {
            // --- MODE NAVIGATION (FILTRÉ PAR TYPE) ---
            // On applique le filtre strict (Musculaire OU Electrique)
            $query->whereHas('modele', function ($q) use ($dbType) {
                $q->where('type_velo', $dbType);
            });

            $titrePage = "TOUS LES VÉLOS " . strtoupper($type) . "S";

            // Logique hiérarchique (Catégorie / Modèle) uniquement hors recherche
            if ($model_id) {
                $query->where('id_modele', $model_id);
                $modeleName = Modele::find($model_id)->nom_modele ?? '';
                $titrePage = "Modèle : " . $modeleName;
            } elseif ($sub_id) {
                $query->whereHas('modele', fn($q) => $q->where('id_categorie', $sub_id));
                $currentCategory = CategorieVelo::find($sub_id);
                $titrePage = $currentCategory ? $currentCategory->nom_categorie : '';
            } elseif ($cat_id) {
                $currentCategory = CategorieVelo::with('enfants')->find($cat_id);
                if ($currentCategory) {
                    $ids = $currentCategory->enfants->pluck('id_categorie');
                    $ids->push($currentCategory->id_categorie);
                    $query->whereHas('modele', fn($q) => $q->whereIn('id_categorie', $ids));
                    $titrePage = $currentCategory->nom_categorie;
                }
            }
        }

        // =========================================================
        // 4. AUTRES FILTRES (Sidebar)
        // =========================================================
        if ($request->filled('prix_min')) $query->where('prix', '>=', $request->prix_min);
        if ($request->filled('prix_max')) $query->where('prix', '<=', $request->prix_max);
        if ($request->filled('couleurs')) $query->whereIn('id_couleur', $request->couleurs);
        if ($request->filled('materiaux')) {
            $query->whereHas('modele', fn($q) => $q->whereIn('materiau_cadre', $request->materiaux));
        }
        if ($request->filled('millesimes')) {
            $query->whereHas('modele', fn($q) => $q->whereIn('millesime_modele', $request->millesimes));
        }
        if ($request->filled('fourches')) $query->whereIn('id_fourche', $request->fourches);
        if ($request->filled('batteries')) $query->whereIn('id_batterie', $request->batteries);
        if ($request->filled('tailles')) {
            $query->whereHas('modele.tailles', fn($q) => $q->whereIn('taille', $request->tailles));
        }

        // Tri
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

        // =========================================================
        // 5. CHARGEMENT INTELLIGENT DES DONNÉES FILTRES
        // =========================================================
        
        // Si on cherche, on veut TOUTES les options (ex: Batteries même si URL dit Musculaire)
        // Si on navigue, on ne veut que les options du TYPE en cours.
        
        $filterQuery = Modele::query();
        if (!$isSearchMode) {
            $filterQuery->where('type_velo', $dbType);
        }

        // On clone la requête de base pour chaque filtre pour éviter les conflits
        $availableMateriaux = (clone $filterQuery)->select('materiau_cadre')->distinct()->whereNotNull('materiau_cadre')->pluck('materiau_cadre');
        $availableMillesimes = (clone $filterQuery)->select('millesime_modele')->distinct()->orderBy('millesime_modele', 'desc')->pluck('millesime_modele');
        
        // Pour les relations, on fait pareil
        $availableFourches = Fourche::whereHas('varianteVelos.modele', function($q) use ($isSearchMode, $dbType) {
            if (!$isSearchMode) $q->where('type_velo', $dbType);
        })->orderBy('nom_fourche')->get();

        $availableTailles = Taille::whereHas('aGeometries.modele', function($q) use ($isSearchMode, $dbType) {
            if (!$isSearchMode) $q->where('type_velo', $dbType);
        })->orderBy('id_taille')->get();

        $availableBatteries = collect();
        // On affiche les batteries si c'est la section Elec OU si on est en recherche (au cas où on trouve des élec)
        if ($type === 'Electrique' || $isSearchMode) {
            $availableBatteries = Batterie::orderBy('capacite_batterie', 'asc')->get();
        }

        $availableCouleurs = Couleur::orderBy('nom_couleur')->get();
        $maxPrice = VarianteVelo::max('prix');

        // Hiérarchie Sidebar (Cachée en mode recherche pour ne pas embrouiller)
        $hierarchyTitle = "CATÉGORIES";
        $hierarchyItems = collect();
        $hierarchyLevel = 'root';

        if (!$isSearchMode) {
            // ... (Votre logique hiérarchique existante, inchangée) ...
            // Je la remets ici pour que le code soit complet si vous copiez-collez
            if ($model_id) {
                $hierarchyTitle = "AUTRES MODÈLES";
                $hierarchyLevel = 'model';
                $hierarchyItems = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()
                    ->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
            } elseif ($sub_id) {
                $hierarchyTitle = "MODÈLES";
                $hierarchyLevel = 'model';
                $hierarchyItems = Modele::where('id_categorie', $sub_id)->where('type_velo', $dbType)->orderBy('nom_modele')->get()
                    ->map(fn($item) => (object)['name' => $item->nom_modele, 'id' => $item->id_modele]);
            } elseif ($cat_id) {
                $hierarchyTitle = "SOUS-CATÉGORIES";
                $hierarchyLevel = 'sub';
                $hierarchyItems = CategorieVelo::where('cat_id_categorie', $cat_id)->orderBy('nom_categorie')->get()
                    ->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
            } else {
                $hierarchyTitle = "CATÉGORIES";
                $hierarchyLevel = 'root';
                $hierarchyItems = CategorieVelo::whereNull('cat_id_categorie')
                    ->whereHas('enfants.modeles', fn($q) => $q->where('type_velo', $dbType))->orderBy('nom_categorie')->get()
                    ->map(fn($item) => (object)['name' => $item->nom_categorie, 'id' => $item->id_categorie]);
            }
        }

        return view('listArticle', compact(
            'velos', 'type', 'titrePage', 'maxPrice',
            'availableCouleurs', 'availableMateriaux', 'availableMillesimes',
            'availableFourches', 'availableTailles', 'availableBatteries',
            'hierarchyTitle', 'hierarchyItems', 'hierarchyLevel',
            'cat_id', 'sub_id', 'model_id'
        ));
    }
}