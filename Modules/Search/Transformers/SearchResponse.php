<?php

namespace Modules\Search\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Article\Entities\ArticleTrans;
use Modules\Page\Entities\PageTrans;

class SearchResponse extends JsonResource
{
    private $pageUrlsOnLang;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->pageUrlsOnLang = cache('urls_pages_' . config('app.locale_id'));
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'title'       => $this->title,
            'link'        => $this->generateLink(),
            'description' => $this->description(),
            'priority'    => $this->relevance,
        ];
    }

    private function generateLink(): string
    {
        $params = [
            count(config('app.locales')) > 1 ? config('app.locale') : null
        ];
        if (!$this->resource instanceof PageTrans && $this->pageUrlsOnLang) {
            if ($this->resource instanceof ArticleTrans) {
                $type = $this->resource->getArticle->type;
                if (array_key_exists($type, $this->pageUrlsOnLang)) {
                    $params[] = cache('urls_pages_' . config('app.locale_id'))[$type];
                }
            }
        }
        $params[] = $this->slug;

        return Str::start(implode('/', $params), '/');
    }

    private function description(): string
    {
        $description = '';
        if (strip_tags($this->description)) {
            $description = strip_tags($this->description);
        } elseif (strip_tags($this->short_desc)) {
            $description = strip_tags($this->short_desc);
        }

        return html_entity_decode(Str::limit($description, 160));
    }
}
