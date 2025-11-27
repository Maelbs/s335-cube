<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geometrie extends Model
{
    use HasFactory;

    protected $table = 'geometrie';
    protected $primaryKey = 'id_geometrie';
    public $timestamps = false;

    protected $fillable = [
        'nom_geometrie',
    ];

    protected $casts = [
        'id_geometrie' => 'integer',
        'nom_geometrie' => 'string',
    ];

    public function aGeometries()
    {
        return $this->hasMany(AGeometrie::class, 'id_geometrie', 'id_geometrie');
    }

    public function __toString()
    {
        return sprintf(
            "Géométrie [ID: %s] : %s",
            $this->id_geometrie,
            $this->nom_geometrie
        );
    }
}