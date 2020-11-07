<?php

namespace Modules\UserGroup\Traits;

use Modules\UserGroup\Entities\UserGroup;

trait UserGroupTrait
{
    public static function groupIdByName($name)
    {
        return UserGroup::where('name', $name)->value('id');
    }
}