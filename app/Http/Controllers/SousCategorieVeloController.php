<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SousCategorieVeloController extends Controller
{
    public function getSubCategories($parentId)
    {
        $subCategories = Category::where('cat_id_categorie_accessoire', $parentId)->get();
        return response()->json($subCategories);
    }
}
