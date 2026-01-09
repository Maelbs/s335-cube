<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieAccessoire extends Model
{
    use HasFactory;
    
    protected $table = "categorie_accessoire";
    protected $primaryKey = "id_categorie_accessoire";
    public $timestamps = false;
    protected $fillable = ['cat_id_categorie_accessoire', 'nom_categorie_accessoire'];

    public function parent()
    {
        return $this->belongsTo(CategorieAccessoire::class, 'cat_id_categorie_accessoire', 'id_categorie_accessoire');
    }

    public function enfants()
    {
        return $this->hasMany(CategorieAccessoire::class, 'cat_id_categorie_accessoire', 'id_categorie_accessoire');
    }

    public function accessoires()
    {
        return $this->hasMany(Accessoire::class, 'id_categorie_accessoire', 'id_categorie_accessoire');
    }
}
