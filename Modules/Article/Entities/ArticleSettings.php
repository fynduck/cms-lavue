<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

class ArticleSettings extends Model
{
    const RESIZE = 'resize';

    const CROP = 'crop';

    protected $fillable = [
        'name',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public static function resizeMethods()
    {
        return [
            self::RESIZE,
            self::CROP
        ];
    }
}
