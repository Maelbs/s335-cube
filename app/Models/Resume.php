<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $table = 'resume';
    protected $primaryKey = 'id_resume';
    public $timestamps = false;

    protected $fillable = [
        'contenu_resume',
        'id_resume'
    ];

    public function variantes()
    {
        return $this->hasMany(VarianteVelo::class, 'id_resume', 'id_resume');
    }
}
