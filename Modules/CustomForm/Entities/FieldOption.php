<?php

namespace Modules\CustomForm\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\CustomForm\Entities\FieldOption
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $field_id
 * @property string $value
 * @property string $title
 * @property string|null $option_class
 * @property string|null $option_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereOptionClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\CustomForm\Entities\FieldOption whereValue($value)
 */
class FieldOption extends Model
{
    protected $fillable = [
        'field_id',
        'value',
        'title',
        'option_class',
        'option_id',
        'priority'
    ];
}
