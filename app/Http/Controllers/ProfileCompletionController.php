<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adresse; // Ton modèle Adresse
use Illuminate\Support\Facades\Auth;

class ProfileCompletionController extends Controller
{
    // Afficher le formulaire
    public function showForm()
    {
        return view('completeProfile');
    }

    public function saveDetails(Request $request)
    {
        // 1. VALIDATION : On utilise les noms "name" de votre formulaire HTML
        $request->validate([
            'tel'     => 'required|string|max:20',
            'date_naissance' => 'required|date',
            'rue'     => 'required|string',
            'city'    => 'required|string', // C'était 'ville' avant
            'zipcode' => 'required|string', // C'était 'code_postal' avant
            'country' => 'required|string', // C'était 'pays' avant
        ], [
            // Messages d'erreur personnalisés (optionnel)
            'city.required' => 'Le champ ville est obligatoire.',
            'zipcode.required' => 'Le champ code postal est obligatoire.',
            'country.required' => 'Le champ pays est obligatoire.',
        ]);

        $client = Auth::user();

        // 2. CRÉATION ADRESSE
        $adresse = new Adresse();
        
        // À gauche : Vos colonnes BDD (Français)
        // À droite : Les champs du Formulaire (Anglais)
        $adresse->rue         = $request->input('rue');
        $adresse->ville       = $request->input('city');     // Mapping city -> ville
        $adresse->code_postal = $request->input('zipcode');  // Mapping zipcode -> code_postal
        $adresse->pays        = $request->input('country');  // Mapping country -> pays
        
        // Si vous avez d'autres champs obligatoires dans votre table adresse, ajoutez-les ici
        // Ex: $adresse->nom_destinataire = $client->nom_client;
        
        $adresse->save();

        // 3. MISE À JOUR CLIENT
        $client->tel = $request->input('tel');
        $client->date_naissance = $request->input('date_naissance');
        $client->id_adresse_facturation = $adresse->id_adresse; 
        
        $client->save();

        return redirect()->route('home')->with('success', 'Profil complété avec succès !');
    }
}