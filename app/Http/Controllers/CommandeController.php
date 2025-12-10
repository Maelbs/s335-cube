<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    public function index()
    {
        $client = Auth::user();
        
        $commandes = $client->commandes()
                            ->orderBy('date_commande', 'desc')
                            ->get();
    
            return view('listCommandes', compact('commandes'));;
    }

    public function show($id)
    {
        $client = Auth::user();
    
        $commande = $client->commandes()
            ->with(['articles.photos', 'adresse', 'client.adresseFacturation']) 
            ->where('id_commande', $id)
            ->firstOrFail();
    
        return view('vizualizeCommande', compact('commande'));
    }
}