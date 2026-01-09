<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleSimilaire extends Model
{
    use HasFactory;

    protected $table = "article_similaire"; 
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null; 

    protected $fillable = [
        'art_reference',
        'reference'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'art_reference', 'reference');
    }

    public function similaire()
    {
        return $this->belongsTo(Article::class, 'reference');
    }
}