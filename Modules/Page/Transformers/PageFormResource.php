<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

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
            'items'     => $this->getItems()
        ];
    }

    private function getItems(): Collection
    {
        $items = $this->emptyItems();

        if ($this->getTrans()->exists()) {
            $existItems = $this->getTrans->keyBy('lang_id');

            $items = array_replace($items, $existItems->toArray());
        }

        return collect($items);
    }


    private function emptyItems(): array
    {
        $items = [];
        foreach (config('app.locales') as $lang_id => $locale) {
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
        return $items;
    }
}
