<?php

namespace Modules\UserGroup\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupListResource extends JsonResource
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
            'permissions' => [
                'edit'    => checkModulePermission('user-group', 'edit'),
                'destroy' => checkModulePermission('user-group', 'destroy')
            ]
        ];
    }
}
