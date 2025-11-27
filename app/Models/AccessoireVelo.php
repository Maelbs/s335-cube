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
        'var_reference', 
        'reference',     
    ];

    public function varianteVelo()
    {
        return $this->belongsTo(VarianteVelo::class, 'var_reference', 'reference');
    }

    public function accessoire()
    {
        return $this->belongsTo(Accessoire::class, 'reference', 'reference');
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