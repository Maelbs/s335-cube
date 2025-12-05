<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeCaracteristique extends Model
{
    protected $table = 'type_caracteristique';
    protected $primaryKey = 'id_type_caracteristique';
    public $timestamps = false;

    protected $fillable = [
        'nom_type_caracteristique',
    ];

     
    public function caracteristiques(): HasMany
    {
        return $this->hasMany(
            Caracteristique::class, 
            'id_type_caracteristique',
            'id_type_caracteristique'  
        );
    }

    public function __toString(): string
    {
        return $this->nom_type_caracteristique ?? '';
    }
}