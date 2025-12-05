<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commande extends Model
{
    protected $table = 'commande';
    protected $primaryKey = 'id_commande';
    public $timestamps = false;

    protected $fillable = [
        'id_adresse',
        'id_client',
        'id_type_livraison',
        'id_panier',
        'date_commande',
        'montant_total_commande',
        'cout_livraison',
        'date_livraison',
        'statut_livraison',
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison' => 'date',
        'montant_total_commande' => 'float',
        'cout_livraison' => 'float',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function adresse(): BelongsTo
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }

    public function typeLivraison(): BelongsTo
    {
        return $this->belongsTo(TypeLivraison::class, 'id_type_livraison', 'id_type_livraison');
    }

    public function panier(): BelongsTo
    {
        return $this->belongsTo(Panier::class, 'id_panier', 'id_panier');
    }

    public function retours(): HasMany
    {
        return $this->hasMany(RetourArticle::class, 'id_commande', 'id_commande');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'ligne_commande', 'id_commande', 'reference')
                    ->withPivot(['quantite_article_commande', 'prix_unitaire_article', 'taille_selectionnee']);
    }

    public function __toString(): string
    {
        $date = $this->date_commande ? $this->date_commande->format('d/m/Y') : 'Date inconnue';
        
        $clientInfo = $this->client 
            ? "{$this->client->prenom_client} {$this->client->nom_client}" 
            : "Client #{$this->id_client}";

        $statut = ucfirst($this->statut_livraison ?? 'indéfini');

        return "Commande #{$this->id_commande} du {$date} ({$statut}) - {$clientInfo} - Total : {$this->montant_total_commande} €";
    }
}