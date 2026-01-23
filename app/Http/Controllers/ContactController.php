<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'message.min' => 'Votre message est trop court (min 10 caractères).',
        ]);

        return back()->with('success', 'Merci ! Votre message a bien été envoyé. Notre équipe vous répondra sous 24h.');
    }
}