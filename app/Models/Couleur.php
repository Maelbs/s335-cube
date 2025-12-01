<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couleur extends Model
{
    use HasFactory;

    protected $table = 'couleur';
    public $timestamps = false;
    protected $primaryKey = 'id_couleur';

    protected $fillable = [
        'nom_couleur',
        'hexa_couleur',
    ];

    protected $casts = [
        'id_couleur' => 'integer',
        'nom_couleur' => 'string',
        'hexa_couleur' => 'string',
    ];

    public function varianteVelos()
    {
        return $this->hasMany(VarianteVelo::class, 'id_couleur');
    }

    public function __toString()
    {
        return sprintf(
            "Couleur [ID: %s] : %s (%s)",
            $this->id_couleur,
            $this->nom_couleur,
            $this->hexa_couleur
        );
    }
}