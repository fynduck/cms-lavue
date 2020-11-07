<?php

namespace Modules\Page\Transformers;

use App\Services\SeoCalculator;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Language\Entities\Language;

class PageListResource extends JsonResource
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
            'id'           => $this->page_id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'active'       => $this->active,
            'lang'         => $this->lang_id ? $languages[$this->lang_id] : null,
            'socials'      => $this->socials,
            'seo_complete' => SeoCalculator::collectionCalcSeo($this),
            'link'         => $this->generateLink(),
            'permissions' => [
                'edit'    => checkModulePermission('page', 'edit'),
                'destroy' => !$this->method ?? checkModulePermission('page', 'destroy')
            ]
        ];
    }

    private function generateLink()
    {
        $link = url($this->slug);
        if (count(config('app.locales')) > 1)
            $link = url(config('app.locales.' . $this->lang_id . '.slug'), $this->slug);

        return $link;
    }
}
