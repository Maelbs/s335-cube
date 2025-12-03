<?php

namespace App\Http\Controllers;

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
            'inventaires.taille'
        ];

        if ($this->estUnVelo($article)) {
            $article->load(array_merge($relationsCommunes, [
                'varianteVelo.modele.geometries',
                'varianteVelo.modele.tailles',
                'varianteVelo.modele.description',
                'varianteVelo.couleur',
                'varianteVelo.modele.varianteVelos.couleur'
            ]));

            $stock = $article->inventaires->keyBy('id_taille');
            $tailleGeometrie = $article->varianteVelo->modele->tailles->keyBy('id_taille');
            $typeVue = 'velo';
            $isAccessoire = ($typeVue === 'accessoire');

        } else {
            $article->load(array_merge($relationsCommunes, [
                'accessoire'
            ]));

            $stock = $article->accessoire->quantite_stock_accessoire;
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

        return view('vizualize_article', compact(
            'article',
            'stock',
            'tailleGeometrie',
            'specifications',
            'articlesSimilaires',
            'typeVue',
            'isAccessoire'
        ));
    }
    private function estUnVelo($article)
    {
        return $article->varianteVelo()->exists();
    }
}
