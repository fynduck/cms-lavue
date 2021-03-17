<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;

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
            'id'          => $this->id,
            'form_name'   => $this->form_name,
            'action'      => $this->action,
            'method'      => $this->method,
            'send_emails' => explode(';', $this->send_emails),
            'lang'        => $this->getLang(),
            'permissions' => [
                'edit'    => checkModulePermission('CustomForm', 'edit'),
                'destroy' => checkModulePermission('CustomForm', 'destroy')
            ]
        ];
    }

    /**
     * @return mixed|null
     */
    private function getLang()
    {
        $languages = Cache::remember(
            'languages_name_id',
            now()->addDay(),
            function () {
                return Language::pluck('name', 'id');
            }
        );

        return $languages[$this->lang_id] ?? null;
    }
}
