<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batterie extends Model
{
    use HasFactory;

    protected $table = 'batterie';
    public $timestamps = false;
    protected $primaryKey = 'id_batterie';

    protected $fillable = [
        'capacite_batterie',
    ];

    protected $casts = [
        'id_batterie' => 'integer',
        'capacite_batterie' => 'integer',
    ];


    public function varianteVelos()
    {
        return $this->hasMany(VarianteVelo::class, 'id_batterie');
    }

    public function __toString()
    {
        return sprintf(
            "Batterie [ID: %s] : Capacité: %s Ah",
            $this->id_batterie,
            $this->capacite_batterie
        );
    }
}
