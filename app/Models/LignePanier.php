<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot; 
use Illuminate\Database\Eloquent\Builder;

class LignePanier extends Pivot
{
    use HasFactory;

    protected $table = 'ligne_panier';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    public function getKeyName()
    {
        return null;
    }

    protected $fillable = [
        'id_panier',
        'reference',
        'quantite_article',
        'taille_selectionnee',
    ];

    protected $casts = [
        'quantite_article' => 'integer',
    ];

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('id_panier', $this->getAttribute('id_panier'))
            ->where('reference', $this->getAttribute('reference'))
            ->where('taille_selectionnee', $this->getAttribute('taille_selectionnee'));
        return $query;
    }

    public function panier()
    {
        return $this->belongsTo(Panier::class, 'id_panier', 'id_panier');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function __toString()
    {
        return sprintf(
            "Ligne Panier [Panier: %d | Ref: %s] : Qte: %d, Taille: %s",
            $this->id_panier,
            $this->reference,
            $this->quantite_article,
            $this->taille_selectionnee
        );
    }
}