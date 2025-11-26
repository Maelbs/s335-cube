<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieAccessoire;

class SousCategorieVeloController extends Controller
{
    public function index()
    {
        $categoriesParents = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')->get();
        dd($categoriesParents);
        return view('accueil', compact('categoriesParents'));
    }
    public function getSubCategories($parentId)
    {
        $subCategories = Category::where('id_categorie_accessoire', $parentId)->get();
        return response()->json($subCategories);
    }

    public function getParents()
    {
        dd("Je suis bien dans la fonction !");
        $parents = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')->get();
        
        return response()->json($parents);
    }
}
