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

    public function editArticle($reference)
    {
        // A. Vérification : Est-ce un Vélo ? (Si oui, on refuse)
        $isVelo = VarianteVelo::where('reference', $reference)->exists();
        if ($isVelo) {
            return back()->with('error', 'La modification des Vélos/VAE n\'est pas encore disponible. Uniquement les accessoires.');
        }

        // B. Récupération de l'Accessoire avec son Article parent et le Résumé
        // On utilise 'with' pour charger les relations définies dans le Modèle
        $accessoire = Accessoire::with('parent.resume')
                        ->where('reference', $reference)
                        ->firstOrFail();

        return view('commercial.editAccessoire', compact('accessoire'));
    }

    // 2. ENREGISTRER LES MODIFICATIONS (UPDATE)
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
                
                // 1. Récupérer l'accessoire
                $accessoire = Accessoire::where('reference', $reference)->firstOrFail();

                // 2. Mise à jour de la table ACCESSOIRE
                $accessoire->update([
                    'nom_article' => $request->nom_article,
                    'prix'        => $request->prix,
                    'poids'       => $request->poids,
                    'materiau'    => $request->materiau,
                ]);

                // 3. Mise à jour de la table ARTICLE (Parent)
                // Il faut garder la cohérence entre les deux tables qui ont des colonnes communes
                $article = Article::where('reference', $reference)->first();
                if ($article) {
                    $article->update([
                        'nom_article' => $request->nom_article,
                        'prix'        => $request->prix,
                        'poids'       => $request->poids,
                    ]);

                    // 4. Mise à jour de la table RESUME (Liée à Article)
                    // On récupère le modèle Resume via l'ID stocké dans Article
                    $resume = Resume::find($article->id_resume);
                    if ($resume) {
                        $resume->update([
                            'contenu_resume' => $request->description
                        ]);
                    }
                }
            });

            return redirect()->route('commercial.edit.article') // Retour à la liste
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
    
            // Commit la transaction, c'est-à-dire applique les suppressions
            DB::commit();
    
            return back()->with('success', 'Article supprimé avec succès.');
        } catch (\Exception $e) {
            // Si une erreur survient, annule les changements (rollback)
            DB::rollBack();
            
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

    // --- GESTION DES VÉLOS (US40) ---

    // 1. AFFICHER LE FORMULAIRE DE CRÉATION DE VÉLO
    public function addVelo()
    {
        // On récupère les données pour remplir les <select> du formulaire
        $modeles  = Modele::all(); // Pour choisir "Pi-pop" (ou le créer avant si besoin)
        $couleurs = \App\Models\Couleur::all(); // Pour choisir "Rouge"
        $fourches = \App\Models\Fourche::all(); // Obligatoire selon le SQL (not null)
        $tailles  = \App\Models\Taille::whereNotNull('taille_min')->get();
        $batteries= \App\Models\Batterie::all(); // Optionnel (pour VAE)

        return view('commercial.addVelo', compact('modeles', 'couleurs', 'fourches', 'tailles', 'batteries'));
    }

    // 2. ENREGISTRER LE VÉLO (TRANSACTION COMPLEXE)
    public function storeVelo(Request $request)
    {
        // 1. Validation des données communes
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
            // On valide le tableau des stocks
            'stock'       => 'array',
            'stock.*'     => 'integer|min:0',
            // On autorise la batterie à être vide ou un ID valide
            'id_batterie' => 'nullable|integer|exists:batterie,id_batterie', 
        ];

        // 2. Logique spécifique pour la Référence
        // Si l'utilisateur a entré une ref, on la valide strictement
        if ($request->filled('reference')) {
            $rules['reference'] = 'digits:6|unique:article,reference';
        } 
        // Si vide, pas de validation "required", on la générera plus tard

        $request->validate($rules, [
            'reference.digits' => 'La référence doit comporter exactement 6 chiffres.',
            'reference.unique' => 'Cette référence existe déjà dans la base de données.',
            'tailles.required' => 'Veuillez cocher au moins une taille.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                // --- A. GESTION DE LA RÉFÉRENCE (Check & Random) ---
                $referenceFinale = null;

                if ($request->filled('reference')) {
                    // Cas 1 : L'utilisateur a fourni une référence (déjà validée unique ci-dessus)
                    $referenceFinale = $request->reference;
                } else {
                    // Cas 2 : Génération aléatoire d'une référence libre (6 chiffres)
                    do {
                        // Génère un nombre entre 100000 et 999999
                        $randomRef = (string) mt_rand(100000, 999999);
                        // Vérifie si elle existe déjà
                        $exists = Article::where('reference', $randomRef)->exists();
                    } while ($exists); // Recommence tant qu'on trouve un doublon
                    
                    $referenceFinale = $randomRef;
                }

                // --- B. GESTION DE LA BATTERIE (Null handling) ---
                // Si le champ est vide, on force NULL, sinon on prend la valeur
                $batterieId = $request->filled('id_batterie') ? $request->id_batterie : null;


                // --- C. INSERTIONS EN BASE ---

                // 1. Création du Résumé
                $resume = Resume::create([
                    'contenu_resume' => $request->description
                ]);

                // 2. Création de l'Article (Parent)
                Article::create([
                    'reference'   => $referenceFinale,
                    'id_resume'   => $resume->id_resume,
                    'nom_article' => $request->nom_article,
                    'prix'        => $request->prix,
                    'poids'       => $request->poids,
                ]);

                // 3. Création de la Variante Vélo (Enfant)
                VarianteVelo::create([
                    'reference'   => $referenceFinale,
                    'id_modele'   => $request->id_modele,
                    'id_fourche'  => $request->id_fourche,
                    'id_couleur'  => $request->id_couleur,
                    'id_batterie' => $batterieId, // Sera NULL ou un ID
                    'nom_article' => $request->nom_article,
                    'prix'        => $request->prix,
                    'poids'       => $request->poids,
                ]);

                // 4. Gestion du Stock par Taille (US40)
                foreach ($request->tailles as $idTaille) {
                    
                    // On récupère la quantité saisie pour cette taille précise
                    // $request->stock est un tableau associatif : [50 => 10, 55 => 0, ...]
                    $quantite = isset($request->stock[$idTaille]) ? intval($request->stock[$idTaille]) : 0;

                    // CONDITION : Si quantité > 0, on insère.
                    // Si quantité = 0, on considère que le vélo n'est pas dispo dans cette taille
                    if ($quantite > 0) {
                        DB::table('article_inventaire')->insert([
                            'reference' => $referenceFinale,
                            'id_taille' => $idTaille,
                            'quantite_stock_en_ligne' => $quantite
                        ]);
                    }
                }

                // 5. Photo Placeholder (Optionnel)
                DB::table('photo_article')->insert([
                    'reference' => $referenceFinale,
                    'url_photo' => 'https://placehold.co/600x400?text=Velo+' . $referenceFinale,
                    'est_principale' => true
                ]);
            });

            // Succès : on redirige vers le dashboard avec un message
            // Note: On peut récupérer la ref générée via une variable de session flash si on veut l'afficher
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

    // 2. Enregistrer les photos (Dossier + BDD)
public function storeImageModele(Request $request)
    {
        $request->validate([
            'reference' => 'required|exists:article,reference',
            'photos'    => 'required|array',
            'photos.*'  => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'est_principale' => 'nullable|boolean'
        ]);

        $reference = $request->reference;

        // Chemins IUT (Adaptés à ton serveur)
        $pathImages = public_path('images');
        $pathVelos  = public_path('images/VELOS');
        $pathCible  = public_path('images/VELOS/' . $reference);

        // Création des dossiers (Sécurité IUT)
        if (!file_exists($pathImages)) { mkdir($pathImages, 0777, true); @chmod($pathImages, 0777); }
        if (!file_exists($pathVelos))  { mkdir($pathVelos, 0777, true);  @chmod($pathVelos, 0777); }
        if (!file_exists($pathCible))  { mkdir($pathCible, 0777, true);  @chmod($pathCible, 0777); }

        try {
            DB::transaction(function () use ($request, $pathCible, $reference) {
                
                // 1. Récupérer les photos existantes pour savoir où on en est
                // On utilise la BDD comme référence
                $existingPhotos = DB::table('photo_article')
                                    ->where('reference', $reference)
                                    ->get();
                
                // Trouver le numéro le plus élevé (ex: si image_5.jpg existe, max = 5)
                $maxNum = 0;
                foreach($existingPhotos as $p) {
                    if(preg_match('/image_(\d+)\.jpg$/', $p->url_photo, $matches)) {
                        $num = (int)$matches[1];
                        if($num > $maxNum) $maxNum = $num;
                    }
                }

                $isMainChecked = $request->has('est_principale');

                // --- LOGIQUE DE DÉCALAGE (SHIFT) ---
                // Si on veut mettre la nouvelle en n°1, il faut pousser toutes les autres
                if ($isMainChecked && $maxNum > 0) {
                    
                    // On trie les photos existantes par numéro décroissant (pour renommer 3->4 avant 2->3)
                    $sortedPhotos = $existingPhotos->sort(function($a, $b) {
                        preg_match('/image_(\d+)\.jpg$/', $a->url_photo, $m_a);
                        preg_match('/image_(\d+)\.jpg$/', $b->url_photo, $m_b);
                        return ((int)($m_b[1] ?? 0)) <=> ((int)($m_a[1] ?? 0));
                    });

                    foreach ($sortedPhotos as $photo) {
                        if(preg_match('/image_(\d+)\.jpg$/', $photo->url_photo, $matches)) {
                            $currentNum = (int)$matches[1];
                            $newNum = $currentNum + 1; // On décale de +1
                            
                            $oldFilename = 'image_' . $currentNum . '.jpg';
                            $newFilename = 'image_' . $newNum . '.jpg';
                            
                            $oldPath = $pathCible . '/' . $oldFilename;
                            $newPath = $pathCible . '/' . $newFilename;

                            // 1. Renommer le fichier physique
                            if (file_exists($oldPath)) {
                                rename($oldPath, $newPath);
                            }

                            // 2. Mettre à jour la BDD
                            DB::table('photo_article')
                                ->where('id_photo', $photo->id_photo)
                                ->update([
                                    'url_photo' => 'images/VELOS/' . $reference . '/' . $newFilename,
                                    'est_principale' => false // L'ancienne principale ne l'est plus
                                ]);
                        }
                    }
                    // Le max a augmenté de 1 car tout a bougé
                    $maxNum++;
                }

                // --- TRAITEMENT DES NOUVELLES IMAGES ---
                foreach ($request->file('photos') as $index => $file) {
                    
                    if ($isMainChecked && $index === 0) {
                        // C'est LA photo principale : elle prend la place n°1 (libérée par le décalage)
                        $filename = 'image_1.jpg';
                        $isMain = true;
                    } else {
                        // Les autres s'ajoutent à la suite
                        $maxNum++; 
                        $filename = 'image_' . $maxNum . '.jpg';
                        $isMain = false;
                    }

                    $fullPath = $pathCible . '/' . $filename;

                    $sourceImage = null;
                    $extension = strtolower($file->getClientOriginalExtension());

                    if ($extension === 'jpg' || $extension === 'jpeg') {
                        $sourceImage = imagecreatefromjpeg($file->getRealPath());
                    } elseif ($extension === 'png') {
                        $sourceImage = imagecreatefrompng($file->getRealPath());
                    } elseif ($extension === 'webp') {
                        $sourceImage = imagecreatefromwebp($file->getRealPath());
                    }

                    if ($sourceImage) {
                        $width  = imagesx($sourceImage);
                        $height = imagesy($sourceImage);
                        $outputImage = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($outputImage, 255, 255, 255);
                        imagefilledrectangle($outputImage, 0, 0, $width, $height, $white);
                        imagecopy($outputImage, $sourceImage, 0, 0, 0, 0, $width, $height);
                        
                        // Sauvegarde
                        imagejpeg($outputImage, $fullPath, 90);
                        imagedestroy($sourceImage);
                        imagedestroy($outputImage);
                    } else {
                        // Fallback
                        $file->move($pathCible, $filename);
                    }

                    // Permissions
                    @chmod($fullPath, 0644);

                    // Insertion BDD
                    $dbUrl = 'images/VELOS/' . $reference . '/' . $filename;

                    DB::table('photo_article')->insert([
                        'reference'      => $reference,
                        'url_photo'      => $dbUrl,
                        'est_principale' => $isMain
                    ]);
                }
            });

            return redirect()->route('commercial.dashboard')
                             ->with('success', 'Images sauvegardées et numérotées correctement !');

        } catch (\Exception $e) {
            dd("Erreur traitement image : " . $e->getMessage());
        }
    }

    public function articleListImage()
    {
        // 1. Récupérer les Vélos MUSCULAIRES
        // On utilise 'whereHas' pour filtrer directement sur la table liée 'modele'
        $velosMusculaires = VarianteVelo::with(['modele', 'photos'])
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'musculaire');
            })
            ->orderBy('reference', 'desc') // Trie par référence décroissante (optionnel)
            ->get();

        // 2. Récupérer les Vélos ÉLECTRIQUES
        $velosElectriques = VarianteVelo::with(['modele', 'photos'])
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'electrique');
            })
            ->orderBy('reference', 'desc')
            ->get();

        // 3. Récupérer les ACCESSOIRES
        // On charge aussi les photos pour l'affichage des miniatures
        $accessoires = Accessoire::with(['photos'])->orderBy('reference', 'desc')->get();

        return view('commercial.modifierArticleImage', compact('velosMusculaires', 'velosElectriques', 'accessoires'));
    }

