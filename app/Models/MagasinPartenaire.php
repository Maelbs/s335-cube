<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Adresse;
use App\Models\TypeLivraison;

class MagasinPartenaire extends Model
{
    protected $table = 'magasin_partenaire';
    protected $primaryKey = 'id_magasin';
    public $timestamps = false;

    protected $fillable = [
        'nom_magasin',
    ];

    public function adresses(): BelongsToMany
    {
        // Table pivot : adresse_magasin | Clés étrangères : id_magasin, id_adresse
        return $this->belongsToMany(Adresse::class, 'adresse_magasin', 'id_magasin', 'id_adresse');
    }

    public function typesLivraison(): BelongsToMany
    {
        // Table pivot : type_livraison_magasin | Clés étrangères : id_magasin, id_type_livraison
        return $this->belongsToMany(TypeLivraison::class, 'type_livraison_magasin', 'id_magasin', 'id_type_livraison');
    }

    public function __toString(): string
    {
        return $this->nom_magasin ?? '';
    }
}