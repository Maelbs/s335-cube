<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;

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
        $request->validate([
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'tel' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date', 'before:today'],
        ]);
    
        $client = Auth::user();
    
        $client->update([
            'nom_client' => $request->lastname,
            'prenom_client' => $request->firstname,
            'email_client' => $request->email,
            'tel' => $request->tel,
            'date_naissance' => $request->birthday,
        ]);
    
        return redirect()->route('profil')->with('success', 'Vos informations ont été mises à jour avec succès.');
    }
    
}