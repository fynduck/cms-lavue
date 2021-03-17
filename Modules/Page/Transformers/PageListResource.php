<?php

namespace Modules\Page\Transformers;

use App\Services\SeoCalculator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
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
        return [
            'id'           => $this->page_id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'active'       => $this->active,
            'lang'         => $this->getLang(),
            'socials'      => $this->socials,
            'seo_complete' => SeoCalculator::collectionCalcSeo($this),
            'link'         => $this->generateLink(),
            'permissions'  => [
                'edit'    => checkModulePermission('page', 'edit'),
                'destroy' => !$this->method ?? checkModulePermission('page', 'destroy')
            ]
        ];
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    private function generateLink()
    {
        $link = url($this->slug);
        if (count(config('app.locales')) > 1) {
            $link = url(config('app.locales.' . $this->lang_id . '.slug'), $this->slug);
        }

        return $link;
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
