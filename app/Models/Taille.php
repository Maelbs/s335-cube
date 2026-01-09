<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taille extends Model
{
    use HasFactory;

    protected $table = 'taille';
    protected $primaryKey = 'id_taille';
    public $timestamps = false;

    protected $fillable = [
        'taille', 
        'taille_min', 
        'taille_max', 
    ];

    protected $casts = [
        'id_taille' => 'integer',
        'taille' => 'string',
        'taille_min' => 'integer',
        'taille_max' => 'integer',
    ];

    public function aGeometries()
    {
        return $this->hasMany(AGeometrie::class, 'id_taille', 'id_taille');
    }

    public function ArticleInventaire()
    {
        return $this->hasMany(ArticleInventaire::class, 'id_taille', 'id_taille');
    }

    public function __toString()
    {
        return sprintf(
            "Taille [ID: %d] : %s (Pour cycliste: %d-%d cm)",
            $this->id_taille,
            $this->taille,
            $this->taille_min,
            $this->taille_max
        );
    }
}