<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Article\Entities\Article;
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
            'id'           => $this->article_id,
            'title'        => $this->title,
            'show_type'    => Article::getTypes()[$this->type],
            'show_img'     => $this->image ? asset('storage/' . Article::FOLDER_IMG . '/' . key(Article::getSizes()) . '/' . $this->image) : null,
            'lang'         => $languages[$this->lang_id],
            'active'       => $this->active,
            'sort'         => $this->sort,
            'permissions' => [
                'edit'    => checkModulePermission('article', 'edit'),
                'destroy' => checkModulePermission('article', 'destroy')
            ]
        ];
    }
}
