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
                ->where('image', '!=', '')->get(['id', 'image', 'mobile_image']);
            foreach ($banners as $banner) {
                $encode = 'webp';
                $bannerService->deleteImages($banner->image);
                if ($banner->image) {
                    $path = Storage::get(Banner::FOLDER_IMG . '/' . $banner->image);
                    $explodeImgName = explode('.', $banner->image);
                    array_pop($explodeImgName);
                    $newName = $encode . '_' . $explodeImgName[0] . '.' . $encode;
                    $this->generateBannerImages($path, $data, $newName, $encode);
                    $banner->image = $newName;
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
                    $explodeImgName = explode('.', $banner->mobile_image);
                    array_pop($explodeImgName);
                    $newName = $encode . '_' . $explodeImgName[0] . '.' . $encode;
                    $this->generateBannerImages($path, $imgSettings, $newName, $encode);

                    $banner->mobile_image = $newName;
                }
                $banner->save();
            }
        }
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $image
     * @param string|null $encode
     */
    private function generateBannerImages(string $path, array $data, string $image, ?string $encode = null)
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
            ->setEncodeFormat($encode)
            ->save($data['resizeMethod']);
    }
}
