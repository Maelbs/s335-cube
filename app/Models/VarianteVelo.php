<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class VarianteVelo extends Article
{
    use HasFactory;

    protected $table = 'variante_velo';
    protected $primaryKey = 'reference';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reference',
        'id_batterie',
        'id_modele',
        'id_fourche',
        'id_couleur',
        'nom_article',
        'prix',
        'poids',
    ];

    protected $casts = [
        'prix' => 'float',
        'poids' => 'float',
    ];

    public function batterie()
    {
        return $this->belongsTo(Batterie::class, 'id_batterie');
    }

    public function modele()
    {
        return $this->belongsTo(Modele::class, 'id_modele');
    }

    public function fourche()
    {
        return $this->belongsTo(Fourche::class, 'id_fourche');
    }

    public function couleur()
    {
        return $this->belongsTo(Couleur::class, 'id_couleur');
    }

    public function parent()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function accessoires()
    {
        return $this->belongsToMany(
            Accessoire::class,
            'accessoire_velo',      // Nom de la table pivot
            'reference_velo',       // Clé étrangère de ce modèle dans la pivot
            'reference_accessoire', // Clé étrangère de l'autre modèle dans la pivot
            'reference',            // Clé locale de ce modèle
            'reference'             // Clé locale de l'autre modèle
        );
    }

    public function __toString()
    {
        return sprintf(
            "Variante Vélo [Ref: %s] : %s | Modèle ID: %s | Couleur ID: %s | Batterie ID: %s | Fourche ID: %s | Prix: %s € | Stock: %s | Poids: %s kg | Dispo Web: %s",
            $this->reference,
            $this->nom_article,
            $this->id_batterie,
            $this->id_modele,
            $this->id_fourche,
            $this->id_couleur,
            number_format($this->prix, 2, ',', ' '),
            $this->qte_stock,
            $this->poids,
            $this->dispo_en_ligne ? 'Oui' : 'Non'
        );
    }
}