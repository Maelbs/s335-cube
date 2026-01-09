<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Adresse;

class AdresseController extends Controller
{
    public function index()
    {
        $client = Auth::user();
    
        $adresseFacturation = $client->adresseFacturation;
    
        $adressesLivraison = $client->adressesLivraison;
    
        return view('listAdresses', compact('adresseFacturation', 'adressesLivraison', 'client'));
    }    

    public function editAdresse($id)
    {
        $client = Auth::user();
        $adresse = Adresse::findOrFail($id);
    
        $isFacturation = $client->id_adresse_facturation == $id;
    
        $isLivraison = $client->adressesLivraison->contains('id_adresse', $id);
    
        if (! $isFacturation && ! $isLivraison) {
            abort(403, "Cette adresse ne vous appartient pas.");
        }
    
        return view('modifierAdresse', compact('adresse', 'isFacturation', 'client'));
    }
    
    public function update(Request $request, $id)
    {
        $client = Auth::user();
        $adresse = Adresse::findOrFail($id);

        $isFacturation = $client->id_adresse_facturation == $id;
        $isLivraison = $client->adressesLivraison->contains('id_adresse', $id);

        if (! $isFacturation && ! $isLivraison) {
            abort(403);
        }

        $request->validate([
            'rue' => 'required|string|max:255',
            'code_postal' => 'required|string|max:20',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ]);

        $adresse->update([
            'rue' => $request->rue,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'pays' => $request->pays,
        ]);

        return redirect()->route('client.adresses')->with('success', 'Adresse mise à jour avec succès.');
    }

    public function createAdresse()
    {
        $client = Auth::user(); 
        return view('creerAdresse', compact('client'));
    }
    

    public function create(Request $request)
    {
        $client = Auth::user();
    
        $request->validate([
            'rue' => 'required|string|max:255',
            'code_postal' => 'required|string|max:20',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ]);
    
        $adresse = new Adresse([
            'rue' => $request->rue,
            'code_postal' => $request->code_postal,
            'ville' => $request->ville,
            'pays' => $request->pays,
        ]);
    
        $adresse->save();
    
        $client->adressesLivraison()->attach($adresse->id_adresse);
    
        return redirect()->route('client.adresses')->with('success', 'Adresse de livraison ajoutée avec succès.');
    }
}