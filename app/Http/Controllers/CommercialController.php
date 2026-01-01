<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use App\Models\Article;
use App\Models\CategorieVelo;
use App\Models\CategorieAccessoire;
use App\Models\Modele;
use App\Models\Description;

class CommercialController extends Controller
{
    public function dashboard()
    {
        return view('commercial.dashboard');
    }

    public function articleList()
    {
        // 1. Récupérer tous les vélos avec les relations nécessaires pour déterminer le type
        $tousLesVelos = VarianteVelo::with(['photos', 'modele'])->get();

        // 2. Filtrer selon le 'type_velo' défini dans la catégorie
        // On utilise filter() sur la collection pour trier en PHP
        $velosMusculaires = $tousLesVelos->filter(function ($velo) {
            // Sécurité : on vérifie que le modèle et la catégorie existent
            if ($velo->modele) {
                // On compare en minuscule pour être sûr
                return strtolower($velo->modele->type_velo) === 'musculaire';
            }
            return false;
        });

        $velosElectriques = $tousLesVelos->filter(function ($velo) {
            if ($velo->modele) {
                return strtolower($velo->modele->type_velo) === 'electrique';
            }
            return false;
        });

        // 3. Récupérer les accessoires
        $accessoires = Accessoire::with(['photos'])->get();

        return view('commercial.modifierArticle', compact('velosMusculaires', 'velosElectriques', 'accessoires'));
    }

    public function destroy($reference)
    {
        try {
            Article::where('reference', $reference)->delete();
            return back()->with('success', 'Article supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cet article (il est peut-être lié à une commande).');
        }
    }

    // 1. Afficher le formulaire
    public function addCategorie()
    {
        // CORRECTION : On ne filtre plus par type_velo car la colonne n'existe pas.
        // On récupère toutes les catégories racines de vélos (celles qui n'ont pas de parent)
        $parentsVelos = CategorieVelo::whereNull('cat_id_categorie')->get();

        // On récupère les racines pour les Accessoires
        $parentsAccessoires = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')->get();

        return view('commercial.addCategorie', compact('parentsVelos', 'parentsAccessoires'));
    }

