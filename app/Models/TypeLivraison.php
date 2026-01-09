<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TypeLivraison extends Model
{
    protected $table = 'type_livraison';
    protected $primaryKey = 'id_type_livraison';
    public $timestamps = false;

    protected $fillable = [
        'type_livraison',
    ];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'id_type_livraison', 'id_type_livraison');
    }

    public function magasins(): BelongsToMany
    {
        return $this->belongsToMany(
            MagasinPartenaire::class, 
            'type_livraison_magasin', 
            'id_type_livraison',      
            'id_magasin'              
        );
    }

    public function __toString(): string
    {
        return $this->type_livraison ?? '';
    }
}