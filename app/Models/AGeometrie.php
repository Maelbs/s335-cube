<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AGeometrie extends Model
{
    use HasFactory;

    protected $table = 'a_geometrie';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_geometrie',
        'id_modele',
        'id_taille',
        'valeur_geometrie',
    ];

    protected $casts = [
        'id_geometrie' => 'integer',
        'id_modele' => 'integer',
        'id_taille' => 'integer',
        'valeur_geometrie' => 'decimal:1',
    ];

    public function geometrie()
    {
        return $this->belongsTo(Geometrie::class, 'id_geometrie', 'id_geometrie');
    }

    public function modele()
    {
        return $this->belongsTo(Modele::class, 'id_modele', 'id_modele');
    }

    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille', 'id_taille');
    }

    public function __toString()
    {
        return sprintf(
            "Géométrie [Modèle: %d | Taille: %d | Type: %d] : %s",
            $this->id_modele,
            $this->id_taille,
            $this->id_geometrie,
            $this->valeur_geometrie
        );
    }
}