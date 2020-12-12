<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

class ArticleSettings extends Model
{
    protected $fillable = [
        'sizes',
        'resize'
    ];

    protected $casts = [
        'sizes' => 'array'
    ];
}
