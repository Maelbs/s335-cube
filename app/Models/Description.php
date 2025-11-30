<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    use HasFactory;

    protected $table = 'description';
    protected $primaryKey = 'id_description';
    public $timestamps = false;

    protected $fillable = [
        'texte_description',
    ];
    
    public function modeles()
    {
        // hasMany(Modele::class, 'foreign_key', 'local_key')
        return $this->hasMany(Modele::class, 'id_description', 'id_description');
    }
}
