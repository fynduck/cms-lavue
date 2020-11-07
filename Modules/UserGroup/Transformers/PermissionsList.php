<?php

namespace Modules\UserGroup\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsList extends JsonResource
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
            'name'   => $this->name,
            'rights' => $this->rights,
        ];
    }
}
