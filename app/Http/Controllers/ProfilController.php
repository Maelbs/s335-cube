<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 
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
        ]);

        
    
        if ($request->filled('password')) {
            $user->mdp = Hash::make($request->password); 
            $user->save();
        }
    
        return redirect()->route('profil')->with('success', 'Vos informations ont été mises à jour avec succès.');
    }
}