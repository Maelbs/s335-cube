<?php

namespace App\Http\Controllers;

use App\Models\VarianteVelo;
use Illuminate\Http\Request;

class VarianteVeloController extends Controller
{
    public function index()
    {
        $variantes = VarianteVelo::with([
            'couleur', 
            'fourche',
            'modele.categorie',
            'batterie',
        ])->get();


        return response()->json($variantes);
    }
}
