<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UtilisationCodePromo extends Pivot
{
    protected $table = 'utilisation_code_promo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_client',
        'id_codepromo',
    ];

    
    protected $casts = [
        'id_client' => 'integer',
        'id_codepromo' => 'string',
    ];
}