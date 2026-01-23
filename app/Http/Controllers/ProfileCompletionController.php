<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Adresse;
use App\Models\Client;

class ProfileCompletionController extends Controller
{
    public function showForm()
    {
        return view('completeProfile');
    }

    public function saveDetails(Request $request)
    {
        $rules = [
            'tel'     => 'required|string|max:20',
            'rue'     => 'required|string',
            'city'    => 'required|string',
            'zipcode' => 'required|string',
            'country' => 'required|string',
        ];

        if (!$request->has('use_same_address')) {
            $rules['billing_rue']     = 'required|string';
            $rules['billing_city']    = 'required|string';
            $rules['billing_zipcode'] = 'required|string';
            $rules['billing_country'] = 'required|string';
        }

        $request->validate($rules);
        
        $client = Auth::user();

        $adresseLivraison = new Adresse();
        $adresseLivraison->rue         = $request->input('rue');
        $adresseLivraison->ville       = $request->input('city');
        $adresseLivraison->code_postal = $request->input('zipcode');
        $adresseLivraison->pays        = $request->input('country');
        $adresseLivraison->save();

        $exists = DB::table('adresse_livraison')
                    ->where('id_client', $client->id_client)
                    ->where('id_adresse', $adresseLivraison->id_adresse)
                    ->exists();

        if (!$exists) {
            DB::table('adresse_livraison')->insert([
                'id_client'  => $client->id_client,
                'id_adresse' => $adresseLivraison->id_adresse,
            ]);
        }

        $adresseFacturation = new Adresse();

        if ($request->has('use_same_address')) {
            $adresseFacturation->rue         = $request->input('rue');
            $adresseFacturation->ville       = $request->input('city');
            $adresseFacturation->code_postal = $request->input('zipcode');
            $adresseFacturation->pays        = $request->input('country');
        } else {
            $adresseFacturation->rue         = $request->input('billing_rue');
            $adresseFacturation->ville       = $request->input('billing_city');
            $adresseFacturation->code_postal = $request->input('billing_zipcode');
            $adresseFacturation->pays        = $request->input('billing_country');
        }
        
        $adresseFacturation->save(); 

  
        $client->tel = $request->input('tel');
        
        $client->id_adresse_facturation = $adresseFacturation->id_adresse; 
        
        if ($request->has('date_naissance')) {
            $client->date_naissance = $request->input('date_naissance');
        }
        
        $client->save();

        return redirect()->route('home')->with('success', 'Profil complété avec succès !');
    }
}