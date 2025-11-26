<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieAccessoire; 

class CategorieAccessoireController extends Controller
{
    public function getParents()
    {
        $parents = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')->get();
        return response()->json($parents);
    }

    public function getSubCategories($parentId)
    {
        $enfants = CategorieAccessoire::where('cat_id_categorie_accessoire', $parentId)->get();
        return response()->json($enfants);
    }
}