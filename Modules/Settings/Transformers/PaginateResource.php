<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
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
            'id'       => $this->id,
            'on'       => $this->on,
            'for'      => $this->for,
            'value'    => $this->value,
            'readonly' => true
        ];
    }
}
