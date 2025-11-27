<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdresseLivraison extends Model
{
    use HasFactory;

    protected $table = 'adresse_livraison';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_client',
        'id_adresse',
    ];

    protected $casts = [
        'id_client' => 'integer',
        'id_adresse' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }

    public function __toString()
    {
        return sprintf(
            "Liaison Livraison [Client: %d -> Adresse: %d]",
            $this->id_client,
            $this->id_adresse
        );
    }
}