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

    private $position;

    /**
     * Create a new job instance.
     * @param string $position
     * @param string $sizeName
     */
    public function __construct(string $position, string $sizeName)
    {
        $this->position = $position;
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
            $banners = Banner::where('position', $this->position)
                ->where('image', '!=', '')->get(['image', 'mobile_image']);

            foreach ($banners as $banner) {
                if ($banner->image) {
                    $bannerService->deleteImages($banner->image);
                    $imageName = $bannerService->getOriginalImageName($banner->image);
                    $bannerService->deleteImages($imageName);
                    $path = Storage::get(Banner::FOLDER_IMG . '/' . $imageName);
                    $sizeSaveName = $bannerService->setExtensionByEncode($imageName, $data['encode']);
                    $bannerService->generateImageSizes($path, $data, $sizeSaveName);

                    $bannerService->generateReserveImg($data, $imageName, false);

                    if ($banner->image !== $sizeSaveName) {
                        $banner->image = $sizeSaveName;
                    }
                }

                if ($banner->mobile_image) {
                    $bannerService->deleteImages($banner->mobile_image);
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
                    $imageName = $bannerService->getOriginalImageName($banner->mobile_image);
                    $bannerService->deleteImages($imageName);
                    $path = Storage::get(Banner::FOLDER_IMG . '/' . $imageName);
                    $sizeSaveName = $bannerService->setExtensionByEncode($imageName, $data['encode']);
                    $bannerService->generateImageSizes($path, $imgSettings, $sizeSaveName);

                    $bannerService->generateReserveImg($data, $imageName, false);

                    if ($banner->mobile_image !== $sizeSaveName) {
                        $banner->mobile_image = $sizeSaveName;
                    }
                }

                $banner->save();
            }
        }
    }
}
