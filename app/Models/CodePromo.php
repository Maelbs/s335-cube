<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodePromo extends Model
{
    use HasFactory;

    protected $table = 'code_promo';
    protected $primaryKey = 'id_codepromo';
    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_codepromo',
        'pourcentage',
    ];

    protected $casts = [
        'pourcentage' => 'float', 
    ];

    public function paniers()
    {
        return $this->hasMany(Panier::class, 'code_promo', 'id_codepromo');
    }

    public function getPourcentageAfficheAttribute()
    {
        return ($this->pourcentage * 100) . '%';
    }
    public function clientsAyantUtilise()
    {
        return $this->belongsToMany(
            Client::class,              
            'utilisation_code_promo',   
            'id_codepromo',             
            'id_client'                 
        );
    }
}