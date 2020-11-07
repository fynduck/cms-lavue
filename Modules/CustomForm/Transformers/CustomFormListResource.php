<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFormListResource extends JsonResource
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
            'id'              => $this->id,
            'form_name'       => $this->form_name,
            'action'          => $this->action,
            'method'          => $this->method,
            'send_emails'     => explode(';', $this->send_emails),
            'permissions'     => [
                'edit'    => checkModulePermission('CustomForm', 'edit'),
                'destroy' => checkModulePermission('CustomForm', 'destroy')
            ]
        ];
    }
}
