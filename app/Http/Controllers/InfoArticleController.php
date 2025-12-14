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

        // --- MODIFICATION ICI : On ajoute 'inventaires.magasins' ---
        // Cela permet de récupérer le stock de chaque déclinaison dans chaque magasin (table pivot)
        $relationsCommunes = [
            'caracteristiques.typeCaracteristique',
            'photos',
            'resume',
            'inventaires.taille',
            'inventaires.magasins' // <--- INDISPENSABLE pour le store locator
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

            // On garde le keyBy si tu en as besoin pour tes boutons de taille
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

        // --- AJOUT POUR LE STORE LOCATOR ---
        // On a besoin de la liste de tous les magasins pour initialiser la carte et la liste
        // On charge aussi 'adresses' pour afficher la ville et le code postal
        $tousLesMagasins = MagasinPartenaire::with('adresses')->get();

        return view('vizualizeArticle', compact(
            'article',
            'stock',
            'tailleGeometrie',
            'specifications',
            'articlesSimilaires',
            'typeVue',
            'isAccessoire',
            'tousLesMagasins' // <--- On envoie cette variable à la vue
        ));
    }
    private function estUnVelo($article)
    {
        return $article->varianteVelo()->exists();
    }
}
