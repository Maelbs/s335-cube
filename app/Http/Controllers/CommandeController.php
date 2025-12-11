<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function downloadInvoice($id)
    {
        $client = Auth::user();

        // On récupère la commande avec toutes les infos (y compris l'adresse de facturation)
        $commande = $client->commandes()
            ->with(['articles', 'adresse', 'client.adresseFacturation'])
            ->where('id_commande', $id)
            ->firstOrFail();

        // On charge la vue 'client.commandes.invoice' avec les données
        $pdf = Pdf::loadView('invoice', compact('commande'));

        // On télécharge le fichier avec un nom propre
        return $pdf->download('facture-cube-' . $commande->id_commande . '.pdf');
    }
}