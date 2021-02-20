<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Settings\Entities\Social
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $class_icon
 * @property int $priority
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereClassIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Social whereUrl($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Social wherePriority($value)
 */
class Social extends Model
{
    protected $fillable = [
        'name',
        'url',
        'class_icon',
        'priority'
    ];

    public $timestamps = false;
}
