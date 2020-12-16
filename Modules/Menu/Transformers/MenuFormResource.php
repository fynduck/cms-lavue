<?php

namespace Modules\Menu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Services\MenuService;

class MenuFormResource extends JsonResource
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
            'id'         => $this->id,
            'image'      => $this->oldImage(),
            'to_page'    => $this->type_page ? $this->type_page . '_' . $this->page_id : '',
            'show_page'  => $this->pagesShow(),
            'old_image'  => $this->image,
            'position'   => $this->position,
            'target'     => $this->target ?? key(Menu::targets()),
            'icon'       => $this->icon,
            'parent_id'  => $this->parent_id,
            'attributes' => $this->attributes,
            'nofollow'   => $this->nofollow,
            'priority'   => $this->priority ?? 0,
            'items'      => $this->items()
        ];
    }

    private function oldImage(): ?string
    {
        $old_image = null;
        if ($this->image)
            $old_image = (new MenuService())->linkImage($this->image, null, true);

        return $old_image;
    }

    private function items()
    {
        if (!$this->getTrans()->exists()) {
            return $this->emptyItems();
        } else {
            $items = $this->getTrans->keyBy('lang_id');

            if (count($items) != count(config('app.locales'))) {
                $locales = config('app.locales');
                foreach ($items as $lang_id => $item)
                    unset($locales[$lang_id]);

                $items = $this->emptyItems($items->toArray(), $locales);
            }

            return $items;
        }
    }

    private function emptyItems(array $items = [], $locales = null)
    {
        if (is_null($locales))
            $locales = config('app.locales');

        foreach ($locales as $lang_id => $locale) {
            $items[$lang_id] = [
                'title'            => '',
                'additional_title' => '',
                'description'      => '',
                'link'             => '',
                'active'           => null,
                'lang_id'          => '',
            ];
        }
        return collect($items);
    }

    private function pagesShow()
    {
        $page_show = [];
        if ($this->getShow()->exists()) {
            foreach ($this->getShow as $item)
                $page_show[] = $item->show_type . '_' . $item->show_on;
        }

        return $page_show;
    }
}
