<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Modules\User\Transformers\UserResource;

class AccountInfo extends AdminController
{
    /**
     * Show the profile for the given user.
     *
     * @return UserResource
     */
    public function __invoke()
    {
        return new UserResource(auth()->user());
    }
}
