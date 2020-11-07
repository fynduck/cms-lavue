<?php

namespace Modules\UserGroup\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupFormResource extends JsonResource
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
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
