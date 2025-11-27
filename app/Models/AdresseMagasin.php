<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdresseMagasin extends Pivot
{
    protected $table = 'adresse_magasin';
    public $timestamps = false;
    
    // Indique que la clé primaire n'est pas auto-incrémentée (c'est une clé composée)
    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'id_adresse', // Clé étrangère
        'id_magasin', // Clé étrangère
    ];

    public function __toString(): string
    {
        return "{$this->id_adresse}, {$this->id_magasin}";
    }
}