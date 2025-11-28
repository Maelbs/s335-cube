<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = "article";
    protected $primaryKey = "reference";
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reference', 
        'nom_article', 
        'prix', 
        'qte_stock', 
        'dispo_en_ligne', 
        'poids'
    ];

    protected $casts = [
        'dispo_en_ligne' => 'boolean',
        'prix' => 'decimal:2', 
        'poids' => 'decimal:1',
    ];

    public function caracteristiques()
    {
        return $this->belongsToMany(
            Caracteristique::class, 
            'a_caracteristique',      // Nom de la table pivot
            'reference',             // Clé étrangère modèle courant
            'id_caracteristique'      // Clé étrangère modèle lié
        )->withPivot('valeur_caracteristique'); // IMPORTANT : On récupère la valeur spécifique ici
    }

    public function photos()
    {
        return $this->hasMany(PhotoArticle::class, 'reference', 'reference');
    }

    public function accessoire()
    {
        return $this->hasOne(Accessoire::class, 'reference', 'reference');
    }

    public function varianteVelo()
    {
        return $this->hasOne(VarianteVelo::class, 'reference', 'reference');
    }
    
    public function similaires()
    {
        return $this->hasMany(ArticleSimilaire::class, 'article_reference', 'reference');
    }

    public function getPhotoPrincipaleAttribute()
    {
        if ($this->photos->isEmpty()) {
            return 'https://placehold.co/300x200?text=Pas+d+image';
        }

        $photoPrincipale = null;
        foreach ($this->photos as $photo) {
            if ($photo->est_principale) {
                $photoPrincipale = $photo;
                break;
            }
        }

        if ($photoPrincipale !== null) {
            return $photoPrincipale->url_photo;
        } else {
            return $this->photos->first()->url_photo;
        }
    }

    public function __toString()
    {
        $dispo = 'Non';

        if ($this->dispo_en_ligne) {
            $dispo = 'Oui';
        }

        return sprintf(
            "Article [Ref: %s] : %s | Prix: %s € | Stock: %s | Dispo: %s",
            $this->reference,
            $this->nom_article,
            $this->prix,
            $this->qte_stock,
            $dispo
        );
    }
}