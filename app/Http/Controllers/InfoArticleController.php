<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfoArticleController extends Controller
{
    public function show($reference)
    {
        $article = Article::where('reference', $reference)->firstOrFail();

        $relationsCommunes = [
            'caracteristiques.typeCaracteristique',
            'photos',
        ];

        if ($this->estUnVelo($article)) {
            $article->load(array_merge($relationsCommunes, [
                'varianteVelo.modele.geometries',
                'varianteVelo.modele.tailles',
                'varianteVelo.modele.description',
                'varianteVelo.resume',
                'varianteVelo.inventaires.taille',
                'varianteVelo.couleur',
                'varianteVelo.modele.varianteVelos.couleur'
            ]));

            $stock = $article->varianteVelo->inventaires->keyBy('id_taille');
            $tailleGeometrie = $article->varianteVelo->modele->tailles->keyBy('id_taille');
            $typeVue = 'velo';

        } else {
            $article->load(array_merge($relationsCommunes, [
                'accessoire'
            ]));

            $stock = $article->stock_global;
            $tailleGeometrie = null;
            $typeVue = 'accessoire';
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

        return view('vizualize_article', compact(
            'article',
            'stock',
            'tailleGeometrie',
            'specifications',
            'articlesSimilaires',
            'typeVue'
        ));
    }
    private function estUnVelo($article)
    {
        return $article->varianteVelo()->exists();
    }
}
