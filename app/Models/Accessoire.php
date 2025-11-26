<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessoire extends Article
{
    use HasFactory;

    protected $table = "accessoire";
    protected $primaryKey = "reference";
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['reference', 'id_categorie_accessoire', 'nom_article', 'prix', 'qte_stock', 'dispo_en_ligne', 'poids'];

    public function categorie()
    {
        return $this->belongsTo(CategorieAccessoire::class, 'id_categorie_accessoire', 'id_categorie_accessoire');
    }
     
    public function parent()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function __toString()
    {
        return sprintf(
            "Accessoire [Ref: %s] : %s | Prix: %s € | Stock: %s | Poids: %s kg | Dispo: %s | Cat ID: %s",
            $this->reference,
            $this->nom_article,
            $this->prix,
            $this->qte_stock,
            $this->poids,
            $this->dispo_en_ligne ? 'Oui' : 'Non',
            $this->id_categorie_accessoire
        );
    }
}