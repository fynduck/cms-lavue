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
 */
class MenuSettings extends Model
{
    protected $fillable = [
        'sizes',
        'resize'
    ];

    protected $casts = [
        'sizes' => 'array'
    ];
}
