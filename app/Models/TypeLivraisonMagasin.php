<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TypeLivraisonMagasin extends Pivot
{
    protected $table = 'type_livraison_magasin';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_type_livraison', // Clé étrangère
        'id_magasin', // Clé étrangère
    ];
}