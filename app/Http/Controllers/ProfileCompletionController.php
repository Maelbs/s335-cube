<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adresse;
use Illuminate\Support\Facades\Auth;

class ProfileCompletionController extends Controller
{
    public function showForm()
    {
        return view('completeProfile');
    }

    public function saveDetails(Request $request)
    {
        $request->validate([
            'tel'     => 'required|string|max:20',
            'date_naissance' => 'required|date',
            'rue'     => 'required|string',
            'city'    => 'required|string',
            'zipcode' => 'required|string',
            'country' => 'required|string',
        ], [
            'city.required' => 'Le champ ville est obligatoire.',
            'zipcode.required' => 'Le champ code postal est obligatoire.',
            'country.required' => 'Le champ pays est obligatoire.',
        ]);

        $client = Auth::user();

        $adresse = new Adresse();

        $adresse->rue         = $request->input('rue');
        $adresse->ville       = $request->input('city'); 
        $adresse->code_postal = $request->input('zipcode');
        $adresse->pays        = $request->input('country');  
        
        $adresse->save();

        $client->tel = $request->input('tel');
        $client->date_naissance = $request->input('date_naissance');
        $client->id_adresse_facturation = $adresse->id_adresse; 
        
        $client->save();

        return redirect()->route('home')->with('success', 'Profil complété avec succès !');
    }
}