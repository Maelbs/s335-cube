<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

use App\Models\Resume;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use App\Models\Article;
use App\Models\CategorieVelo;
use App\Models\CategorieAccessoire;
use App\Models\Modele;
use App\Models\Description;
use App\Models\PhotoArticle;
use App\Models\TypeCaracteristique;
use App\Models\Caracteristique;
use App\Models\ACaracteristique; 
use App\Models\ArticleInventaire; 

class CommercialController extends Controller
{
    public function dashboard()
    {
        return view('commercial.dashboard');
    }

    public function articleList()
    {
     
        $tousLesVelos = VarianteVelo::with(['photos', 'modele'])->get();

       
        // On utilise filter() sur la collection pour trier en PHP
        $velosMusculaires = $tousLesVelos->filter(function ($velo) {
       
            if ($velo->modele) {
                
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

    
        $accessoires = Accessoire::with(['photos'])->get();

        return view('commercial.modifierArticle', compact('velosMusculaires', 'velosElectriques', 'accessoires'));
    }

    public function editArticle($reference)
    {
       
        $isVelo = VarianteVelo::where('reference', $reference)->exists();
        if ($isVelo) {
            return back()->with('error', 'La modification des Vélos/VAE n\'est pas encore disponible. Uniquement les accessoires.');
        }

 
        $accessoire = Accessoire::with('parent.resume')
                        ->where('reference', $reference)
                        ->firstOrFail();

        return view('commercial.editAccessoire', compact('accessoire'));
    }


    public function updateArticle(Request $request, $reference)
    {
        // Validation des données
        $request->validate([
            'nom_article' => 'required|string|max:50',
            'prix'        => 'required|numeric|min:0',
            'poids'       => 'required|numeric|min:0',
            'materiau'    => 'required|string|max:50',
            'description' => 'required|string|max:5000',
        ], [
            'nom_article.required' => 'Le nom de l\'article est obligatoire.',
            'prix.required'        => 'Le prix est obligatoire.',
            'prix.numeric'         => 'Le prix doit être un nombre valide.',
            'poids.required'       => 'Le poids est obligatoire.',
            'materiau.required'    => 'Le matériau est obligatoire.',
            'description.required' => 'La description est obligatoire.',
        ]);

        try {
            DB::transaction(function () use ($request, $reference) {
                
             
                $accessoire = Accessoire::where('reference', $reference)->firstOrFail();

             
                $accessoire->update([
                    'nom_article' => $request->nom_article,
                    'prix'        => $request->prix,
                    'poids'       => $request->poids,
                    'materiau'    => $request->materiau,
                ]);

          
                // Il faut garder la cohérence entre les deux tables qui ont des colonnes communes
                $article = Article::where('reference', $reference)->first();
                if ($article) {
                    $article->update([
                        'nom_article' => $request->nom_article,
                        'prix'        => $request->prix,
                        'poids'       => $request->poids,
                    ]);

                    // On récupère le modèle Resume via l'ID stocké dans Article
                    $resume = Resume::find($article->id_resume);
                    if ($resume) {
                        $resume->update([
                            'contenu_resume' => $request->description
                        ]);
                    }
                }
            });

            return redirect()->route('commercial.edit.article')
                             ->with('success', 'L\'accessoire a été modifié avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la modification : ' . $e->getMessage()])
                         ->withInput();
        }
    }

    public function destroy($reference) {
        // Démarrer la transaction
        DB::beginTransaction();
    
        try {
            $isVelo = VarianteVelo::where('reference', $reference)->exists();
            
            if ($isVelo) {
                // Suppression pour le cas VarianteVelo
                ArticleInventaire::where('reference', $reference)->delete();
                ACaracteristique::where('reference', $reference)->delete();
                PhotoArticle::where('reference', $reference)->delete();
                VarianteVelo::where('reference', $reference)->delete();
                Article::where('reference', $reference)->delete();
            } else {
                // Suppression pour le cas Accessoire
                ArticleInventaire::where('reference', $reference)->delete();
                ACaracteristique::where('reference', $reference)->delete();
                PhotoArticle::where('reference', $reference)->delete();
                Accessoire::where('reference', $reference)->delete();
                Article::where('reference', $reference)->delete();
            }
    
   
            DB::commit();
    
            return back()->with('success', 'Article supprimé avec succès.');
        } catch (\Exception $e) {
      
            DB::rollBack();
            
            return back()->with('error', 'Impossible de supprimer cet article (il est peut-être lié à une commande).');
        }
    }
    


    public function addCategorie()
    {
    
        // On récupère toutes les catégories racines de vélos (celles qui n'ont pas de parent)
        $parentsVelos = CategorieVelo::whereNull('cat_id_categorie')->get();

        // On récupère les racines pour les Accessoires
        $parentsAccessoires = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')->get();

        return view('commercial.addCategorie', compact('parentsVelos', 'parentsAccessoires'));
    }


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



    public function addModele()
    {
        // On récupère toutes les catégories de vélos avec leurs enfants pour le menu dynamique
        $categoriesVelos = CategorieVelo::with('enfants')
                            ->whereNull('cat_id_categorie') // Seulement les racines (VTT, Route...)
                            ->get();

        return view('commercial.addModele', compact('categoriesVelos'));
    }


    public function storeModele(Request $request)
    {
     
            $validated = $request->validate([
            'type_velo'      => 'required|in:musculaire,electrique',
            'sub_category_id'=> 'required|integer',
            'nom_modele'     => 'required|string|max:50',
            
          
            'millesime'      => [
                'required',
                'digits:4',         
                'integer',         
                'min:1993',          
                'max:' . date("Y") 
            ],

            'materiau'       => 'required|string|max:50',
            
          
            'description'    => 'required|string|max:5000', 
        ], [
         
            
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

        
        try {
            DB::transaction(function () use ($request) { 
                

                $desc = Description::create([
                    'texte_description' => $request->description
                ]);

      
    
                Modele::create([
                    'id_categorie'     => $request->sub_category_id,
                    'id_description'   => $desc->id_description,
                    'nom_modele'       => $request->nom_modele,
                    'millesime_modele' => $request->millesime,
                    'materiau_cadre'   => $request->materiau,
                    'type_velo'        => $request->type_velo,
                ]);
            });

            // Si on arrive ici transaction a réussi
            return redirect()->route('commercial.dashboard')
                            ->with('success', 'Le modèle "' . $request->nom_modele . '" a été ajouté avec succès !');

        } catch (\Exception $e) {
     
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage()])
                        ->withInput();
        }
    }




    public function addVelo()
    {

        $modeles  = Modele::all(); /
        $couleurs = \App\Models\Couleur::all(); 
        $fourches = \App\Models\Fourche::all();
        $tailles  = \App\Models\Taille::whereNotNull('taille_min')->get();
        $batteries= \App\Models\Batterie::all(); 

        return view('commercial.addVelo', compact('modeles', 'couleurs', 'fourches', 'tailles', 'batteries'));
    }


    public function storeVelo(Request $request)
    {

        $rules = [
            'nom_article' => 'required|string|max:50',
            'prix'        => 'required|numeric|min:0',
            'poids'       => 'required|numeric|min:0',
            'id_modele'   => 'required|exists:modele,id_modele',
            'id_couleur'  => 'required|exists:couleur,id_couleur',
            'id_fourche'  => 'required|exists:fourche,id_fourche',
            'description' => 'required|string',
            'tailles'     => 'required|array|min:1',
            'tailles.*'   => 'exists:taille,id_taille',
        
            'stock'       => 'array',
            'stock.*'     => 'integer|min:0',
         
            'id_batterie' => 'nullable|integer|exists:batterie,id_batterie', 
        ];

    

        if ($request->filled('reference')) {
            $rules['reference'] = 'digits:6|unique:article,reference';
        } 


        $request->validate($rules, [
            'reference.digits' => 'La référence doit comporter exactement 6 chiffres.',
            'reference.unique' => 'Cette référence existe déjà dans la base de données.',
            'tailles.required' => 'Veuillez cocher au moins une taille.',
        ]);

        try {
            DB::transaction(function () use ($request) {
       
                $referenceFinale = null;

                if ($request->filled('reference')) {
             
                    $referenceFinale = $request->reference;
                } else {
         
                    do {
              
                        $randomRef = (string) mt_rand(100000, 999999);
               
                        $exists = Article::where('reference', $randomRef)->exists();
                    } while ($exists);
                    
                    $referenceFinale = $randomRef;
                }

                $batterieId = $request->filled('id_batterie') ? $request->id_batterie : null;



                $resume = Resume::create([
                    'contenu_resume' => $request->description
                ]);

            
                Article::create([
                    'reference'   => $referenceFinale,
                    'id_resume'   => $resume->id_resume,
                    'nom_article' => $request->nom_article,
                    'prix'        => $request->prix,
                    'poids'       => $request->poids,
                ]);


                VarianteVelo::create([
                    'reference'   => $referenceFinale,
                    'id_modele'   => $request->id_modele,
                    'id_fourche'  => $request->id_fourche,
                    'id_couleur'  => $request->id_couleur,
                    'id_batterie' => $batterieId, 
                    'nom_article' => $request->nom_article,
                    'prix'        => $request->prix,
                    'poids'       => $request->poids,
                ]);

              
                foreach ($request->tailles as $idTaille) {
                    
    
                    $quantite = isset($request->stock[$idTaille]) ? intval($request->stock[$idTaille]) : 0;

             
                    if ($quantite > 0) {
                        DB::table('article_inventaire')->insert([
                            'reference' => $referenceFinale,
                            'id_taille' => $idTaille,
                            'quantite_stock_en_ligne' => $quantite
                        ]);
                    }
                }

        
                DB::table('photo_article')->insert([
                    'reference' => $referenceFinale,
                    'url_photo' => 'https://placehold.co/600x400?text=Velo+' . $referenceFinale,
                    'est_principale' => true
                ]);
            });

 
            return redirect()->route('commercial.dashboard')
                             ->with('success', 'Vélo créé avec succès ! Référence enregistrée.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur technique : ' . $e->getMessage()])
                         ->withInput();
        }
    }

    public function addImageModele($reference)
    {
        $isVelo = VarianteVelo::where('reference', $reference)->exists();

        if (!$isVelo) {
            return back()->with('error', 'La modification des accessoires n\'est pas encore disponible. Uniquement les Vélos/VAE.');
        }

        $article = VarianteVelo::with('parent.resume')->where('reference', $reference)->firstOrFail();

        return view('commercial.addImageModele', compact('article'));
    }

    public function storeImageModele(Request $request)
    {
        $request->validate([
            'reference' => 'required|exists:article,reference',
            'photos'    => 'required|array',
            'photos.*'  => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'est_principale' => 'nullable|boolean'
        ]);
    
        $reference = $request->reference;
    
        $pathCible = public_path('images/VELOS/' . $reference);
    

        if (!file_exists($pathCible)) {
            mkdir($pathCible, 0777, true);
            chmod($pathCible, 0777);
        }
    
        try {
    
          
            $existingFiles = glob($pathCible . '/image_*.jpg');
    
            $maxNum = 0;
            foreach ($existingFiles as $file) {
                if (preg_match('/image_(\d+)\.jpg$/', $file, $matches)) {
                    $num = (int) $matches[1];
                    if ($num > $maxNum) {
                        $maxNum = $num;
                    }
                }
            }
    
            $isMainChecked = $request->has('est_principale');
    

            if ($isMainChecked && $maxNum > 0) {
    
 
                rsort($existingFiles);
    
                foreach ($existingFiles as $file) {
                    if (preg_match('/image_(\d+)\.jpg$/', $file, $matches)) {
                        $currentNum = (int) $matches[1];
                        $newNum = $currentNum + 1;
    
                        rename(
                            $pathCible . '/image_' . $currentNum . '.jpg',
                            $pathCible . '/image_' . $newNum . '.jpg'
                        );
                    }
                }
    
                $maxNum++;
            }
    

            foreach ($request->file('photos') as $index => $file) {
    
                if ($isMainChecked && $index === 0) {
                    $filename = 'image_1.jpg';
                } else {
                    $maxNum++;
                    $filename = 'image_' . $maxNum . '.jpg';
                }
    
                $fullPath = $pathCible . '/' . $filename;
    
                $extension = strtolower($file->getClientOriginalExtension());
    
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
    
                    switch ($extension) {
                        case 'png':
                            $sourceImage = imagecreatefrompng($file->getRealPath());
                            break;
                        case 'webp':
                            $sourceImage = imagecreatefromwebp($file->getRealPath());
                            break;
                        default:
                            $sourceImage = imagecreatefromjpeg($file->getRealPath());
                    }
    
                    $width  = imagesx($sourceImage);
                    $height = imagesy($sourceImage);
    
                    $outputImage = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($outputImage, 255, 255, 255);
                    imagefilledrectangle($outputImage, 0, 0, $width, $height, $white);
                    imagecopy($outputImage, $sourceImage, 0, 0, 0, 0, $width, $height);
    
                    imagejpeg($outputImage, $fullPath, 90);
    
                    imagedestroy($sourceImage);
                    imagedestroy($outputImage);
                } else {
                    $file->move($pathCible, $filename);
                }
    
                chmod($fullPath, 0644);
            }
    
            return redirect()->route('commercial.dashboard')
                ->with('success', 'Images sauvegardées uniquement dans les fichiers ✔');
    
        } catch (\Exception $e) {
            dd("Erreur traitement image : " . $e->getMessage());
        }
    }    

