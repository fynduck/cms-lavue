<?php

namespace Modules\Banner\Jobs;

use Fynduck\FilesUpload\ManipulationImage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\BannerSettings;
use Modules\Banner\Services\BannerService;

class RegenerateImageSizes implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $sizeName;

    /**
     * Create a new job instance.
     * @param string $sizeName
     */
    public function __construct(string $sizeName)
    {
        $this->sizeName = $sizeName;
    }

    /**
     * Execute the job.
     *
     * @param BannerService $bannerService
     * @return void
     */
    public function handle(BannerService $bannerService)
    {
        $imageSettings = BannerSettings::where('name', $this->sizeName)->first();
        if ($imageSettings) {
            $data = $bannerService->prepareImgParams($imageSettings);
            $banners = Banner::where('image', '!=', '')->get(['image', 'mobile_image']);
            foreach ($banners as $banner) {
                $bannerService->deleteImages($banner->image);
                if ($banner->image) {
                    $path = Storage::get(Banner::FOLDER_IMG . '/' . $banner->image);
                    $this->generateBannerImages($path, $data, $banner->image);
                }

                if ($banner->mobile_image) {
                    $imgSettings = $data;
                    $mobileSizes = [];
                    foreach ($imageSettings->data['sizes'] as $key => $size) {
                        if ($size['mobile']) {
                            $mobileSizes[$key] = $size;
                        }
                    }
                    if ($mobileSizes) {
                        $imgSettings['sizes'] = $mobileSizes;
                    }
                    $path = Storage::get(Banner::FOLDER_IMG . '/' . $banner->mobile_image);
                    $bannerService->deleteImages($banner->mobile_image);
                    $this->generateBannerImages($path, $imgSettings, $banner->mobile_image);
                }
            }
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $image
     */
    private function generateBannerImages(string $path, array $data, string $image)
    {
        ManipulationImage::load($path)
            ->setSizes($data['sizes'])
            ->setName($image)
            ->setFolder(Banner::FOLDER_IMG)
            ->setGreyscale($data['greyscale'])
            ->setBlur($data['blur'])
            ->setBrightness($data['brightness'])
            ->setBackground($data['background'])
            ->setOptimize($data['optimize'])
            ->save($data['resizeMethod']);
    }
}
