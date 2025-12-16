<?php

namespace App\Http\Controllers;

use App\Models\MagasinPartenaire;
use Illuminate\Http\Request;
use App\Models\Article;

class InfoArticleController extends Controller
{
    public function show($reference)
    {
        $article = Article::where('reference', $reference)->firstOrFail();

        $relationsCommunes = [
            'caracteristiques.typeCaracteristique',
            'photos',
            'resume',
            'inventaires.taille',
            'inventaires.magasins' 
        ];

        if ($this->estUnVelo($article)) {
            $article->load(array_merge($relationsCommunes, [
                'varianteVelo.modele.geometries',
                'varianteVelo.modele.tailles',
                'varianteVelo.modele.description',
                'varianteVelo.modele',
                'varianteVelo.couleur',
                'varianteVelo.modele.varianteVelos.couleur',
                'varianteVelo.accessoires'
            ]));

            $stock = $article->inventaires->keyBy('id_taille');
            $tailleGeometrie = $article->varianteVelo->modele->tailles->keyBy('id_taille');
            $typeVue = 'velo';
            $isAccessoire = ($typeVue === 'accessoire');

        } else {
            $article->load(array_merge($relationsCommunes, [
                'accessoire'
            ]));

            $stock = $article->inventaires;
            $tailleGeometrie = null;
            $typeVue = 'accessoire';
            $isAccessoire = ($typeVue === 'accessoire');
        }

        $specifications = $article->caracteristiques->groupBy(function ($item) {
            return $item->typeCaracteristique->nom_type_caracteristique;
        });

        $articlesSimilaires = Article::whereIn('reference', function ($query) use ($reference) {
            $query->select('art_reference')->from('article_similaire')->where('reference', $reference);
        })
            ->orWhereIn('reference', function ($query) use ($reference) {
                $query->select('reference')->from('article_similaire')->where('art_reference', $reference);
            })
            ->distinct()
            ->get();

        $tousLesMagasins = MagasinPartenaire::with('adresses')->get();

        return view('vizualizeArticle', compact(
            'article',
            'stock',
            'tailleGeometrie',
            'specifications',
            'articlesSimilaires',
            'typeVue',
            'isAccessoire',
            'tousLesMagasins' 
        ));
    }
    private function estUnVelo($article)
    {
        return $article->varianteVelo()->exists();
    }
}
