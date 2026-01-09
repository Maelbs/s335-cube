<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content; 
use Illuminate\Support\Facades\Log;
use App\Models\CategorieVelo;
use App\Models\CategorieAccessoire;
use Illuminate\Support\Facades\Auth;
use App\Models\Accessoire;
use App\Models\VarianteVelo;
use App\Models\MagasinPartenaire;

class ChatBotController extends Controller
{
    public function ask(Request $request)
    {
        $userMessage = $request->input('message');
        if (!$userMessage) return response()->json(['reply' => "Besoin d'aide ?"], 200);

        $models = [
            'gemini-2.5-flash',   
            'gemini-2.0-flash',  
            'gemini-2.0-flash-lite',  
            'gemini-3-flash-preview', 
            'gemini-1.5-flash-latest' 
        ];

        try {
            $clientData = null;
            $user = Auth::user(); 

            if ($user) {
                $clientData = [
                    'nom' => $user->nom_client,
                    'prenom' => $user->prenom_client,
                    'email' => $user->email_client,
                    'panier_actuel' => $user->paniers()->whereDoesntHave('commande')->with('articles')->latest('id_panier')->get()->map(fn($p) => [
                        'total' => $p->montant_total_panier . '€',
                        'articles' => $p->articles->map(fn($a) => $a->nom_article . ' (Qté: ' . $a->pivot->quantite_article . ')')
                    ])->first(),
                    'dernieres_commandes' => $user->commandes()->latest('date_commande')->limit(3)->get()->map(fn($c) => [
                        'id' => $c->id_commande,
                        'date' => $c->date_commande->format('d/m/Y'),
                        'total' => $c->montant_total_commande . '€',
                        'statut' => $c->statut_livraison
                    ])
                ];
            }

            $magasins = MagasinPartenaire::with('adresses')->get()->map(fn($m) => [
                'nom' => $m->nom_magasin,
                'villes' => $m->adresses->pluck('ville')->implode(', ')
            ]);

            $categories = [
                'velos' => CategorieVelo::all(['id_categorie', 'nom_categorie'])->toArray(),
                'accessoires' => CategorieAccessoire::all(['id_categorie_accessoire', 'nom_categorie_accessoire'])->toArray()
            ];

            $velos = VarianteVelo::with(['modele.categorie', 'couleur', 'batterie'])
            ->get()
            ->map(fn($v) => [
                'ref' => $v->reference,
                'nom' => $v->nom_article,
                'prix' => $v->prix . '€',
                'url' => "/velo/" . $v->reference,
                'type' => $v->batterie ? 'Électrique' : 'Musculaire',
                'cadre' => optional($v->modele)->materiau_cadre ?? 'N/A',
                'categorie' => optional($v->modele)->categorie->nom_categorie ?? 'N/A',
                'millesime' => optional($v->modele)->millesime_modele ?? 'N/A'
            ]);

            $accessoires = Accessoire::with('categorie')
            ->get()
            ->map(fn($a) => [
                'ref' => $a->reference,
                'nom' => $a->nom_article,
                'prix' => $a->prix . '€',
                'url' => "/accessoire/" . $a->reference,
                'categorie' => $a->categorie->nom_categorie_accessoire ?? 'N/A'
            ]);

            $systemPrompt = "
                Tu es l'expert CUBE. 

                INFORMATIONS DU SITE (À UTILISER UNIQUEMENT SI PERTINENT) :
                1. MAGASINS : Un bouton « Choisir mon magasin » est disponible en haut à droite du site pour localiser les revendeurs. Après clic, l’utilisateur peut choisir un magasin soit depuis une liste verticale de magasins, soit via une carte interactive.
                2. TAILLE DE CADRE : TAILLE DE CADRE : Chaque page produit vélo (hors accessoires) dispose d’un outil de calcul de taille (« Calculateur de taille ») situé en bas de la page, sous les caractéristiques du vélo.

                ESPACE CLIENT (CONFIDENTIEL) :
                Utilisateur connecté : " . ($clientData ? json_encode($clientData) : "Aucun utilisateur connecté") . "

                CONSIGNE STRICTE : 
                
                - Pour CHAQUE produit mentionné, tu DOIS afficher un bouton de lien HTML : <a href='URL' class='chat-product-link'>👉 Voir le produit</a>

                - Dès que l’utilisateur parle de panier, ajout, suppression ou total, tu DOIS afficher un bouton de lien HTML : <a href='/panier' class='chat-product-link'>🛒 Voir le panier</a>

                - Dès que l’utilisateur parle de commande, paiement, suivi ou historique, tu DOIS afficher un bouton de lien HTML : <a href='/commandes' class='chat-product-link'>📦 Voir mes commandes</a>

                LISTE DES MAGASINS PARTENAIRES : " . json_encode($magasins) . "

                DONNÉES :
                - CATÉGORIES : " . json_encode($categories) . "
                - CATALOGUE VÉLOS : " . json_encode($velos) . "
                - ACCESSOIRES : " . json_encode($accessoires) . "

                RÈGLES :
                - INTERDICTION TOTALE d’utiliser des étoiles (* ou **) ou tout autre format Markdown.
                - Si l'utilisateur demande 'où en est ma commande', utilise les données 'dernieres_commandes'.
                - Si l'utilisateur demande 'qu'est-ce que j'ai dans mon panier', utilise 'panier_actuel'.
                - Salue l'utilisateur par son prénom s'il est connecté.
                - INTERDICTION de divulguer des mots de passe ou IDs techniques.
                - Réponds uniquement en texte simple + HTML autorisé pour les liens (<a>).
                - Ne jamais inventer d’information liée au fonctionnement du site.
                - Si une question est pertinente pour un site e-commerce (paiement, livraison, garanties, SAV, retours, compte client, etc.) mais que la réponse n’est pas disponible dans les données, indiquer que le bouton « Aide » en haut du site permet d’obtenir l’information.
                - Ne pas rediriger vers « Aide » pour des questions hors sujet ou non pertinentes.
                - Pas de blabla inutile sur 'l'agilité' ou 'le confort' sauf si demandé.
                - Donne le Nom, le Prix et le LIEN immédiatement.
                - Si tu ne trouves pas la référence exacte dans les données, ne l'invente pas.
                - Si l'utilisateur cherche un vélo, propose un accessoire compatible avec son lien.
                - Réponds en 2-3 phrases maximum.
            ";

            foreach ($models as $modelName) {
                try {
                    Log::info("Tentative de chat avec : " . $modelName);
                    
                    $result = Gemini::generativeModel(model: $modelName)
                        ->withSystemInstruction(Content::parse($systemPrompt))
                        ->generateContent($userMessage);
                
                    return response()->json(['reply' => $result->text(), 'model_info' => $modelName]);
                } 
                catch (\Exception $e) {
                    Log::warning("Échec du modèle $modelName : " . $e->getMessage());
                    continue; 
                }
            }
            return response()->json(['reply' => "Désolé, tous nos experts sont occupés. Réessayez dans 1 minute !"], 200);
        } 
        catch (\Exception $e) {
            Log::error("Erreur Chatbot : " . $e->getMessage());
            return response()->json(['reply' => "Erreur technique. Réessayez."], 200);
        }
    }
}