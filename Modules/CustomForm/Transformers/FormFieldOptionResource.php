<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldOptionResource extends JsonResource
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
            'field_id'     => $this->field_id,
            'value'        => $this->value,
            'title'        => $this->title,
            'option_class' => $this->option_class,
            'option_id'    => $this->option_id
        ];
    }
}
