<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\VerificationCodeMail;
use App\Models\Client;
use App\Models\Adresse;
use App\Models\Panier;
use App\Models\LignePanier;
use App\Models\Taille;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('inscription');
    }

    public function showFacturationForm()
    {
        if (!session('reg_data')) {
            return redirect()->route('register.form');
        }

        $regData = session('reg_data');
        return view('facturation', compact('regData'));
    }

    public function showLoginForm()
    {
        return view('connexion');
    }

    public function checkInscription(Request $request)
    {
        $request->validate([
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', 'min:5'],
            'tel' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date', 'before:today'],
        ]);

        $client = Client::where('email_client', $request->email)->first();

        if ($client) {
            return back()
                ->withErrors(['email' => 'Cette adresse email est déjà utilisée.'])
                ->withInput();
        }

        // 3. Sauvegarde en session
        $request->session()->put('reg_data', $request->only([
            'lastname',
            'firstname',
            'email',
            'password',
            'tel',
            'birthday'
        ]));

        return redirect()->route('facturation.form');
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'rue' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:50'],
        ]);

        $regData = $request->session()->get('reg_data');
        if (!$regData) {
            return redirect()->route('register.form')->withErrors('Vos données ont expiré, veuillez recommencer.');
        }

        $request->session()->put('reg_billing', $request->only([
            'rue',
            'city',
            'zipcode',
            'country'
        ]));

        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        Cache::put('verification_code_' . $regData['email'], $code, $expiresAt);

        Mail::to($regData['email'])->send(new VerificationCodeMail($code));

        return redirect()->route('verification.form')
            ->with('success', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    public function verifyCode(Request $request)
    {
        // 1. Validation avec messages en Français
        $request->validate([
            'verification_code' => ['required', 'numeric', 'digits:6'],
        ], [
            'verification_code.required' => 'Le code de vérification est obligatoire.',
            'verification_code.numeric' => 'Le code doit être composé uniquement de chiffres.',
            'verification_code.digits' => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        $regData = $request->session()->get('reg_data');
        $billing = $request->session()->get('reg_billing');

        if (!$regData || !$billing) {
            return redirect()->route('register.form')
                ->withErrors(['email' => 'Vos données ont expiré. Veuillez recommencer.']);
        }

        $cachedCode = Cache::get('verification_code_' . $regData['email']);

        if (!$cachedCode || $cachedCode != $request->verification_code) {
            return back()
                ->withInput()
                ->withErrors(['verification_code' => 'Le code est incorrect ou a expiré.']);
        }

        $adresse = Adresse::create([
            'rue' => $billing['rue'],
            'code_postal' => $billing['zipcode'],
            'ville' => $billing['city'],
            'pays' => $billing['country'],
        ]);

        $client = Client::create([
            'id_adresse_facturation' => $adresse->id_adresse,
            'nom_client' => $regData['lastname'],
            'prenom_client' => $regData['firstname'],
            'email_client' => $regData['email'],
            'mdp' => Hash::make($regData['password']),
            'tel' => $regData['tel'],
            'date_inscription' => now(),
            'date_naissance' => $regData['birthday'],
        ]);

        Auth::login($client);
        $request->session()->regenerate();

        Cache::forget('verification_code_' . $regData['email']);
        $request->session()->forget(['reg_data', 'reg_billing']);

        return redirect()->route('home')->with('success', 'Votre compte a été créé avec succès !');
    }

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
            $this->fusionnerPanier($client->id_client);
            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    private function fusionnerPanier($clientId)
    {
        $sessionCart = Session::get('cart', []);

        if (empty($sessionCart)) {
            return;
        }

        $panier = Panier::firstOrCreate(
            ['id_client' => $clientId],
            ['date_creation' => now(), 'montant_total_panier' => 0,]
        );

        foreach ($sessionCart as $item) {
            $ref = $item['reference'];
            $qteSession = $item['quantity'];

            $taille = (isset($item['taille']) && $item['taille'] !== 'Unique' && $item['taille'] !== '')
                ? $item['taille']
                : 'Non renseigné';

            $ligneExistante = LignePanier::where('id_panier', $panier->id_panier)
                ->where('reference', $ref)
                ->where('taille_selectionnee', $taille)
                ->first();

            if ($ligneExistante) {
                $ligneExistante->quantite_article += $qteSession;
                $ligneExistante->save();
            } else {
                LignePanier::create([
                    'id_panier' => $panier->id_panier,
                    'reference' => $ref,
                    'quantite_article' => $qteSession,
                    'taille_selectionnee' => $taille
                ]);
            }
        }

        Session::forget('cart');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}