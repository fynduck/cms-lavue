<?php

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Article\Entities\ArticleSettings
 *
 * @property int $id
 * @property string $name
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleSettings whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleSettings extends Model
{
    const RESIZE = 'resize';

    const RESIZE_CROP = 'resize-crop';

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
            self::RESIZE_CROP,
            self::CROP
        ];
    }
}
