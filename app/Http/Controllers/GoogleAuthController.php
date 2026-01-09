<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        $port = request()->getPort();
        $redirectUrl = "http://51.83.36.122.nip.io:{$port}/auth/google/callback";

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/user.birthday.read'
            ])
            ->with(['prompt' => 'consent select_account'])
            ->redirect();
    }
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $dateNaissance = null;

            try {
                $response = Http::withToken($googleUser->token)
                    ->get('https://people.googleapis.com/v1/people/me?personFields=birthdays');

                if ($response->successful()) {
                    $birthdays = $response->json()['birthdays'] ?? [];

                    foreach ($birthdays as $bday) {
                        if (isset($bday['date']) && isset($bday['date']['year']) && isset($bday['date']['month']) && isset($bday['date']['day'])) {
                            $dateNaissance = sprintf(
                                '%04d-%02d-%02d',
                                $bday['date']['year'],
                                $bday['date']['month'],
                                $bday['date']['day']
                            );
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
            }

            $client = Client::where('google_id', $googleUser->getId())
                ->orWhere('email_client', $googleUser->getEmail())
                ->first();

            if (!$client) {
                $client = new Client();
                $client->nom_client = $googleUser->user['family_name'] ?? $googleUser->getName();
                $client->prenom_client = $googleUser->user['given_name'] ?? '';
                $client->email_client = $googleUser->getEmail();
                $client->google_id = $googleUser->getId();

                if ($dateNaissance) {
                    $client->date_naissance = $dateNaissance;
                }

                $client->mdp = null;
                $client->role = 'client';
                $client->date_inscription = Carbon::now();
                $client->save();
            }

            Auth::login($client);

            if (empty($client->tel) || empty($client->id_adresse_facturation) || empty($client->date_naissance)) {
                return redirect()->route('client.complete_profile');
            }

            return redirect()->route('home');

        } catch (\Exception $e) {
            //return redirect()->route('login')->with('error', 'Erreur Google : ' . $e->getMessage());
            //dd($e->getMessage());
        }
    }
}