<?php

namespace Modules\Menu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Modules\Menu\Traits\MenuImageTrait;

class MenuResource extends JsonResource
{
    use MenuImageTrait;

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
            'attributes' => $this->attributes,
            'target'     => $this->target,
            'image'      => $this->image(),
            'icon'       => $this->icon,
            'nofollow'   => $this->nofollow ? 'nofollow' : false,
            'link'       => generateRoute($this),
            'title'      => $this->title ?? $this->getTrans()->lang()->value('title'),
            'children'   => MenuResource::collection($this->activeChildren->sortBy('priority'))
        ];
    }

    private function image()
    {
        if ($this->image) {
            return $this->linkImage($this->image, $this->position);
        }

        return null;
    }
}