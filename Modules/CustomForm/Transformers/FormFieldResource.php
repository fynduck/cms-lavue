<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
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
            'value'       => $this->type == 'checkbox' && !Str::contains($this->validate, 'accepted') ? [] : '',
            'file_name'   => '',
            'label'       => $this->label,
            'placeholder' => $this->placeholder,
            'validate'    => $this->checkValidate(),
            'options'     => FormFieldOptionResource::collection($this->getOptions->sortBy('priority')),
        ];
    }

    private function checkValidate()
    {
        if (!$this->validate) {
            return [];
        }

        $validates = [];
        foreach (explode(';', $this->validate) as $validate) {
            if (!in_array($validate, ['numeric', 'email', 'file', 'accepted'])) {
                $validates[] = $validate;
            }
        }

        return $validates;
    }
}
