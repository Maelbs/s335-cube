<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\AdresseLivraison;

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
        return $this->belongsToMany(MagasinPartenaire::class, 'adresse_magasin', 'id_adresse', 'id_magasin');
    }

    public function clientsLivraison()
    {
        return $this->belongsToMany(Client::class, 'adresse_livraison', 'id_adresse', 'id_client')
            ->using(AdresseLivraison::class)
            ->withPivot('nom_destinataire', 'prenom_destinataire');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'id_adresse', 'id_adresse');
    }

    public function __toString(): string
    {
        return "{$this->rue}, {$this->code_postal} {$this->ville}";
    }
}