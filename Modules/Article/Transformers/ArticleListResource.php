<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Services\ArticleService;
use Modules\Language\Entities\Language;

class ArticleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $languages = Language::pluck('name', 'id')->toArray();

        return [
            'id'          => $this->article_id,
            'title'       => $this->title,
            'show_type'   => Article::getTypes()[$this->type],
            'show_img'    => $this->image(),
            'lang'        => $languages[$this->lang_id],
            'active'      => $this->active,
            'priority'    => $this->priority,
            'permissions' => [
                'edit'    => checkModulePermission('article', 'edit'),
                'destroy' => checkModulePermission('article', 'destroy')
            ]
        ];
    }

    private function image(): string
    {
        return (new ArticleService())->linkImage($this->image, null, 'first');
    }
}