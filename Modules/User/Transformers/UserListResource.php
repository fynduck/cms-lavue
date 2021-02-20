<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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
            'email'       => $this->email,
            'group'       => $this->roles->name,
            'permissions' => [
                'edit'    => checkModulePermission('user', 'edit'),
                'destroy' => checkModulePermission('user', 'destroy')
            ]
        ];
    }
}
