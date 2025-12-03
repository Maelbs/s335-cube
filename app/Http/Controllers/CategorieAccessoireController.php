<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieAccessoire; 

class CategorieAccessoireController extends Controller
{
    public function index() 
    {
        $categorieAccessoires = CategorieAccessoire::whereNull('cat_id_categorie_accessoire')
                    ->with('enfants')
                    ->get();

        return view('accueil', compact($categorieAccessoires));
    }
}