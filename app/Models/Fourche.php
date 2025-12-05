<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fourche extends Model
{
    use HasFactory;

    protected $table = 'fourche';
    public $timestamps = false;
    protected $primaryKey = 'id_fourche';

    protected $fillable = [
        'nom_fourche',
    ];

    protected $casts = [
        'id_fourche' => 'integer',
        'nom_fourche' => 'string',
    ];

    public function varianteVelos()
    {
        return $this->hasMany(VarianteVelo::class, 'id_fourche');
    }

    public function __toString()
    {
        return sprintf(
            "Fourche [ID: %s] : Nom: %s",
            $this->id_fourche,
            $this->nom_fourche
        );
    }
}
