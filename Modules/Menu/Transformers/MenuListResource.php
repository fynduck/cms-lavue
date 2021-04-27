<?php

namespace Modules\Menu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Modules\Language\Entities\Language;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Traits\MenuImageTrait;

class MenuListResource extends JsonResource
{
    use MenuImageTrait;

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
            'title'       => $this->title,
            'position'    => Menu::positions()[$this->position],
            'show_img'    => $this->image(),
            'show_page'   => generateRoute($this),
            'lang'        => $this->getLang(),
            'active'      => $this->active,
            'priority'    => $this->priority,
            'permissions' => [
                'edit'    => checkModulePermission('menu', 'edit'),
                'destroy' => checkModulePermission('menu', 'destroy')
            ]
        ];
    }

    /**
     * @return string
     */
    private function image(): string
    {
        return $this->linkImage($this->image, $this->position, null, true);
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