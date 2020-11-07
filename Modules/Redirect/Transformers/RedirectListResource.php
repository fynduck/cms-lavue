<?php

namespace Modules\Redirect\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RedirectListResource extends JsonResource
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
            'id'           => $this->id,
            'from'         => $this->from,
            'to'           => $this->to,
            'status_code'  => $this->status_code,
            'active'       => $this->active,
            'permissions' => [
                'edit'    => checkModulePermission('redirect', 'edit'),
                'destroy' => checkModulePermission('redirect', 'destroy')
            ]
        ];
    }
}
