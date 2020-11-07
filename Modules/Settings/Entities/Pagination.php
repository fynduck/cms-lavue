<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Settings\Entities\Pagination
 *
 * @property int $id
 * @property string $on
 * @property string $for
 * @property int $value
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Settings\Entities\Pagination whereValue($value)
 * @mixin \Eloquent
 */
class Pagination extends Model
{
    protected $fillable = ['on', 'for', 'value', 'user_id'];
}
