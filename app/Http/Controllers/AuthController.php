<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Adresse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class AuthController extends Controller
{
    // Affiche le formulaire d'inscription
    public function showRegisterForm()
    {
        return view('inscription');
    }

    // Affiche le formulaire de facturation
    public function showFacturationForm()
    {
        if (!session('reg_data')) {
            return redirect()->route('register.form');
        }

        return view('facturation');
    }

    // Affiche le formulaire de connexion
    public function showLoginForm()
    {
        return view('connexion');
    }

    // Étape 1 : vérifier l'inscription et rediriger vers facturation
    public function checkInscription(Request $request)
    {
        $request->validate([
            'lastname'  => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'password'  => ['required', 'confirmed', 'min:5'],
            'tel'       => ['required', 'string', 'max:20'],
            'birthday'  => ['required', 'date_format:d/m/Y'],
        ]);

        $client = Client::where('email_client', $request->email)->first();
        if ($client) {
            return back()->withErrors(['email' => 'Cette adresse email est déjà utilisée.']);
        }

        $request->session()->put('reg_data', $request->only([
            'lastname', 'firstname', 'email', 'password', 'tel', 'birthday'
        ]));

        return redirect()->route('facturation.form');
    }

    // Étape 2 : envoyer le code de vérification après facturation
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'address'  => ['required', 'string', 'max:255'],
            'city'     => ['required', 'string', 'max:255'],
            'zipcode'  => ['required', 'string', 'max:10'],
            'country'  => ['nullable', 'string', 'max:50'],
        ]);

        $regData = $request->session()->get('reg_data');
        if (!$regData) {
            return redirect()->route('register.form')->withErrors('Vos données ont expiré, veuillez recommencer.');
        }

        $request->session()->put('reg_billing', $request->only([
            'address', 'city', 'zipcode', 'country'
        ]));

        // Générer le code
        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        Cache::put('verification_code_'.$regData['email'], $code, $expiresAt);

        // Envoyer le mail
        Mail::to($regData['email'])->send(new VerificationCodeMail($code));

        return redirect()->route('verification.form')
                         ->with('success', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    // Étape 3 : vérifier le code et créer l'adresse + client
    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => ['required', 'numeric', 'digits:6'],
        ]);

        $regData = $request->session()->get('reg_data');
        $billing = $request->session()->get('reg_billing');

        if (!$regData || !$billing) {
            return redirect()->route('register.form')->withErrors('Vos données ont expiré. Veuillez recommencer.');
        }

        $code = Cache::get('verification_code_'.$regData['email']);
        if ($code != $request->verification_code) {
            return back()->withErrors(['verification_code' => 'Le code est incorrect ou expiré.']);
        }

        // 1️⃣ Créer l'adresse
        $adresse = Adresse::create([
            'rue'        => $billing['address'],
            'code_postal'=> $billing['zipcode'],
            'ville'      => $billing['city'],
            'pays'       => $billing['country'] ?? 'France',
        ]);

        // 2️⃣ Créer le client
        $client = Client::create([
            'id_adresse_facturation' => $adresse->id_adresse,
            'nom_client'             => $regData['lastname'],
            'prenom_client'          => $regData['firstname'],
            'email_client'           => $regData['email'],
            'mdp'                    => Hash::make($regData['password']),
            'tel'                    => $regData['tel'],
            'date_inscription'       => now(),
            'date_naissance'         => \Carbon\Carbon::createFromFormat('d/m/Y', $regData['birthday'])->format('Y-m-d'),
        ]);

        // Connexion automatique
        Auth::login($client);
        $request->session()->regenerate();

        // Nettoyage
        Cache::forget('verification_code_'.$regData['email']);
        $request->session()->forget(['reg_data', 'reg_billing']);

        return redirect()->route('home')->with('success', 'Votre compte a été créé avec succès !');
    }

    // Connexion
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $client = Client::where('email_client', $request->email)->first();

        if ($client && Hash::check($request->password, $client->mdp)) {
            Auth::login($client);
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Les identifiants ne correspondent pas.'])->onlyInput('email');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
