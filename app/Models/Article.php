<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author',
        'image',
    ];

    public function categories()
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_pivot', 'article_id', 'category_id');
    }
}

