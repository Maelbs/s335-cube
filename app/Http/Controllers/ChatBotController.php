<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Illuminate\Support\Facades\Log;
use App\Models\CategorieVelo;
use App\Models\Couleur;
use App\Models\CategorieAccessoire;
use App\Models\Article;
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
            'gemini-2.0-flash-lite',
            'gemini-2.5-flash',  
            'gemini-2.0-flash',  
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
                'poids' => $v->poids,
                'url' => "/velo/" . $v->reference,
                'type' => $v->batterie ? 'Électrique' : 'Musculaire',
                'cadre' => optional($v->modele)->materiau_cadre ?? 'N/A',
                'categorie' => optional($v->modele)->categorie->nom_categorie ?? 'N/A',
                'millesime' => optional($v->modele)->millesime_modele ?? 'N/A',
                'couleur' => $v->couleur ? [
                    'nom' => $v->couleur->nom_couleur,
                    'hexa' => $v->couleur->hexa_couleur,
                ] : null
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
 
            $webRoutesPath = base_path('routes/web.php');
 
            $webRoutesContent = '';
 
            if (File::exists($webRoutesPath)) {
                $webRoutesContent = trim(File::get($webRoutesPath));
            }
 
            $viewsPath = resource_path('views');
 
            $bladeFiles = File::allFiles($viewsPath);
 
            $bladesContent = [];
 
            foreach ($bladeFiles as $file) {
                if ($file->getExtension() === 'php') {
                    $relativePath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
 
                    $bladesContent[] = [
                        'fichier' => $relativePath,
                        'contenu' => trim(file_get_contents($file->getPathname()))
                    ];
                }
            }
 
            $cleanBlades = [];
 
            foreach ($bladesContent as $blade) {
                $cleanBlades[] = [
                    'fichier' => $blade['fichier'],
                    'contenu' => $this->cleanBlade($blade['contenu'])
                ];
            }
 
            $viewsText = "";
 
            foreach ($cleanBlades as $blade) {
                $viewsText .= "\nFICHIER: {$blade['fichier']}\n";
                $viewsText .= "{$blade['contenu']}\n";
            }
 
            $systemPrompt = "
            Tu es l'expert CUBE.
 
            ROUTES DU SITE (web.php) :
            $webRoutesContent
           
            VUES DU SITE :
            $viewsText
           
            ESPACE CLIENT :
            " . ($clientData ? json_encode($clientData) : "Aucun utilisateur connecté") . "
 
            CONSIGNE STRICTE DE FORMATAGE :
            - Pour CHAQUE produit mentionné, tu DOIS TOUJOURS afficher DEUX éléments l'un après l'autre :
              1. Le lien vers la page : <a href='URL' class='chat-product-link'>👉 Voir le produit</a>
              2. Le bouton panier : <button onclick=\"addToCartFromBot('REFERENCE', 1)\" class='chat-product-panier'>🛒 Ajouter au panier</button>
              (Remplace REFERENCE par la référence exacte de l'article provenant des données).
 
            - Dès que l’utilisateur parle de panier, suppression ou total : <a href='/panier' class='chat-product-link'>🛒 Voir le panier</a>
            - Dès que l’utilisateur parle de commande, paiement ou suivi : <a href='/commandes' class='chat-product-link'>📦 Voir mes commandes</a>
 
            LISTE DES MAGASINS PARTENAIRES : " . json_encode($magasins) . "
 
            DONNÉES :
            - CATÉGORIES : " . json_encode($categories) . "
            - CATALOGUE VÉLOS : " . json_encode($velos) . "
            - ACCESSOIRES : " . json_encode($accessoires) . "
 
            RÈGLES :
            - INTERDICTION TOTALE d’utiliser Markdown (étoiles, gras, etc.). Réponds en texte brut + HTML.
            - Salue l'utilisateur par son prénom s'il est connecté.
            - Si un utilisateur veut ajouter au panier, utilise IMPÉRATIVEMENT la fonction JS addToCartFromBot.
            - Si la couleur est null, indique 'Couleur non précisée'.
            - Si l'information (SAV, retours, etc.) n'est pas dans les données, renvoie vers le bouton « Aide ».
            - Réponds en 2-3 phrases maximum.
        ";
 
            foreach ($models as $modelName) {
                try {
                    Log::info("Tentative de chat avec : " . $modelName);
                    Log::info('TAILLE PROMPT', ['chars' => strlen($systemPrompt)]);
                   
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
   
    function cleanBlade($content)
    {
        $content = preg_replace('/@\w+(\(.*?\))?/', '', $content);
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        return trim($content);
    }
 
    public function addToCartFromBot(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Veuillez vous connecter pour ajouter au panier.'], 401);
        }
   
        $ref = $request->input('ref');
        $qty = (int) $request->input('qty', 1);
   
        $article = Article::where('reference', $ref)->first();
        if (!$article) {
            return response()->json(['error' => 'Produit introuvable'], 404);
        }
   
        $panier = $user->paniers()->whereDoesntHave('commande')->latest('id_panier')->first();
        if (!$panier) {
            $panier = Panier::create([
                'id_client' => $user->id_client,
                'date_creation' => now(),
                'montant_total_panier' => 0,
            ]);
        }
 
        $ligne = $panier->articles()->where('ligne_panier.reference', $ref)->first();
   
        if ($ligne) {
            $newQty = $ligne->pivot->quantite_article + $qty;
            $panier->articles()->updateExistingPivot($ref, ['quantite_article' => $newQty]);
        } else {
            $panier->articles()->attach($ref, [
                'quantite_article' => $qty,
                'taille_selectionnee' => 'Unique'
            ]);
        }
   
        return response()->json([
            'success' => true,
            'message' => "L'article " . $article->nom_article . " a été ajouté à votre panier !"
        ]);
    }
}