<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Language\Entities\Language;

class FormResource extends JsonResource
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
            'form_name'   => $this->form_name,
            'file'        => $this->file,
            'form_class'  => $this->form_class,
            'form_id'     => $this->form_id,
            'action'      => $this->action,
            'method'      => $this->method,
            'send_emails' => $this->send_emails,
            'fields'      => FormFieldResource::collection($this->getFields->sortBy('priority'))
        ];
    }
}
