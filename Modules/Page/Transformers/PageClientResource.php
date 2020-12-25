<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Page\Entities\PageTrans;

class PageClientResource extends JsonResource
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
            'id'                 => $this->id,
            'method'             => $this->method,
            'module'             => $this->module,
            'socials'            => $this->socials ?? 0,
            'title'              => $this->title,
            'description'        => "<div>$this->description</div>",
            'description_footer' => "<div>$this->description_footer</div>"
        ];
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
        $pageLang = PageTrans::where('page_id', $this->page_id)
            ->where('lang_id', '!=', $this->lang_id)
            ->where('active', 1)
            ->pluck('slug', 'lang_id');

        return [
            'meta'      => [
                'meta_title'         => $this->generateMeta('meta_title', ['title']),
                'meta_description'   => $this->generateMeta('meta_description', ['description', 'description_footer']),
                'meta_keywords'      => $this->generateMeta('meta_keywords')
            ],
            'page_lang' => $pageLang,
            's' => config('app.locale_id')
        ];
    }
}
