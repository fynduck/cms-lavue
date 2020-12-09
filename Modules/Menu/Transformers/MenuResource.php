<?php

namespace Modules\Menu\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'attributes' => $this->attributes,
            'target'     => $this->target,
            'image'      => $this->image ? asset('storage/menus/sm/' . $this->image) : null,
            'icon'       => $this->icon,
            'nofollow'   => $this->nofollow ? 'nofollow' : false,
            'link'       => generateRoute($this),
            'title'      => $this->title ?? $this->getTrans()->lang()->value('title'),
            'children'   => MenuResource::collection($this->activeChildren->sortBy('sort'))
        ];
    }
}