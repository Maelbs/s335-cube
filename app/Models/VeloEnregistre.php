<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VeloEnregistre extends Model
{
    use HasFactory;

    protected $table = 'velo_enregistre';
    protected $primaryKey = 'num_serie';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'num_serie',
        'id_client',
        'date_enregistrement',
    ];

    protected $casts = [
        'date_enregistrement' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function __toString()
    {
        if ($this->date_enregistrement) {
            $date = $this->date_enregistrement->format('d/m/Y');
        } else {
            $date = 'N/A';
        }

        return sprintf(
            "Vélo Enregistré [Série: %s] - Client ID: %d (Date: %s)",
            $this->num_serie,
            $this->id_client,
            $date
        );
    }
}