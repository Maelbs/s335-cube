<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ACaracteristique extends Model
{
    use HasFactory;

    protected $table = 'a_caracteristique';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_caracteristique',
        'reference',
        'valeur_caracteritique', 
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function caracteristique()
    {
        return $this->belongsTo(Caracteristique::class, 'id_caracteristique', 'id_caracteristique');
    }

    public function __toString()
    {
        return sprintf(
            "Caractéristique Article [Ref: %s] : %s (Valeur: %s)",
            $this->reference,
            $this->id_caracteristique,
            $this->valeur_caracteritique
        );
    }
}