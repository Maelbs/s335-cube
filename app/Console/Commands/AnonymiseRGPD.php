<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnonymizeGdpr extends Command
{
    protected $signature = 'rgpd:anonymize';
    protected $description = 'Anonymise les clients inactifs (3 ans) en réaffectant les commandes vers le compte 999999.';

    // Identifiants FIXES (ceux insérés par le SQL ci-dessus)
    const ID_ANONYME_CLIENT = 999999;
    const ID_ANONYME_ADRESSE = 999999;

    public function handle()
    {
        $this->info("=== DÉBUT DU TRAITEMENT RGPD ===");
    
        // Définition des dates limites
        $dateLimiteCompta = Carbon::now()->subYears(10); // Pour les factures
        $dateLimiteInactivite = Carbon::now()->subYears(3); // Pour les utilisateurs
    
        // -----------------------------------------------------------------
        // ÉTAPE 1 : GESTION DES VIEILLES COMMANDES (> 10 ANS)
        // -----------------------------------------------------------------
        // On peut totalement détacher ces commandes du client d'origine
        // car l'obligation légale de conservation est passée.
        $this->info("1. Traitement des commandes de plus de 10 ans...");
        
        $nbCommandesVieux = DB::table('commande')
            ->where('date_commande', '<', $dateLimiteCompta)
            ->where('id_client', '!=', self::ID_ANONYME_CLIENT) // Pas déjà anonymisées
            ->update([
                'id_client'  => self::ID_ANONYME_CLIENT,
                'id_adresse' => self::ID_ANONYME_ADRESSE
            ]);
    
        $this->info("   -> $nbCommandesVieux commandes > 10 ans transférées vers le compte Anonyme.");
    
    
        // -----------------------------------------------------------------
        // ÉTAPE 2 : IDENTIFICATION DES CLIENTS INACTIFS (> 3 ANS)
        // -----------------------------------------------------------------
        $this->info("2. Recherche des clients inactifs depuis 3 ans...");
    
        // On sélectionne les clients inactifs
        // On doit savoir s'ils ont des commandes RÉCENTES (< 10 ans)
        $clientsInactifs = DB::table('client')
            ->leftJoin('commande', 'client.id_client', '=', 'commande.id_client')
            ->select('client.id_client', 'client.email_client', DB::raw('MAX(commande.date_commande) as last_order_date'))
            ->where('client.id_client', '!=', self::ID_ANONYME_CLIENT)
            ->groupBy('client.id_client', 'client.email_client', 'client.date_inscription')
            ->havingRaw('COALESCE(MAX(commande.date_commande), client.date_inscription) < ?', [$dateLimiteInactivite])
            ->get();
    
        $bar = $this->output->createProgressBar($clientsInactifs->count());
    
        foreach ($clientsInactifs as $client) {
            
            // Est-ce qu'il a des commandes qu'on doit garder légalement ?
            // (C'est-à-dire une commande passée APRÈS la date limite de 2016 par exemple)
            $aDesCommandesRecentes = $client->last_order_date && ($client->last_order_date > $dateLimiteCompta->toDateString());
    
            DB::transaction(function () use ($client, $aDesCommandesRecentes) {
                
                // CAS A : IL A DES FACTURES RÉCENTES (< 10 ANS)
                // -> On ne peut PAS supprimer son Nom/Adresse.
                // -> On ne fait que "désactiver" le compte (Anonymisation "Light").
                if ($aDesCommandesRecentes) {
                    
                    // On vérifie si ce n'est pas déjà un compte archivé (évite de refaire le travail tous les jours)
                    if (!str_starts_with($client->email_client, 'archive_legal_')) {
                        DB::table('client')
                            ->where('id_client', $client->id_client)
                            ->update([
                                // On garde Nom/Prénom pour la facture
                                'mdp'            => 'DELETED', // Plus de connexion
                                'tel'            => null,
                                'date_naissance' => null,
                                'google_id'      => null,
                                'double_auth'    => false,
                                // On marque l'email pour dire "C'est gardé pour la compta"
                                'email_client'   => 'archive_legal_' . $client->id_client . '@local',
                            ]);
                        
                        // On supprime quand même les infos non vitales (paniers, retours vieux...)
                        DB::table('panier')->where('id_client', $client->id_client)->delete();
                    }
    
                } 
                // CAS B : IL N'A QUE DES VIEILLES FACTURES (OU AUCUNE)
                // -> On peut TOUT supprimer (Anonymisation Totale).
                else {
                    
                    // 1. On déplace ses (éventuelles) très vieilles commandes restantes vers Anonyme
                    // (Normalement déjà fait à l'étape 1, mais sécurité supplémentaire)
                    DB::table('commande')
                        ->where('id_client', $client->id_client)
                        ->update([
                            'id_client'  => self::ID_ANONYME_CLIENT,
                            'id_adresse' => self::ID_ANONYME_ADRESSE
                        ]);
    
                    // 2. Nettoyage des données liées
                    DB::table('panier')->where('id_client', $client->id_client)->delete();
                    
                    // Suppression liaison adresse_livraison
                    DB::table('adresse_livraison')->where('id_client', $client->id_client)->delete();
                    
                    // Récupération des adresses à supprimer
                    // (Attention de ne pas supprimer si utilisée ailleurs, mais avec la logique ci-dessus c'est bon)
                    $idFactu = DB::table('client')->where('id_client', $client->id_client)->value('id_adresse_facturation');
                    
                    // 3. Suppression du Client
                    DB::table('client')->where('id_client', $client->id_client)->delete();
    
                    // 4. Suppression de l'adresse orpheline
                    if ($idFactu && $idFactu != self::ID_ANONYME_ADRESSE) {
                         DB::table('adresse')->where('id_adresse', $idFactu)->delete();
                    }
                }
            });
    
            $bar->advance();
        }
    
        $bar->finish();
        $this->newLine();
        $this->info("=== TRAITEMENT TERMINÉ AVEC SUCCÈS ===");
    }
}