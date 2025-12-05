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
            'resume',
        ])->get();


        return $variantes;
    }

    public function show($reference)
    {
        $velo = Article::where('reference', $reference)
            ->with(['caracteristiques.typeCaracteristique', 
            'photos', 
            'varianteVelo.modele.geometries', 
            'varianteVelo.modele.tailles', 
            'varianteVelo.modele.description', 
            'varianteVelo.resume',
            'varianteVelo.inventaires.taille',
            'varianteVelo.couleur',
            'varianteVelo.modele.varianteVelos.couleur'])
            ->firstOrFail(); 

        $specifications = $velo->caracteristiques->groupBy(function ($item) {
            return $item->typeCaracteristique->nom_type_caracteristique;
        });

        $stockParIdTaille = $velo->varianteVelo->inventaires->keyBy('id_taille');
        $tailleGeometrie = $velo->varianteVelo->modele->tailles->keyBy('id_taille');



        $velosSimilaires = Article::whereIn('reference', function($query) use ($reference) {
            $query->select('art_reference')
                  ->from('article_similaire')
                  ->where('reference', $reference);
        })
        ->orWhereIn('reference', function($query) use ($reference) {
            $query->select('reference')
                  ->from('article_similaire')
                  ->where('art_reference', $reference);
        })
        ->distinct()
        ->get();

        
        return view('vizualizeArticle', compact('velo', 'stockParIdTaille', 'tailleGeometrie', 'specifications', 'velosSimilaires'));
    }
}
