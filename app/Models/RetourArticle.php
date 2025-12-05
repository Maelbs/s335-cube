<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetourArticle extends Model
{
    protected $table = 'retour_article';
    protected $primaryKey = 'id_retour';
    public $timestamps = false;

    protected $fillable = [
        'id_commande', 
        'date_retour',
        'motif',
        'etat_traitement',
    ];

    protected $casts = [
        'date_retour' => 'date',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function __toString(): string
    {
        return "Retour #{$this->id_retour}";
    }
}