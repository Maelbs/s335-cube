<?php

namespace App\Http\Controllers;

use App\Models\VarianteVelo;
use Illuminate\Http\Request;
use App\Models\Article;


class VarianteVeloController extends Controller
{
    public function index()
    {
        $variantes = VarianteVelo::with([
            'couleur', 
            'fourche',
            'modele.categorie',
            'batterie',
            'modele.description',
            'resume'
        ])->get();


        return response()->json($variantes);
    }

    public function show($reference)
    {
        // 1. Récupérer l'article grâce à la référence
        // On charge 'caracteristiques' ET le 'typeCaracteristique' de chaque caractéristique
        $velo = Article::where('reference', $reference)
            ->with(['caracteristiques.typeCaracteristique', 'photos', 'varianteVelo.modele.geometries', 'varianteVelo.modele.tailles', 'varianteVelo.modele.description', 'varianteVelo.resume'])
            ->firstOrFail(); // Renvoie une 404 si la réf n'existe pas

        // 2. Grouper les caractéristiques par leur Type
        // Cela va créer un tableau associatif où la clé est le nom du type (ex: "CADRE", "SUSPENSIONS")
        $specifications = $velo->caracteristiques->groupBy(function ($item) {
            return $item->typeCaracteristique->nom_type_caracteristique;
        });

        // 3. Envoyer à la vue
        return view('vizualize_article', compact('velo', 'specifications'));
    }
}
