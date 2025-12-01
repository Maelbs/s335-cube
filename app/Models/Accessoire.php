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

    protected $fillable = [
        'reference', 
        'id_categorie_accessoire', 
        'nom_article', 
        'prix', 
        'poids', 
        'quantite_stock_accessoire', 
        'dispo_en_ligne', 
        'dispo_magasin',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'poids' => 'decimal:1',
        'quantite_stock_accessoire' => 'integer',
        'dispo_en_ligne' => 'boolean',
        'dispo_magasin' => 'boolean',
    ];

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
        $dispoenligne = 'Non';
        $dispomagasin = 'Non';

        if ($this->dispo_en_ligne) {
            $dispoenligne = 'Oui';
        }

        if ($this->dispo_magasin) {
            $dispomagasin = 'Oui';
        }

        return sprintf(
            "Accessoire [Ref: %s] : %s | Prix: %s € | Poids: %s kg | Stock: %s | Dispo en ligne: %s | Dispo en magasin: %s | Cat ID: %s",
            $this->reference,
            $this->nom_article,
            $this->prix,
            $this->poids,
            $this->quantite_stock_accessoire,
            $dispoenligne,
            $dispomagasin,
            $this->id_categorie_accessoire
        );
    }

}