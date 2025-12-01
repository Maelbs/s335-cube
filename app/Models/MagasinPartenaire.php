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
        return $this->belongsToMany(Adresse::class, 'adresse_magasin', 'id_magasin', 'id_adresse');
    }

    public function typesLivraison(): BelongsToMany
    {
        return $this->belongsToMany(TypeLivraison::class, 'type_livraison_magasin', 'id_magasin', 'id_type_livraison');
    }

    public function stocksVelo()
    {
        return $this->belongsToMany(
            VarianteVeloInventaire::class, 
            'inventaire_magasin',          
            'id_magasin',                  
            'id_velo_inventaire'           
        )->withPivot('quantite_stock_magasin'); 
    }

    public function __toString(): string
    {
        return (string) $this->nom_magasin;
    }
}