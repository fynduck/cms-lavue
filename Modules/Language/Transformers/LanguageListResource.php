<?php

namespace Modules\Language\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Language\Entities\Language;

class LanguageListResource extends JsonResource
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
            'name'        => $this->name,
            'country_iso' => $this->country_iso,
            'slug'        => $this->slug,
            'active'      => $this->active,
            'default'     => $this->default,
            'priority'    => $this->priority ?? 0,
            'show_img'    => $this->image ? asset('storage/' . Language::FOLDER_IMG . '/' . $this->image) : null,
            'image'       => $this->oldImage(),
            'permissions' => [
                'edit'    => checkModulePermission('Languages', 'edit'),
                'destroy' => checkModulePermission('Languages', 'destroy')
            ]
        ];
    }


    private function oldImage()
    {
        $old_image = null;

        if ($this->image) {
            $old_image = asset('storage/' . Language::FOLDER_IMG . '/' . $this->image);
        }

        return $old_image;
    }
}
