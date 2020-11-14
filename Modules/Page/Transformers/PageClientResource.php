<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'description'        => $this->description,
            'description_footer' => $this->description_footer,
            'meta_title'         => $this->generateMeta('meta_title', ['title']),
            'meta_description'   => $this->generateMeta('meta_description', ['description', 'description_footer']),
            'meta_keywords'      => $this->generateMeta('meta_keywords')
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
        $string = \Str::limit($string, $limit, '');

        return preg_replace('!\s+!', ' ', trim($string));
    }
}
