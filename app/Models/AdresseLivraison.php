<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdresseLivraison extends Pivot
{
    protected $table = 'adresse_livraison';
    public $timestamps = false; 
    protected $fillable = [
        'id_client',
        'id_adresse',
        'nom_destinataire',
        'prenom_destinataire',
    ];

    protected $foreignKey = 'id_client';
    protected $relatedKey = 'id_adresse';
}