    public function articleListImage()
    {
  
        $velosMusculaires = VarianteVelo::with(['modele', 'photos'])
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'musculaire');
            })
            ->orderBy('reference', 'desc') 
            ->get();

        $velosElectriques = VarianteVelo::with(['modele', 'photos'])
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'electrique');
            })
            ->orderBy('reference', 'desc')
            ->get();

        $accessoires = Accessoire::with(['photos'])->orderBy('reference', 'desc')->get();

        return view('commercial.modifierArticleImage', compact('velosMusculaires', 'velosElectriques', 'accessoires'));
    }




    public function articleListCaracteristique()
    {
    
        $velosMusculaires = VarianteVelo::with('modele')
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'musculaire');
            })
            ->orderBy('reference', 'desc')
            ->get();

        $velosElectriques = VarianteVelo::with('modele')
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'electrique');
            })
            ->orderBy('reference', 'desc')
            ->get();


        $accessoires = Accessoire::orderBy('reference', 'desc')->get();

     
        return view('commercial.modifierArticleCaracteristique', compact('velosMusculaires', 'velosElectriques', 'accessoires'));
    }


    public function addCaracteristique($reference)
    {
        $velo = VarianteVelo::where('reference', $reference)->firstOrFail();

 
        $toutesLesCaracs = Caracteristique::orderBy('id_caracteristique', 'asc')->get();

        $valeursExistantes = DB::table('a_caracteristique')
                                ->where('reference', $reference)
                                ->pluck('valeur_caracteristique', 'id_caracteristique')
                                ->toArray();

        return view('commercial.addCaracteristique', compact('velo', 'toutesLesCaracs', 'valeursExistantes'));
    }


    public function storeCaracteristique(Request $request)
    {
        $request->validate([
            'reference' => 'required|exists:article,reference',
            'caracs'    => 'array', 
            'caracs.*'  => 'nullable|string|max:150'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $reference = $request->reference;

          
                foreach ($request->caracs as $idCarac => $valeur) {
                    
             
                    if (is_null($valeur) || trim($valeur) === '') {
                        DB::table('a_caracteristique')
                            ->where('reference', $reference)
                            ->where('id_caracteristique', $idCarac)
                            ->delete();
                    } 
          
                    else {
                        DB::table('a_caracteristique')->updateOrInsert(
                            [
                                'reference' => $reference, 
                                'id_caracteristique' => $idCarac
                            ],
                            [
                               
                                'valeur_caracteristique' => trim($valeur) 
                            ]
                        );
                    }
                }
            });

            return redirect()->route('commercial.choix.caracteristique')
                             ->with('success', 'Fiche technique mise à jour pour le vélo ' . $request->reference);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur sauvegarde : ' . $e->getMessage());
        }
    }
}