<?php

namespace Modules\Banner\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Banner\Services\BannerService;

class BannerFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'link'             => $this->link,
            'old_image'        => $this->image,
            'image'            => $this->oldImage($this->image),
            'old_mobile_image' => $this->mobile_image,
            'mobile_image'     => $this->oldImage($this->mobile_image),
            'target'           => $this->target,
            'position'         => $this->position,
            'priority'         => $this->priority ?? 0,
            'date_from'        => $this->date_from ? $this->date_from->toDateTimeString() : null,
            'date_to'          => $this->date_to ? $this->date_to->toDateTimeString() : null,
            'items'            => $this->getItems(),
            'pagesShow'        => $this->showOn(),
            'toPage'           => $this->toPage()
        ];
    }

    /**
     * @param $image
     * @return string|null
     */
    private function oldImage($image): ?string
    {
        if (!$image) {
            return null;
        }

        return (new BannerService())->linkImage($image, $this->position, null, true);
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

    /**
     * @return array
     */
    private function emptyItems(): array
    {
        $items = [];
        foreach (config('app.locales') as $lang_id => $locale) {
            $items[$lang_id] = [
                'title'       => '',
                'description' => '',
                'active'      => 0,
                'lang_id'     => '',
            ];
        }
        return $items;
    }

    private function showOn()
    {
        $page_show = [];
        foreach ($this->getShow as $item) {
            $page_show[] = $item->type_page . '_' . $item->page_id;
        }

        return $page_show;
    }

    private function toPage()
    {
        $toPage = '';
        if ($this->type_page && $this->page_id) {
            $toPage = $this->type_page . '_' . $this->page_id;
        }

        return $toPage;
    }
}
