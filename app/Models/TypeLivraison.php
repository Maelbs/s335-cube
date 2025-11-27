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
        // Relation : Un type de livraison peut concerner plusieurs commandes
        // Clé étrangère dans la table commande : id_type_livraison
        return $this->hasMany(Commande::class, 'id_type_livraison', 'id_type_livraison');
    }

    public function magasins(): BelongsToMany
    {
        // Relation : Un type de livraison est disponible dans plusieurs magasins
        // Table pivot : type_livraison_magasin
        return $this->belongsToMany(
            MagasinPartenaire::class, 
            'type_livraison_magasin', // Nom de la table pivot
            'id_type_livraison',      // Clé étrangère du modèle actuel dans la pivot
            'id_magasin'              // Clé étrangère du modèle à lier dans la pivot
        );
    }

    public function __toString(): string
    {
        // Affiche le type (ex: "domicile", "magasin")
        return $this->type_livraison ?? '';
    }
}