// --- US42 : GESTION DES CARACTÉRISTIQUES ---

// 1. LISTE DES ARTICLES POUR CARACTÉRISTIQUES
    public function articleListCaracteristique()
    {
        // 1. Récupérer les Vélos MUSCULAIRES
        $velosMusculaires = VarianteVelo::with('modele')
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'musculaire');
            })
            ->orderBy('reference', 'desc')
            ->get();

        // 2. Récupérer les Vélos ÉLECTRIQUES
        $velosElectriques = VarianteVelo::with('modele')
            ->whereHas('modele', function ($query) {
                $query->where('type_velo', 'electrique');
            })
            ->orderBy('reference', 'desc')
            ->get();

        // 3. Récupérer les ACCESSOIRES
        // La vue attend la variable $accessoires, même si on ne met pas souvent de fiche technique dessus
        $accessoires = Accessoire::orderBy('reference', 'desc')->get();

        // On passe les 3 variables à la vue pour éviter l'erreur "count(null)"
        return view('commercial.modifierArticleCaracteristique', compact('velosMusculaires', 'velosElectriques', 'accessoires'));
    }

    // 2. AFFICHER LE FORMULAIRE
    public function addCaracteristique($reference)
    {
        $velo = VarianteVelo::where('reference', $reference)->firstOrFail();

        // CORRECTION ICI : On trie par 'id_caracteristique' ascendant
        $toutesLesCaracs = Caracteristique::orderBy('id_caracteristique', 'asc')->get();

        $valeursExistantes = DB::table('a_caracteristique')
                                ->where('reference', $reference)
                                ->pluck('valeur_caracteristique', 'id_caracteristique')
                                ->toArray();

        return view('commercial.addCaracteristique', compact('velo', 'toutesLesCaracs', 'valeursExistantes'));
    }

    // 3. ENREGISTRER
    public function storeCaracteristique(Request $request)
    {
        $request->validate([
            'reference' => 'required|exists:article,reference',
            'caracs'    => 'array', // Tableau [id => valeur]
            'caracs.*'  => 'nullable|string|max:150'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $reference = $request->reference;

                // On parcourt chaque champ du formulaire
                foreach ($request->caracs as $idCarac => $valeur) {
                    
                    // Si vide => On supprime la ligne (Nettoyage)
                    if (is_null($valeur) || trim($valeur) === '') {
                        DB::table('a_caracteristique')
                            ->where('reference', $reference)
                            ->where('id_caracteristique', $idCarac)
                            ->delete();
                    } 
                    // Si rempli => On Insère ou on Met à jour
                    else {
                        DB::table('a_caracteristique')->updateOrInsert(
                            [
                                'reference' => $reference, 
                                'id_caracteristique' => $idCarac
                            ],
                            [
                                // Attention à l'orthographe de ta colonne en BDD ici !
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