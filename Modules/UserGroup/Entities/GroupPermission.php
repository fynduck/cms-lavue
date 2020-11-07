<?php

namespace Modules\UserGroup\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\UserGroup\Entities\GroupPermission
 *
 * @property int $id
 * @property int $group_id
 * @property int $permission_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\UserGroup\Entities\Permission $permission
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\UserGroup\Entities\GroupPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GroupPermission extends Model
{
    protected $fillable = ['group_id', 'permission_id'];

    public function permission()
    {
        return $this->hasOne(Permission::class, 'id', 'permission_id');
    }
}
