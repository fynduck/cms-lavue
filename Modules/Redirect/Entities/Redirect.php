<?php

namespace Modules\Redirect\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Modules\Redirect\Entities\Redirect
 *
 * @property int $id
 * @property string $from
 * @property string $to
 * @property int $status_code
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect filter(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\Redirect\Entities\Redirect whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Redirect extends Model
{
    protected $fillable = [
        'from',
        'to',
        'status_code',
        'active'
    ];

    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q')) {
            $query->where('from', 'LIKE', '%' . $request->get('q') . '%')
                ->orWhere('to', 'LIKE', '%' . $request->get('q') . '%');
        }

        if ($request->get('active')) {
            $query->where('active', $request->get('active'));
        }

        if ($request->get('sortBy')) {
            $sort = 'ASC';
            if ($request->get('sortDesc')) {
                $sort = 'DESC';
            }
            $query->orderBy($request->get('sortBy'), $sort);
        }

        return $query;
    }
}
