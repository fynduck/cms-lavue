<?php

namespace Modules\Banner\Observers;

use Fynduck\FilesUpload\PrepareFile;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Services\BannerService;

class BannerObserver
{
    /**
     * Handle the banner "deleted" event.
     *
     * @param Banner $banner
     * @return void
     */
    public function deleted(Banner $banner)
    {
        if ($banner->image) {
            (new BannerService())->deleteImages($banner->image);
        }
        if ($banner->mobile_image) {
            (new BannerService())->deleteImages($banner->mobile_image);
        }
    }
}
