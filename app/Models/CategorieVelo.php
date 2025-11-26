<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieVelo extends Model
{
    use HasFactory;

    protected $table = 'categorie_velo';
    public $timestamps = false;
    protected $primaryKey = 'id_categorie';

    protected $fillable = [
        'CAT_id_categorie',
        'nom_categorie',
    ];

    protected $casts = [
        'id_categorie' => 'integer',
        'CAT_id_categorie' => 'integer',
        'nom_categorie' => 'string',
    ];

    public function modeles()
    {
        return $this->hasMany(Modele::class, 'id_categorie');
    }

    public function parent()
    {
        return $this->belongsTo(CategorieVelo::class, 'CAT_id_categorie');
    }

    public function enfants()
    {
        return $this->hasMany(CategorieVelo::class, 'CAT_id_categorie');
    }

    public function __toString()
    {
        return sprintf(
            "Catégorie Vélo [ID: %s] : Nom: %s, Catégorie Parent ID: %s",
            $this->id_categorie,
            $this->nom_categorie,
            $this->CAT_id_categorie
        );
    }
}