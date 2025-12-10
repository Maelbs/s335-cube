<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Ou App\Models\Client selon votre setup

class MagasinController extends Controller
{
    public function definirMagasin(Request $request)
    {
        $validated = $request->validate([
            'id_magasin' => 'required|exists:magasin_partenaire,id_magasin',
        ]);

        $idMagasin = $validated['id_magasin'];

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $user->id_magasin = $idMagasin;

            $user->save();
        }

        session(['id_magasin_choisi' => $idMagasin]);

        return back()->with('succes_magasin', 'Votre magasin favori a été mis à jour !');
    }
}