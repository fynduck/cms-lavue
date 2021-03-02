<?php

use Modules\User\Entities\User;
use Nwidart\Modules\Facades\Module;

/**
 * Check active module
 * @param string $module
 * @return bool
 */
function checkModule(string $module): bool
{
    if (array_key_exists($module, Module::allEnabled())) {
        return true;
    }

    return false;
}

/**
 * Check permission user for module
 * @param $permission
 * @param $type
 * @param User|null $user
 * @return bool
 */
function checkModulePermission($permission, $type, User $user = null): bool
{
    if (!auth()->check() && !$user) {
        return false;
    }

    if (auth()->check()) {
        return auth()->user()->isAdmin() ? true : auth()->user()->roles->groupPermission()->ofAccess(
            strtolower($permission),
            $type
        )->exists();
    } else {
        return $user->roles->groupPermission()->ofAccess(strtolower($permission), $type)->exists();
    }
}
