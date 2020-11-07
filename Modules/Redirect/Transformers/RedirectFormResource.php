<?php

namespace Modules\Redirect\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RedirectFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'from'         => $this->from,
            'to'           => $this->to,
            'status_code'  => $this->status_code,
            'active'       => $this->active,
        ];
    }
}
