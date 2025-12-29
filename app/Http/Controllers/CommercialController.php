<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VarianteVelo;
use App\Models\Accessoire;
use App\Models\Article;

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
}