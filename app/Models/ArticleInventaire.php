<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleInventaire extends Model
{
    use HasFactory;

    protected $table = 'article_inventaire';
    protected $primaryKey = 'id_article_inventaire';
    public $timestamps = false;

    protected $fillable = [
        'id_taille',
        'reference',
        'quantite_stock_en_ligne',
    ];

    protected $casts = [
        'id_article_inventaire' => 'integer',
        'id_taille' => 'integer',
        'quantite_stock_en_ligne' => 'integer',
    ];

    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille', 'id_taille');
    }

    public function articles()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function magasins()
    {
        return $this->belongsToMany(MagasinPartenaire::class, 'inventaire_magasin', 'id_article_inventaire', 'id_magasin')
                    ->withPivot('quantite_stock_magasin');
    }

    public function __toString()
    {
        return sprintf(
            "Inventaire [ID: %d] : Réf %s (Taille ID: %d) - Stock en ligne: %d",
            $this->id_article_inventaire,
            $this->reference,
            $this->id_taille,
            $this->quantite_stock_en_ligne
        );
    }
}