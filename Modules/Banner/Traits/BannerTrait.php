<?php

namespace Modules\Banner\Traits;

use Illuminate\Http\Request;
use Modules\Banner\Entities\Banner;

trait BannerTrait
{
    public static function getPositions()
    {
        return [
            self::TOP     => 'Banner.top',
            self::CONTENT => 'Banner.content'
        ];
    }

    /**
     * Select banners by page_id
     * @param $page_id
     * @param $page_type
     * @param null $position
     * @param null $lang
     * @return mixed
     */
    public static function getByPageId($page_id, $page_type, $position = null, $lang = null)
    {
        $lang = is_null($lang) ? config('app.locale_id') : $lang;

        $query = Banner::leftJoin('banner_trans', 'banners.id', '=', 'banner_trans.banner_id')
            ->join('banner_show', 'banners.id', '=', 'banner_show.banner_id');

        if ($position)
            $query->where('position', $position);

        return $query->where('lang_id', $lang)
            ->where('banner_show.page_id', $page_id)
            ->where('banner_show.type_page', $page_type)
            ->where('active', 1)
            ->where('image', '!=', '')
            ->orderBy('priority')
            ->orderBy('updated_at', 'DESC')
            ->select(['banners.id', 'banners.page_id', 'banners.type_page', 'image', 'mobile_image', 'link', 'target', 'position',
                      'banner_trans.title', 'banner_trans.description']);
    }

    public function scopeBetweenDate($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_from');
            $q->orWhere('date_from', '<=', now()->toDateTimeString());
        })->where(function ($q) {
            $q->whereNull('date_to');
            $q->orWhere('date_to', '>=', now()->toDateTimeString());
        });
    }

    /**
     * @param $query
     * @param Request $request
     * @return mixed
     */
    public function scopeFilter($query, Request $request)
    {
        if ($request->get('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->get('q') . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->get('q') . '%');
            });
        }

        if ($request->get('active'))
            $query->where('active', $request->get('active'));

        if ($request->get('sortBy')) {
            $sort = 'ASC';
            if ($request->get('sortDesc'))
                $sort = 'DESC';
            $query->orderBy($request->get('sortBy'), $sort);
        }

        return $query;
    }

    /**
     * @return string[]
     */
    public static function targets(): array
    {
        return [
            '_self'   => '_self',
            '_blank'  => '_blank',
            '_parent' => '_parent',
            '_top'    => '_top'
        ];
    }
}
