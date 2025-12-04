<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessoireVelo extends Model
{
    use HasFactory;

    protected $table = 'accessoire_velo';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'reference_velo', 
        'reference_accessoire',     
    ];

    public function varianteVelo()
    {
        return $this->belongsTo(VarianteVelo::class, 'reference', 'reference_velo');
    }

    public function accessoire()
    {
        return $this->belongsTo(Accessoire::class, 'reference', 'reference_accessoire');
    }

    public function __toString()
    {
        return sprintf(
            "Compatibilité [Vélo Ref: %s -> Accessoire Ref: %s]",
            $this->var_reference,
            $this->reference
        );
    }
}