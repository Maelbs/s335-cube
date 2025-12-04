<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('inscription');
    }

    public function showLoginForm()
    {
        return view('connexion');
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'lastname'  => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:client,email_client'],
            'password'  => ['required', 'confirmed', 'min:5'],
            'tel'       => ['required', 'string', 'max:20'],
            'birthday'  => ['required', 'date_format:d/m/Y'],
        ]);
    
        $code = rand(100000, 999999);
    
        $expiresAt = now()->addMinutes(10);
        Cache::put('verification_code_'.$request->email, $code, $expiresAt);
        Cache::put('verification_code_'.$request->email.'_expires', $expiresAt, $expiresAt);
    
        Mail::to($request->email)->send(new VerificationCodeMail($code));
    
        $request->session()->put('reg_data', $request->only([
            'lastname', 'firstname', 'email', 'password', 'tel', 'birthday'
        ]));
    
        return redirect()->route('verification.form')
                         ->with('success', 'Un code de vérification a été envoyé à votre adresse email.');
    }
    
    

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => ['required', 'numeric', 'digits:6'],
        ]);

        $regData = $request->session()->get('reg_data');

        if (!$regData) {
            return redirect()->route('register.form')->withErrors('Les données de votre inscription ont expiré. Veuillez recommencer.');
        }

        $code = Cache::get('verification_code_'.$regData['email']);

        if ($code && $code == $request->verification_code) {

            $client = Client::where('email_client', $regData['email'])->first();

            if (!$client) {
                $client = Client::create([
                    'id_adresse_facturation' => 1,
                    'nom_client'             => $regData['lastname'],
                    'prenom_client'          => $regData['firstname'],
                    'email_client'           => $regData['email'],
                    'mdp'                    => Hash::make($regData['password']),
                    'tel'                    => $regData['tel'],
                    'date_inscription'       => now(),
                    'date_naissance'         => \Carbon\Carbon::createFromFormat('d/m/Y', $regData['birthday'])->format('Y-m-d'),
                ]);
            }

            Auth::login($client);
            $request->session()->regenerate();

            Cache::forget('verification_code_'.$regData['email']);
            Cache::forget('verification_code_'.$regData['email'].'_expires');
            $request->session()->forget('reg_data');

            return redirect()->route('home')->with('success', 'Compte créé avec succès ! Bienvenue !');

        } else {
            return back()->withErrors(['verification_code' => 'Le code est incorrect ou a expiré.']);
        }
    }

    
    

    public function login(Request $request)
    {
        // 1. Validation des champs
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Récupération du client selon ton modèle (email_client)
        $client = Client::where('email_client', $request->email)->first();

        // 3. Vérification du mot de passe hashé
        if ($client && Hash::check($request->password, $client->getAuthPassword())) {
            Auth::login($client);

            // Sécurité session
            $request->session()->regenerate();

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
