<?php

namespace Modules\Banner\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Banner\Services\BannerService;

class BannerResource extends JsonResource
{
    private $bannerService;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->bannerService = new BannerService();
    }

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
            'mobile_srcset' => $this->srcset($this->mobile_image, true),
            'link'          => generateRoute($this)
        ];
    }

    private function imgObj(): array
    {
        return [
            'src'     => $this->bannerService->linkImage($this->image, $this->position, null, false, true),
            'loading' => $this->bannerService->linkImage($this->image, $this->position, null, true),
            'error'   => $this->bannerService->linkImage(null, $this->position),
        ];
    }

    protected function srcset(?string $image, bool $mobile = false): array
    {
        $srcset = [];
        if ($image) {
            $srcset = $this->bannerService->linkImages($image, $this->position, true, $mobile);
        }

        return $srcset;
    }
}
