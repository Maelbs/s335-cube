<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieVelo;

class CategorieVeloController extends Controller
{
    public function getParents()
    {
        $parents = CategorieVelo::whereNull('cat_id_categorie')->get();
        return response()->json($parents);
    }

    public function getSubCategories($parentId)
    {
        $enfants = CategorieVelo::where('cat_id_categorie', $parentId)->get();
        return response()->json($enfants);
    }
}
