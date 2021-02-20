<?php

namespace Modules\UserGroup\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\UserGroup\Entities\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $rights
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission ofAccess($to, $access)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission whereRights($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends Model
{
    protected $fillable = ['name', 'rights'];

    public function scopeOfAccess($query, $to, $access)
    {
        if (is_array($to)) {
            return $query->whereIn('name', $to)->where('rights', $access);
        }

        return $query->where('name', $to)->where('rights', $access);
    }
}
