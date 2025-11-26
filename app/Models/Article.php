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

    protected $fillable = ['reference', 'nom_article', 'prix', 'qte_stock', 'dispo_en_ligne', 'poids'];

    public function categorie()
    {
        return $this->belongsTo(CategorieAccessoire::class, 'id_categorie_accessoire', 'id_categorie_accessoire');
    }

    public function parent()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }
}