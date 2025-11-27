<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleSimilaire extends Model
{
    use HasFactory;

    protected $table = "Article_Similaire"; 
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null; 

    protected $fillable = [
        'article_reference',
        'similaire_reference'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_reference', 'reference');
    }

    public function similaire()
    {
        return $this->belongsTo(Article::class, 'similaire_reference', 'reference');
    }
}