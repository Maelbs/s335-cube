<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LignePanier extends Model
{
    use HasFactory;

    protected $table = 'ligne_panier';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_panier',
        'reference',
        'quantite_article',
        'taille_selectionnee',
    ];

    protected $casts = [
        'quantite_article' => 'integer',
        'taille_selectionnee' => 'string',
    ];

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