    // 2. Enregistrer la nouvelle catégorie
    public function storeCategorie(Request $request)
    {
        $request->validate([
            'type_article'  => 'required|in:Musculaire,Electrique,Accessoires',
            'parent_id'     => 'required|integer',
            'nom_categorie' => 'required|string|max:255',
        ], [
            // Messages personnalisés
            'type_article.required'  => 'Veuillez sélectionner un type d\'article (Musculaire, Électrique ou Accessoires).',
            'type_article.in'        => 'Le type d\'article sélectionné est invalide.',
            
            'parent_id.required'     => 'Veuillez sélectionner une catégorie parente.',
            'parent_id.integer'      => 'La catégorie parente sélectionnée est incorrecte.',
            
            'nom_categorie.required' => 'Le nom de la nouvelle catégorie est obligatoire.',
            'nom_categorie.string'   => 'Le nom de la catégorie doit être une chaîne de caractères valide.',
            'nom_categorie.max'      => 'Le nom de la catégorie est trop long (max 255 caractères).',
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                if ($request->type_article === 'Accessoires') {
                    // Insertion Accessoire
                    CategorieAccessoire::create([
                        'nom_categorie_accessoire'    => $request->nom_categorie,
                        'cat_id_categorie_accessoire' => $request->parent_id,
                    ]);
                } else {
                    // Insertion Vélo (Musculaire ou Électrique)
                    // Note : On ne stocke pas le type_velo ici car votre table categorie_velo est neutre
                    CategorieVelo::create([
                        'nom_categorie'    => $request->nom_categorie,
                        'cat_id_categorie' => $request->parent_id,
                    ]);
                }
            });

            // Succès
            return redirect()->route('commercial.dashboard')
                             ->with('success', 'Catégorie "' . $request->nom_categorie . '" ajoutée avec succès !');

        } catch (\Exception $e) {
            // En cas d'erreur technique (ex: base de données inaccessible)
            return back()->withErrors(['error' => 'Erreur lors de la création de la catégorie : ' . $e->getMessage()])
                         ->withInput(); // Garde les champs remplis
        }
    }


    // 1. AFFICHER LE FORMULAIRE
    public function addModele()
    {
        // On récupère toutes les catégories de vélos avec leurs enfants pour le menu dynamique
        $categoriesVelos = CategorieVelo::with('enfants')
                            ->whereNull('cat_id_categorie') // Seulement les racines (VTT, Route...)
                            ->get();

        return view('commercial.addModele', compact('categoriesVelos'));
    }

    // 2. ENREGISTRER LE MODÈLE
    public function storeModele(Request $request)
    {
        // 1. Validation
            $validated = $request->validate([
            'type_velo'      => 'required|in:musculaire,electrique',
            'sub_category_id'=> 'required|integer',
            'nom_modele'     => 'required|string|max:50',
            
            // Validation Millésime : String de 4 chiffres, converti en entier pour vérifier la plage
            'millesime'      => [
                'required',
                'digits:4',          // Force 4 caractères exactement
                'integer',           // Doit être un nombre
                'min:1993',          // Pas avant 1993
                'max:' . date("Y") // Pas après l'année actuelle
            ],

            'materiau'       => 'required|string|max:50',
            
            // Validation Description : Max 5000 caractères (environ 2 pages de texte)
            'description'    => 'required|string|max:5000', 
        ], [
            // --- MESSAGES PERSONNALISÉS ---
            
            // Type de vélo
            'type_velo.required' => 'Le type de vélo est obligatoire.',
            'type_velo.in'       => 'Le type de vélo doit être soit Musculaire, soit Électrique.',

            // Catégorie
            'sub_category_id.required' => 'Veuillez sélectionner une catégorie.',
            'sub_category_id.integer'  => 'La catégorie sélectionnée est invalide.',

            // Nom du modèle
            'nom_modele.required' => 'Le nom du modèle est obligatoire.',
            'nom_modele.max'      => 'Le nom du modèle ne doit pas dépasser 50 caractères.',

            // Millésime
            'millesime.required' => 'L\'année (millésime) est obligatoire.',
            'millesime.digits'   => 'L\'année doit comporter 4 chiffres (ex: 2024).',
            'millesime.integer'  => 'L\'année doit être un nombre valide.',
            'millesime.min'      => 'Nous ne gérons pas les modèles antérieurs à 1993.',
            'millesime.max'      => 'L\'année ne peut pas être supérieure à l\'année en cours (' . date("Y") . ').',
        ]);;

        // 2. Création de la Description (L'ID est généré par la séquence PostgreSQL)
        try {
            DB::transaction(function () use ($request) { // on utilise ça pour tout annuler en cas d'erreur(du coup ça insère pas la description si le modèle échoue)
                
                // ÉTAPE A : Création de la Description
                $desc = Description::create([
                    'texte_description' => $request->description
                ]);

                // ÉTAPE B : Création du Modèle
                // Si cette étape échoue (ex: erreur SQL), la description ci-dessus sera annulée
                Modele::create([
                    'id_categorie'     => $request->sub_category_id,
                    'id_description'   => $desc->id_description,
                    'nom_modele'       => $request->nom_modele,
                    'millesime_modele' => $request->millesime,
                    'materiau_cadre'   => $request->materiau,
                    'type_velo'        => $request->type_velo,
                ]);
            });

            // Si on arrive ici, c'est que la transaction a réussi
            return redirect()->route('commercial.dashboard')
                            ->with('success', 'Le modèle "' . $request->nom_modele . '" a été ajouté avec succès !');

        } catch (\Exception $e) {
            // En cas d'erreur n'importe où dans la transaction :
            // 1. Laravel annule tout automatiquement (Rollback) : la description est supprimée.
            // 2. On redirige l'utilisateur avec le message d'erreur.
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage()])
                        ->withInput(); // Garde les champs remplis pour ne pas tout retaper
        }
    }
}