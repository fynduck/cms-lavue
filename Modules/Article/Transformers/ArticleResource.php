<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Services\ArticleService;

class ArticleResource extends JsonResource
{
    public function __construct($resource)
    {
        if ($resource instanceof ArticleTrans) {

            $resource->id = $resource->article_id;
            $resource->date = $resource->getArticle->date;
            $resource->date_to = $resource->getArticle->date_to;
            $resource->image = $resource->getArticle->image;
        }

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     * @throws \Exception
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'desc'              => $this->generateMiniDescription(),
            'description'       => "<div>$this->description</div>",
            'imgObj'            => $this->imgObj(),
            'srcset'            => $this->srcset(),
            'link'              => $this->generateLink(),
            'date'              => $this->date,
            'show_date'         => $this->date->format('d.m.Y'),
            'date_to'           => $this->date_to,
            'promo_finish_date' => $this->date_to && $this->date_to->isFuture() ? $this->date_to->timestamp : null
        ];
    }

    private function imgObj(): array
    {
        return [
            'src'     => (new ArticleService())->linkImage($this->image),
            'loading' => (new ArticleService())->linkImage($this->image, null, true)
        ];
    }

    private function generateLink(): string
    {
        $pageSlug = null;
        if (array_key_exists($this->type, cache('urls_pages_' . config('app.locale_id')))) {
            $pageSlug = cache('urls_pages_' . config('app.locale_id'))[$this->type];
        }

        $params = [
            count(config('app.locales')) > 1 ? config('app.locale') : null,
            $pageSlug,
            $this->slug
        ];
        return '/' . implode('/', $params);
    }

    private function srcset(): array
    {
        return (new ArticleService())->linkImages($this->image);
    }

    private function generateMiniDescription(): string
    {
        $description = null;
        if ($this->short_desc)
            $description = $this->short_desc;
        else
            $description = $this->description;

        return html_entity_decode(Str::limit(strip_tags($description), 160));
    }

    private function generateMeta(string $key, array $keys = [], int $length = 140)
    {
        $response = null;
        if (!empty($this->{$key})) {
            $response = $this->clearString($this->{$key}, $length);
        } elseif ($keys) {
            foreach ($keys as $keyName) {
                if (!empty($this->{$keyName})) {
                    $response = $this->clearString($this->{$keyName}, $length);
                    break;
                }
            }
        }

        return $response;
    }

    private function clearString(string $string, int $limit)
    {
        $string = html_entity_decode(strip_tags($string));
        $string = Str::limit($string, $limit, '');

        return preg_replace('!\s+!', ' ', trim($string));
    }

    public function with($request): array
    {
        $pageLang = ArticleTrans::where('article_id', $this->id)
            ->where('lang_id', '!=', $this->lang_id)
            ->active()
            ->get(['slug', 'lang_id', 'article_id']);

        foreach ($pageLang as $key => $item) {
            $type = $item->getArticle->type;
            if (array_key_exists($type, cache('urls_pages_' . $item->lang_id))) {
                $params = [
                    cache('urls_pages_' . $item->lang_id)[$type],
                    $item->slug
                ];
                $item->slug = implode('/', $params);
            } else {
                $pageLang->forget($key);
            }
        }

        return [
            'meta'      => [
                'meta_title'       => $this->generateMeta('meta_title', ['title']),
                'meta_description' => $this->generateMeta('meta_description', ['description', 'short_desc']),
                'meta_keywords'    => $this->generateMeta('meta_keywords')
            ],
            'page_lang' => $pageLang->pluck('slug', 'lang_id')
        ];
    }
}
