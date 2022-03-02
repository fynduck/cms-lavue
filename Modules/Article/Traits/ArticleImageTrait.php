<?php

namespace Modules\Article\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;

trait ArticleImageTrait
{
    /**
     * @param string $imageName
     * @return string
     */
    public function getOriginalImageName(string $imageName): string
    {
        $explodedImage = explode('_', $imageName);
        $extension = $explodedImage[0];

        if (in_array($extension, Article::FORMATS)) {
            $imageName = implode('_', $explodedImage);
            $explodedImage = explode('.', $imageName);

            //Extract extension
            array_pop($explodedImage);

            $imageName = implode('.', $explodedImage) . '.' . $extension;
        }

        return $imageName;
    }

    /**
     * @param string $name
     * @param string|null $encode
     * @return string
     */
    public function setExtensionByEncode(string $name, string $encode = null): string
    {
        $explodeExtension = explode('.', $name);
        $originalExtension = explode('_', $name)[0];

        if (!$encode) {
            if (in_array($originalExtension, Article::FORMATS)) {
                //Extract extension
                array_pop($explodeExtension);
                $name = implode('.', $explodeExtension) . '.' . $originalExtension;
            }
        } elseif (end($explodeExtension) !== $encode && in_array($originalExtension, Article::FORMATS)) {
            //Extract extension
            array_pop($explodeExtension);
            $name = implode('.', $explodeExtension) . '.' . $encode;
        }

        return $name;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isBase64(string $file): bool
    {
        return (bool)preg_match("/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).base64,.*/", $file);
    }

    /**
     * @param $imageSettings
     * @return array
     */
    public function prepareImgParams($imageSettings): array
    {
        $data['sizes'] = [];
        $data['resizeMethod'] = '';
        $data['greyscale'] = false;
        $data['blur'] = 1;
        $data['brightness'] = 0;
        $data['background'] = null;
        $data['optimize'] = false;
        $data['encode'] = null;

        if ($imageSettings && !empty($imageSettings->data['sizes'])) {
            $data['sizes'] = $imageSettings->data['sizes'];
            $data['resizeMethod'] = $imageSettings->data['action'];
            if (!empty($imageSettings->data['greyscale'])) {
                $data['greyscale'] = $imageSettings->data['greyscale'];
            }
            if (!empty($imageSettings->data['blur'])) {
                $data['blur'] = $imageSettings->data['blur'];
            }
            if (!empty($imageSettings->data['brightness'])) {
                $data['brightness'] = $imageSettings->data['brightness'];
            }
            if (!empty($imageSettings->data['background'])) {
                $data['background'] = $imageSettings->data['background'];
            }
            if (!empty($imageSettings->data['optimize'])) {
                $data['optimize'] = $imageSettings->data['optimize'];
            }
            if (!empty($imageSettings->data['encode'])) {
                $data['encode'] = $imageSettings->data['encode'];
            }
        }

        return $data;
    }

    /**
     * Get image link by size
     * @param string|null $image
     * @param string $type
     * @param null $size
     * @param bool $first
     * @return string
     */
    public function linkImage(?string $image, string $type, $size = null, bool $first = false): string
    {
        if (!$image) {
            return asset('img/placeholder.jpg');
        }

        if (!$size && !$first) {
            $image = $this->getOriginalImageName($image);
            return asset('storage/' . Article::FOLDER_IMG . '/' . $image);
        }

        $settings = Cache::remember(
            "article_{$type}_sizes",
            now()->addDay(),
            function () use ($type){
                return ArticleSettings::where('name', "{$type}_sizes")->first();
            }
        );

        if ($settings && !empty($settings->data['sizes'])) {
            $sortedSizes = collect($settings->data['sizes'])->sortBy('width')->sortBy('height');
            if ($first) {
                return asset('storage/' . Article::FOLDER_IMG . '/' . $sortedSizes->first()['name'] . '/' . $image);
            }

            if (array_key_exists($size, $settings->data['sizes'])) {
                return asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $image);
            } else {
                $image = $this->getOriginalImageName($image);
                return asset('storage/' . Article::FOLDER_IMG . '/' . $sortedSizes->last()['name'] . '/' . $image);
            }
        }

        return asset('storage/' . Article::FOLDER_IMG . '/' . $image);
    }

    /**
     * Get all links with image size
     * @param $image
     * @param string $type
     * @param bool $srcset
     * @return array
     */
    public function linkImages($image, string $type, $srcset = true): array
    {
        $images = [];

        $settings = Cache::remember(
                    "article_{$type}_sizes",
            now()->addDay(),
            function () use ($type){
                return ArticleSettings::where('name', "${$type}_sizes")->first();
            }
        );

        if ($image && $settings && !empty($settings->data['sizes'])) {
            $sortedSizes = collect($settings->data['sizes'])->sortByDesc('width')->sortByDesc('height');
            foreach ($sortedSizes as $size => $sizes) {
                $src = asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $image);
                if ($srcset) {
                    $src .= ' ' . $sizes['width'] . 'w';
                }

                $images[] = $src;
            }
        }

        return $images;
    }
}