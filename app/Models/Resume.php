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
        'id_resume',
        'contenu_resume'
    ];


    public function article()
    {
        return $this->hasOne(Article::class, 'id_resume', 'id_resume');
    }
}
