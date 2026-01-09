<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdresseMagasin extends Pivot
{
    protected $table = 'adresse_magasin';
    public $timestamps = false;
    
    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'id_adresse', 
        'id_magasin', 
    ];

    public function __toString(): string
    {
        return "{$this->id_adresse}, {$this->id_magasin}";
    }
}