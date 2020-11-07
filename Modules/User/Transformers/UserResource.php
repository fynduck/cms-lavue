<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Entities\User;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'username'    => $this->username,
            'admin'       => $this->isAdmin(),
            'avatar'      => $this->avatar ? getUserAvatar($this->avatarPath() . '/' . User::MD, $this->avatar) : null,
            'permissions' => $this->roles->groupPermission()->get(['name', 'rights'])->mapToGroups(function ($item) {
                return [$item['name'] => $item['rights']];
            })
        ];
    }
}