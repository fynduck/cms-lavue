<?php

namespace Modules\Banner\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Banner\Services\BannerService;
use Modules\Banner\Traits\BannerImageTrait;

class BannerResource extends JsonResource
{
    use BannerImageTrait;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title'         => $this->title,
            'description'   => $this->description,
            'target'        => $this->target,
            'slide'         => $this->imgObj(),
            'srcset'        => $this->srcset($this->image),
//            'mobile_srcset' => $this->srcset($this->mobile_image, true),
            'link'          => generateRoute($this)
        ];
    }

    private function imgObj(): array
    {
        return [
            'src'     => $this->linkImage($this->image, $this->position, 'biggest_size'),
            'loading' => $this->linkImage($this->image, $this->position, null, true),
            'error'   => $this->linkImage(null, $this->position),
        ];
    }

    protected function srcset(?string $image, bool $mobile = false): array
    {
        $srcset = [];
        if ($image) {
            $srcset = $this->linkImages($image, $this->position, true, $mobile);
        }

        return $srcset;
    }
}
