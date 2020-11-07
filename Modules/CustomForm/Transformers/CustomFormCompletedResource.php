<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFormCompletedResource extends JsonResource
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
            'form_title' => $this->form_id ? $this->form->form_name : null,
            'form_data'  => $this->form_data
        ];
    }
}
