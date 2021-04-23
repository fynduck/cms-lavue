<?php

namespace Modules\Search\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\Search\Entities\Search
 *
 * @property int $id
 * @property string $search
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Search newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search query()
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Search extends Model
{
    protected $fillable = [
        'search',
        'count'
    ];
}
