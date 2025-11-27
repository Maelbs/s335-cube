<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Adresse extends Model
{
    protected $table = 'adresse';
    protected $primaryKey = 'id_adresse';
    public $timestamps = false;

    protected $fillable = [
        'rue',
        'code_postal',
        'ville',
        'pays',
    ];

    public function magasins(): BelongsToMany
    {
        // Table pivot : adresse_magasin | Clés étrangères : id_adresse, id_magasin
        return $this->belongsToMany(MagasinPartenaire::class, 'adresse_magasin', 'id_adresse', 'id_magasin');
    }

    public function clientsLivraison(): BelongsToMany
    {
        // Table pivot : adresse_livraison | Clés étrangères : id_adresse, id_client
        return $this->belongsToMany(Client::class, 'adresse_livraison', 'id_adresse', 'id_client');
    }

    public function commandes(): HasMany
    {
        // Clé étrangère : id_adresse
        return $this->hasMany(Commande::class, 'id_adresse', 'id_adresse');
    }

    public function __toString(): string
    {
        return "{$this->rue}, {$this->code_postal} {$this->ville}";
    }
}