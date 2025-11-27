<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeleRa extends Model
{
    use HasFactory;

    protected $table = 'modele_ra';
    protected $primaryKey = 'id_modele_ra';
    public $timestamps = false;

    protected $fillable = [
        'reference', 
        'url_modele_ra',
    ];

    public function varianteVelo()
    {
        return $this->belongsTo(VarianteVelo::class, 'reference', 'reference');
    }

    public function __toString()
    {
        return sprintf(
            "Modèle RA [ID: %d] : %s (Ref Vélo: %s)",
            $this->id_modele_ra,
            $this->url_modele_ra,
            $this->reference
        );
    }
}