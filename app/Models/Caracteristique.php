<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caracteristique extends Model
{
   
    protected $table = 'caracteristique';

    protected $primaryKey = 'id_caracteristique';

    public $timestamps = false;

    protected $fillable = [
        'id_type_caracteristique',
        'nom_caracteristique',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'a_caracteristique', 'id_caracteristique', 'reference')
                    ->withPivot('valeur_caracteritique');
    }
   
    public function typeCaracteristique(): BelongsTo
    {
        return $this->belongsTo(
            TypeCaracteristique::class, 
            'id_type_caracteristique', // Clé étrangère dans cette table
            'id_type_caracteristique'  // Clé locale dans la table parente
        );
    }

    public function __toString(): string
    {
        return $this->nom_caracteristique ?? '';
    }
}