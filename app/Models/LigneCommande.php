<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommande extends Pivot
{
    protected $table = 'ligne_commande';
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'reference',   
        'id_commande', 
        'quantite_article_commande',
        'prix_unitaire_article',
        'taille_selectionnee',
    ];

    protected $casts = [
        'prix_unitaire_article' => 'float',
        'quantite_article_commande' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function __toString(): string
    {
        return "Ref :  {$this->reference} , Qte : {$this->quantite_article_commande} , Prix Unitaire :  {$this->prix_unitaire_article} € , Taille : {$this->taille_selectionnee} ";
    }
}