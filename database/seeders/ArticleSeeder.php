<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Accessoire;
use App\Models\CategorieAccessoire;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $categorie = CategorieAccessoire::firstOrCreate(
            ['nom_categorie_accessoire' => 'Éclairages'],
            ['cat_id_categorie_accessoire' => null]
        );

        $ref = 'ECLAIR001';
        $nom = 'Lampe LED Avant';
        $prix = 29.99;
        $stock = 50;
        $poids = 0.2;
        $dispo = true;

        if (!Article::where('reference', $ref)->exists()) {
            Article::create([
                'reference'      => $ref,
                'nom_article'    => $nom,
                'prix'           => $prix,
                'qte_stock'      => $stock,
                'dispo_en_ligne' => $dispo,
                'poids'          => $poids,
            ]);
        }

        if (!Accessoire::where('reference', $ref)->exists()) {
            Accessoire::create([
                'reference'               => $ref,
                'id_categorie_accessoire' => $categorie->id_categorie_accessoire,
                'nom_article'             => $nom,
                'prix'                    => $prix,
                'qte_stock'               => $stock,
                'dispo_en_ligne'          => $dispo,
                'poids'                   => $poids,
            ]);
        }
        
        $this->command->info('Accessoire de test créé avec succès !');
    }
}