<?php

namespace Modules\Banner\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Banner\Entities\BannerSettings
 *
 * @property int $id
 * @property string $name
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BannerSettings whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BannerSettings extends Model
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
