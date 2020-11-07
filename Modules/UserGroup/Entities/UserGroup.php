<?php

namespace Modules\UserGroup\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\UserGroup\Traits\UserGroupTrait;

/**
 * Modules\UserGroup\Entities\UserGroup
 *
 * @property int $id
 * @property string $name
 * @property int $admin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\UserGroup\Entities\Permission[] $groupPermission
 * @property-read int|null $group_permission_count
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\UserGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserGroup extends Model
{
    use UserGroupTrait;

    const ADMIN = 'Admin';

    const USERS = 'Users';

    protected $fillable = ['name', 'rights', 'admin'];

    public function groupPermission()
    {
        return $this->belongsToMany(Permission::class, 'group_permissions', 'group_id');
    }
}
