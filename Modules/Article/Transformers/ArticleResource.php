<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Article\Entities\ArticleTrans;
use Modules\Article\Traits\ArticleImageTrait;

class ArticleResource extends JsonResource
{
    use ArticleImageTrait;

    private $pageUrlsOnLang;

    public function __construct($resource)
    {
        if ($resource instanceof ArticleTrans) {
            $article = $resource->getArticle;
            $resource->id = $resource->article_id;
            $resource->date = $article->date;
            $resource->date_to = $article->date_to;
            $resource->image = $article->image;
            $resource->type = $article->type;
        }

        $this->pageUrlsOnLang = cache('urls_pages_' . config('app.locale_id'));

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
            'show_date'         => localeDate($this->date),
            'date_to'           => $this->date_to,
            'promo_finish_date' => $this->date_to && $this->date_to->isFuture() ? $this->date_to->timestamp : null,
            'views'             => 0
        ];
    }

    private function imgObj(): array
    {
        return [
            'src'     => $this->linkImage($this->image, $this->type, 'biggest_size'),
            'loading' => $this->linkImage($this->image, $this->type, null, true)
        ];
    }

    private function generateLink(): string
    {
        if ($this->pageUrlsOnLang && array_key_exists($this->type, $this->pageUrlsOnLang)) {
            $pageSlug = cache('urls_pages_' . config('app.locale_id'))[$this->type];
        } else {
            return '';
        }

        $params = [
            count(config('app.locales')) > 1 ? config('app.locale') : null,
            $pageSlug,
            $this->slug
        ];
        return Str::start(implode('/', $params), '/');
    }

    private function srcset(): array
    {
        return $this->linkImages($this->image, $this->type);
    }

    private function generateMiniDescription(): string
    {
        if (request()->get('show_home')) {
            return '';
        }

        $description = $this->description;
        if ($this->short_desc) {
            $description = $this->short_desc;
        }

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
            if (cache('urls_pages_' . $item->lang_id) && array_key_exists($type, cache('urls_pages_' . $item->lang_id))) {
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
