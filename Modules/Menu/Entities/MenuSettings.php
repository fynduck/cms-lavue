<?php

namespace Modules\Menu\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Menu\Entities\MenuSettings
 *
 * @property int $id
 * @property array $sizes
 * @property string $resize
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereResize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereSizes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $name
 * @property array $data
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuSettings whereName($value)
 */
class MenuSettings extends Model
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
