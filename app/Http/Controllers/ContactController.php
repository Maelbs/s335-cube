<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    // Affiche le formulaire
    public function show()
    {
        return view('contact');
    }

    // Traite le formulaire
    public function submit(Request $request)
    {
        // 1. Validation des champs
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'message.min' => 'Votre message est trop court (min 10 caractères).',
        ]);

        // 2. Logique d'envoi d'email (Simulée pour l'instant)
        // Pour faire fonctionner l'envoi réel, il faudra configurer le fichier .env
        // Mail::to('admin@cube.fr')->send(new \App\Mail\ContactMail($request->all()));

        // 3. Retour avec message de succès
        return back()->with('success', 'Merci ! Votre message a bien été envoyé. Notre équipe vous répondra sous 24h.');
    }
}