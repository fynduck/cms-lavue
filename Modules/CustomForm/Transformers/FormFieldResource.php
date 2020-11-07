<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Language\Entities\Language;

class FormFieldResource extends JsonResource
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
            'type'        => $this->type,
            'block_class' => $this->block_class,
            'field_class' => $this->field_class,
            'field_id'    => $this->field_id,
            'name'        => $this->name,
            'value'       => $this->type == 'checkbox' && !\Str::contains($this->validate, 'accepted') ? [] : '',
            'file_name'   => '',
            'label'       => $this->label,
            'placeholder' => $this->placeholder,
            'validate'    => $this->validate ? explode('|', $this->validate) : [],
            'options'     => FormFieldOptionResource::collection($this->getOptions),
        ];
    }
}
