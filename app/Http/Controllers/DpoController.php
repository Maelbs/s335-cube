<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Adresse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DpoController extends Controller
{
    public function index()
    {
        return view('anonymize');
    }

    public function anonymize(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'date_limite' => 'required|date',
        ]);

        $email = $request->input('email');
        $date = $request->input('date_limite');

        
        $client = Client::where('email_client', $email)->first();

        if (!$client) {
            return back()->with('error', 'Aucun client trouvé avec cet email.');
        }

        DB::beginTransaction();

        try {
            
            $ghostClient = Client::firstOrCreate(
                ['email_client' => 'anonyme@deleted.store'],
                [
                    'nom_client' => 'Anonyme',
                    'prenom_client' => 'Supprimé',
                    'mdp' => Hash::make(Str::random(30)),
                    'tel' => '0000000000',
                    'date_inscription' => '1900-01-01',
                    'date_naissance' => '1900-01-01',
                    'role' => 'ghost'
                ]
            );

            
            $ghostAddress = Adresse::firstOrCreate(
                ['rue' => 'Donnée Supprimée', 'code_postal' => '00000'],
                [
                    'ville' => 'Anonymisé',
                    'pays' => 'Nulle part'
                ]
            );

            
            
            $exists = DB::table('adresse_livraison')
                ->where('id_client', $ghostClient->id_client)
                ->where('id_adresse', $ghostAddress->id_adresse)
                ->exists();

            if (!$exists) {
                DB::table('adresse_livraison')->insert([
                    'id_client' => $ghostClient->id_client,
                    'id_adresse' => $ghostAddress->id_adresse,
                    'nom_destinataire' => 'Anonyme',
                    'prenom_destinataire' => 'Anonyme'
                ]);
            }

            
            $commandes = Commande::where('id_client', $client->id_client)
                                 ->where('date_commande', '<=', $date)
                                 ->get();

            $count = 0;

            foreach ($commandes as $commande) {
                
                $commande->id_client = $ghostClient->id_client;
                $commande->id_adresse = $ghostAddress->id_adresse;
                
                $commande->save();
                $count++;
            }

            

            DB::commit();

            return back()->with('success', $count . ' commande(s) ont été transférées au compte Anonyme avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'anonymisation : ' . $e->getMessage());
        }
    }
}