<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Http\Controllers\CommandeController;

class ProfilController extends Controller
{
    public function profil()
    {
        $client = Auth::user();
        return view('profil', compact('client'));
    }

    public function showUpdateForm()
    {
        $client = Auth::user();
        return view('modifierProfil', compact('client'));
    }

    public function update(Request $request)
    {
        $user = Auth::user(); 

        $request->validate([
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:client,email_client,' . $user->getKey() . ',' . $user->getKeyName()],
            'password' => ['nullable', 'confirmed', 'min:5'], 
            'tel' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date', 'before:today'],
        ]);
    
        $user->update([
            'nom_client' => $request->lastname,
            'prenom_client' => $request->firstname,
            'email_client' => $request->email,
            'tel' => $request->tel,
            'date_naissance' => $request->birthday,
            
            // --- AJOUT ICI ---
            // Si la checkbox est cochée, has() renvoie true, sinon false
            'double_auth' => $request->has('double_auth') ? true : false,
        ]);

        if ($request->filled('password')) {
            $user->mdp = Hash::make($request->password); 
            $user->save();
        }
    
        return redirect()->route('profil')->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

// --- US48 : SUPPRESSION DE COMPTE (SECURE) ---
public function destroy(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        try {
            DB::transaction(function () use ($user) {
                
                $aCommandes = $user->commandes()->exists();

                // --- CORRECTION DU BUG SQL ICI ---
                // 1. On identifie les paniers liés à une commande (Intouchables)
                $paniersLies = DB::table('commande')
                                ->where('id_client', $user->id_client)
                                ->pluck('id_panier')
                                ->toArray();

                // 2. On supprime SEULEMENT les paniers qui NE SONT PAS dans cette liste (Paniers abandonnés)
                DB::table('panier')
                    ->where('id_client', $user->id_client)
                    ->whereNotIn('id_panier', $paniersLies)
                    ->delete();
                // ----------------------------------

                $user->velosEnregistres()->delete();
                $user->codesPromoUtilises()->detach();

                if ($aCommandes) {
                    $suffixeUnique = $user->id_client . '_' . time(); 
                    
                    $fakeEmail = 'del_' . $suffixeUnique . '@anonyme.cube';

                    $fakeTel = '0000' . str_pad($user->id_client, 6, '0', STR_PAD_LEFT);
                
                    DB::table('client')
                        ->where('id_client', $user->id_client)
                        ->update([
                            'id_adresse_facturation'   => null,
                            'nom_client'                => 'UTILISATEUR',
                            'prenom_client'             => 'SUPPRIMÉ',
                            'email_client'              => $fakeEmail, 
                            'tel'                       => $fakeTel,
                            'date_naissance'            => '1900-01-01',
                            'mdp'                       => bcrypt(\Illuminate\Support\Str::random(60)),
                            'google_id'                 => null,
                        ]);
                
                    DB::table('adresse_livraison')
                        ->where('id_client', $user->id_client)
                        ->update([
                            'nom_destinataire'    => 'SUPPRIMÉ',
                            'prenom_destinataire' => 'SUPPRIMÉ',
                        ]);
                
                } else {
                    // --- SUPPRESSION TOTALE ---
                    $user->adressesLivraison()->detach();
                    $user->delete();
                }
            });

            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home')->with('success', 'Compte supprimé avec succès.');

        } catch (\Exception $e) {
            // Affiche l'erreur si ça plante encore
            dd("ERREUR : " . $e->getMessage());
        }
    }
}