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
        // 1. Validation des données
        $rules = [
            'tel'     => 'required|string|max:20',
            'rue'     => 'required|string',
            'city'    => 'required|string',
            'zipcode' => 'required|string',
            'country' => 'required|string',
        ];

        // Si l'utilisateur veut une adresse de facturation différente, on valide les champs 'billing_'
        if (!$request->has('use_same_address')) {
            $rules['billing_rue']     = 'required|string';
            $rules['billing_city']    = 'required|string';
            $rules['billing_zipcode'] = 'required|string';
            $rules['billing_country'] = 'required|string';
        }

        $request->validate($rules);
        
        $client = Auth::user();

        // === ÉTAPE 1 : Création de l'adresse de LIVRAISON ===
        // On crée toujours cette adresse en premier
        $adresseLivraison = new Adresse();
        $adresseLivraison->rue         = $request->input('rue');
        $adresseLivraison->ville       = $request->input('city');
        $adresseLivraison->code_postal = $request->input('zipcode');
        $adresseLivraison->pays        = $request->input('country');
        $adresseLivraison->save();

        // === ÉTAPE 2 : Liaison Livraison -> Client (Table Pivot) ===
        // On vérifie si le lien existe déjà pour éviter les doublons (prudence)
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

        // === ÉTAPE 3 : Création de l'adresse de FACTURATION ===
        // On crée une NOUVELLE ligne quoiqu'il arrive (Double Insert)
        $adresseFacturation = new Adresse();

        if ($request->has('use_same_address')) {
            // Cas A : Identique -> On copie les données saisies pour la livraison
            $adresseFacturation->rue         = $request->input('rue');
            $adresseFacturation->ville       = $request->input('city');
            $adresseFacturation->code_postal = $request->input('zipcode');
            $adresseFacturation->pays        = $request->input('country');
        } else {
            // Cas B : Différente -> On prend les champs spécifiques facturation
            $adresseFacturation->rue         = $request->input('billing_rue');
            $adresseFacturation->ville       = $request->input('billing_city');
            $adresseFacturation->code_postal = $request->input('billing_zipcode');
            $adresseFacturation->pays        = $request->input('billing_country');
        }
        
        $adresseFacturation->save(); // On sauvegarde la 2ème adresse (ID différent)

        // === ÉTAPE 4 : Mise à jour du Profil Client ===
        $client->tel = $request->input('tel');
        
        // On lie le client à cette NOUVELLE adresse de facturation
        $client->id_adresse_facturation = $adresseFacturation->id_adresse; 
        
        if ($request->has('date_naissance')) {
            $client->date_naissance = $request->input('date_naissance');
        }
        
        $client->save();

        return redirect()->route('home')->with('success', 'Profil complété avec succès !');
    }
}