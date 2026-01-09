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
        'id_description',
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
            Taille::class,          
            AGeometrie::class,    
            'id_modele',            
            'id_taille',            
            'id_modele',            
            'id_taille'             
        )->distinct(); 
    }

    public function geometries() {
        return $this->belongsToMany(
            Geometrie::class, 
            'a_geometrie',      
            'id_modele',             
            'id_geometrie'      
        )->withPivot('valeur_geometrie', 'id_taille'); 
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieVelo::class, 'id_categorie');
    }

    public function varianteVelos()
    {
        return $this->hasMany(VarianteVelo::class, 'id_modele');
    }
    public function description() {
        return $this->belongsTo(Description::class, 'id_description', 'id_description');
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
