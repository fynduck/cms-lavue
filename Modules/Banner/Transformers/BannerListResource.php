<?php

namespace Modules\Banner\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Services\BannerService;
use Modules\Language\Entities\Language;

class BannerListResource extends JsonResource
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
            'id'          => $this->banner_id,
            'title'       => $this->title,
            'show_img'    => $this->pathImage(),
            'lang'        => $this->getLang(),
            'active'      => $this->active,
            'priority'    => $this->priority,
            'position'    => Banner::getPositions()[$this->position],
            'permissions' => [
                'edit'    => checkModulePermission('banner', 'edit'),
                'destroy' => checkModulePermission('banner', 'destroy')
            ]
        ];
    }

    /**
     * @return string
     */
    private function pathImage(): string
    {
        return (new BannerService())->linkImage($this->image, $this->position, null, true);
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
