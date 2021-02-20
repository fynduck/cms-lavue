<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PageFormResource extends JsonResource
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
            'id'        => $this->id,
            'parent_id' => $this->parent_id,
            'method'    => $this->method,
            'socials'   => $this->socials ?? 0,
            'priority'  => $this->priority ?? 0,
            'items'     => $this->items()
        ];
    }

    private function items()
    {
        if (!$this->getTrans()->exists()) {
            return $this->emptyItems();
        } else {
            $items = $this->getTrans->keyBy('lang_id');

            if (count($items) != count(config('app.locales'))) {
                $locales = config('app.locales');
                foreach ($items as $lang_id => $item) {
                    unset($locales[$lang_id]);
                }

                $items = $this->emptyItems($items->toArray(), $locales);
            }

            return $items;
        }
    }

    private function emptyItems(array $items = [], $locales = null)
    {
        if (is_null($locales)) {
            $locales = config('app.locales');
        }

        foreach ($locales as $lang_id => $locale) {
            $items[$lang_id] = [
                'title'              => '',
                'description'        => '',
                'description_footer' => '',
                'slug'               => '',
                'meta_title'         => '',
                'meta_description'   => '',
                'meta_keywords'      => '',
                'active'             => null,
                'lang_id'            => null,
            ];
        }
        return collect($items);
    }
}
