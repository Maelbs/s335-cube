<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB; // Indispensable pour la table pivot
use App\Mail\VerificationCodeMail;
use App\Models\Client;
use App\Models\Adresse;
use App\Models\Panier;
use App\Models\LignePanier;

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

        $request->session()->put('reg_data', $request->only([
            'lastname', 'firstname', 'email', 'password', 'tel', 'birthday'
        ]));

        return redirect()->route('facturation.form');
    }

    public function sendVerificationCode(Request $request)
    {
        // 1. Validation de l'adresse de livraison (toujours requise)
        $rules = [
            'rue' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:50'],
        ];

        // 2. Validation conditionnelle pour l'adresse de facturation
        // Si "use_same_address" N'EST PAS coché, on valide les champs facturation
        if (!$request->has('use_same_address')) {
            $rules['billing_rue'] = ['required', 'string', 'max:255'];
            $rules['billing_city'] = ['required', 'string', 'max:255'];
            $rules['billing_zipcode'] = ['required', 'string', 'max:10'];
            $rules['billing_country'] = ['required', 'string', 'max:50'];
        }

        $request->validate($rules);

        $regData = $request->session()->get('reg_data');
        if (!$regData) {
            return redirect()->route('register.form')->withErrors('Vos données ont expiré, veuillez recommencer.');
        }

        // 3. Préparation des données
        $deliveryData = [
            'rue' => $request->rue,
            'city' => $request->city,
            'zipcode' => $request->zipcode,
            'country' => $request->country
        ];

        if ($request->has('use_same_address')) {
            $billingData = $deliveryData; // Copie
            $sameAddress = true;
        } else {
            $billingData = [
                'rue' => $request->billing_rue,
                'city' => $request->billing_city,
                'zipcode' => $request->billing_zipcode,
                'country' => $request->billing_country
            ];
            $sameAddress = false;
        }

        // Mise en session
        $request->session()->put('reg_delivery', $deliveryData);
        $request->session()->put('reg_billing', $billingData);
        $request->session()->put('reg_same_address', $sameAddress);

        // Envoi Code
        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        Cache::put('verification_code_' . $regData['email'], $code, $expiresAt);

        Mail::to($regData['email'])->send(new VerificationCodeMail($code));

        return redirect()->route('verification.form')
            ->with('success', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => ['required', 'numeric', 'digits:6'],
        ], [
            'verification_code.required' => 'Le code de vérification est obligatoire.',
            'verification_code.numeric' => 'Le code doit être composé uniquement de chiffres.',
            'verification_code.digits' => 'Le code doit contenir exactement 6 chiffres.',
        ]);

        $regData = $request->session()->get('reg_data');
        $deliveryData = $request->session()->get('reg_delivery');
        $billingData = $request->session()->get('reg_billing');
        $sameAddress = $request->session()->get('reg_same_address');

        if (!$regData || !$deliveryData || !$billingData) {
            return redirect()->route('register.form')
                ->withErrors(['email' => 'Vos données ont expiré. Veuillez recommencer.']);
        }

        $cachedCode = Cache::get('verification_code_' . $regData['email']);

        if (!$cachedCode || $cachedCode != $request->verification_code) {
            return back()->withInput()->withErrors(['verification_code' => 'Le code est incorrect ou a expiré.']);
        }

        // --- TRANSACTION DATABASE ---
        DB::beginTransaction();
        try {
            // 1. Créer adresse Livraison
            $adresseLivraison = Adresse::create([
                'rue' => $deliveryData['rue'],
                'code_postal' => $deliveryData['zipcode'],
                'ville' => $deliveryData['city'],
                'pays' => $deliveryData['country'],
            ]);

            // 2. Gérer adresse Facturation
            if ($sameAddress) {
                $idAdresseFacturation = $adresseLivraison->id_adresse;
            } else {
                $adresseFacturation = Adresse::create([
                    'rue' => $billingData['rue'],
                    'code_postal' => $billingData['zipcode'],
                    'ville' => $billingData['city'],
                    'pays' => $billingData['country'],
                ]);
                $idAdresseFacturation = $adresseFacturation->id_adresse;
            }

            // 3. Créer Client (lié à facturation)
            $client = Client::create([
                'id_adresse_facturation' => $idAdresseFacturation,
                'nom_client' => $regData['lastname'],
                'prenom_client' => $regData['firstname'],
                'email_client' => $regData['email'],
                'mdp' => Hash::make($regData['password']),
                'tel' => $regData['tel'],
                'date_inscription' => now(),
                'date_naissance' => $regData['birthday'],
            ]);

            // 4. Créer liaison Livraison (Pivot)
            DB::table('adresse_livraison')->insert([
                'id_client' => $client->id_client,
                'id_adresse' => $adresseLivraison->id_adresse,
            ]);

            DB::commit();

            Auth::login($client);
            $request->session()->regenerate();
            Cache::forget('verification_code_' . $regData['email']);
            $request->session()->forget(['reg_data', 'reg_delivery', 'reg_billing', 'reg_same_address']);

            return redirect()->route('home')->with('success', 'Votre compte a été créé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la création du compte. ' . $e->getMessage()]);
        }
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
                ? $item['taille'] : 'Non renseigné';

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