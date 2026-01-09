<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventaireMagasin extends Model
{
    use HasFactory;

    protected $table = 'inventaire_magasin';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_magasin',
        'id_velo_inventaire',
        'quantite_stock_magasin',
    ];

    protected $casts = [
        'id_magasin' => 'integer',
        'id_velo_inventaire' => 'integer',
        'quantite_stock_magasin' => 'integer',
    ];

    public function magasin()
    {
        return $this->belongsTo(MagasinPartenaire::class, 'id_magasin', 'id_magasin');
    }

    public function varianteVeloInventaire()
    {
        return $this->belongsTo(VarianteVeloInventaire::class, 'id_velo_inventaire', 'id_velo_inventaire');
    }

    public function __toString()
    {
        return sprintf(
            "Stock Magasin [Magasin %d | Ref Inventaire %d] : %d unités",
            $this->id_magasin,
            $this->id_velo_inventaire,
            $this->quantite_stock_magasin
        );
    }
}