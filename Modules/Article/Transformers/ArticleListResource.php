<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\Article;
use Modules\Article\Traits\ArticleImageTrait;
use Modules\Language\Entities\Language;

class ArticleListResource extends JsonResource
{
    use ArticleImageTrait;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->article_id,
            'title'       => $this->title,
            'show_type'   => Article::getTypes()[$this->type],
            'show_img'    => $this->image(),
            'lang'        => $this->getLang(),
            'active'      => $this->active,
            'priority'    => $this->priority,
            'permissions' => [
                'edit'    => checkModulePermission('article', 'edit'),
                'destroy' => checkModulePermission('article', 'destroy')
            ]
        ];
    }

    /**
     * @return string
     */
    private function image(): string
    {
        return $this->linkImage($this->image, null, true);
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