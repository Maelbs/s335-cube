<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoArticle extends Model
{
    use HasFactory;

    protected $table = "photo_article";
    protected $primaryKey = "id_photo";
    public $timestamps = false;

    protected $fillable = ['reference', 'url_photo', 'est_principale'];

    protected $casts = [
        'est_principale' => 'boolean',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'reference', 'reference');
    }

    public function __toString()
    {
        $status = '';
        if ($this->est_principale) {
            $status = '[PRINCIPALE]';
        }

        return sprintf(
            "Photo #%d %s : %s (Ref Article: %s)",
            $this->id_photo,
            $status,
            $this->url_photo,
            $this->reference
        );
    }
}