<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;

    protected $table = 'panier';
    protected $primaryKey = 'id_panier';
    public $timestamps = false;

    protected $fillable = [
        'id_client',
        'date_creation',
        'code_promo',
        'montant_total_panier',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'montant_total_panier' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function lignesPanier()
    {
        return $this->hasMany(LignePanier::class, 'id_panier', 'id_panier');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'ligne_panier', 'id_panier', 'reference')
                    ->withPivot(['quantite_article', 'taille_selectionnee'])
                    ->using(LignePanier::class);
    }

    public function commande()
    {
        return $this->hasOne(Commande::class, 'id_panier', 'id_panier');
    }

    public function __toString()
    {
        return sprintf(
            "Panier [%d] - Client ID: %d | Total: %s € | Promo: %s",
            $this->id_panier,
            $this->id_client,
            $this->montant_total_panier,
            $this->code_promo ?? 'Aucun'
        );
    }

    public function codePromo()
    {
        return $this->belongsTo(CodePromo::class, 'code_promo', 'id_codepromo');
    }
}