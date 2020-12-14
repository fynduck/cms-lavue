<?php

namespace Modules\Menu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Language\Entities\Language;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Services\MenuService;

class MenuListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $languages = Language::pluck('name', 'id');

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'position'    => Menu::positions()[$this->position],
            'show_img'    => $this->image(),
            'show_page'   => generateRoute($this),
            'lang'        => $languages[$this->lang_id],
            'active'      => $this->active,
            'priority'    => $this->priority,
            'permissions' => [
                'edit'    => checkModulePermission('menu', 'edit'),
                'destroy' => checkModulePermission('menu', 'destroy')
            ]
        ];
    }

    private function image(): string
    {
        return (new MenuService())->linkImage($this->image, null, 'first');
    }
}