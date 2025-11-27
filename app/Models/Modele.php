<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modele extends Model
{
    use HasFactory;

    protected $table = 'modele';
    public $timestamps = false;
    protected $primaryKey = 'id_modele';

    protected $fillable = [
        'id_categorie',
        'nom_modele',
        'millesime_modele',
        'materiau_cadre',
        'type_velo',
    ];

    protected $casts = [
        'id_modele' => 'integer',
        'id_categorie' => 'integer',
        'nom_modele' => 'string',
        'millesime_modele' => 'string',
        'materiau_cadre' => 'string',
        'type_velo' => 'string',
    ];
    public function tailles()
    {
        return $this->hasManyThrough(
            Taille::class,          // Le modèle final qu'on veut (Taille)
            AGeometrie::class,    // Le modèle intermédiaire (Géo)
            'id_modele',            // Clé étrangère sur Velo
            'id_taille',            // Clé étrangère sur Taille
            'id_modele',            // Clé locale sur Modele
            'id_taille'             // Clé locale sur Velo
        )->distinct(); // distinct() pour éviter d'avoir 10 fois "M" si tu as 10 vélos M en stock
    }
    // public function tailles () {
    //     return $this->belongsToMany(
    //         Taille::class, 
    //         'a_geometrie',      // Nom de la table pivot
    //         'id_modele',             // Clé étrangère modèle courant
    //         'id_taille'      // Clé étrangère modèle lié
    //     )->withPivot('taille'); // IMPORTANT : On récupère la valeur spécifique ici
    // }

    public function geometries() {
        return $this->belongsToMany(
            Geometrie::class, 
            'a_geometrie',      // Nom de la table pivot
            'id_modele',             // Clé étrangère modèle courant
            'id_geometrie'      // Clé étrangère modèle lié
        )->withPivot('valeur_geometrie', 'id_taille'); // IMPORTANT : On récupère la valeur spécifique ici
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieVelo::class, 'id_categorie');
    }

    public function varianteVelos()
    {
        return $this->hasMany(VarianteVelo::class, 'id_modele');
    }

    public function __toString()
    {
        return sprintf(
            "Modèle [ID: %s] : Nom: %s, Millésime: %s, Type: %s",
            $this->id_modele,
            $this->nom_modele,
            $this->millesime_modele,
            $this->type_velo
        );
    }
}
