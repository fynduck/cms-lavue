<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 24.05.2018
 * Time: 22:32
 */

use Illuminate\Support\Facades\Cache;
use Modules\Article\Entities\ArticleTrans;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;

/**
 * Parse show views with k
 * @param $count
 * @return string
 */
function viewsShow($count)
{
    if ($count > 999) {
        $count = round($count * 1 / 1000, PHP_ROUND_HALF_UP) . 'k';
    }

    return $count;
}

function getTimeBy30()
{
    $clock = 0;
    $result = [];
    for ($i = 0, $k = 0; $i < 48; $i++) {
        $value = $k / 60;
        if ($k % 60 == 0) {
            $clock = $value;
            $result[$k] = $value . ":00";
        } else {
            $result[$k] = $clock . ":30";
        }
        $k += 30;
    }

    return $result;
}

function getNameWeek($week, $short = false)
{
    $array = [
        'mon' => 'Понедельник',
        'tue' => 'Вторник',
        'wed' => 'Среда',
        'thu' => 'Четверг',
        'fri' => 'Пятница',
        'sat' => 'Суббота',
        'sun' => 'Воскресенье'
    ];

    if ($short) {
        return isset($array[$week]) ? Str::limit($array[$week], 2, '') : $week;
    }

    return isset($array[$week]) ? $array[$week] : $week;
}

function setNumberWeek($week)
{
    $array = [
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thu' => 4,
        'fri' => 5,
        'sat' => 6,
        'sun' => 7
    ];

    return $array[$week];
}

function showWorkDays($days)
{
    sort($days);
    $first = array_shift($days);
    $last = end($days);
    $array = [
        1 => 'Пн',
        2 => 'Вт',
        3 => 'Ср',
        4 => 'Чт',
        5 => 'Пт',
        6 => 'Сб',
        7 => 'Вс'
    ];

    $result = $array[$first];

    if ($last) {
        $result .= '-' . $array[$last];
    }

    return $result;
}

function convertIntToTimeString($int)
{
    $hours = (string)floor($int / 60);
    $minutes = (string)$int % 60;
    if ($minutes == '0') {
        $minutes = '00';
    }

    return "$hours:$minutes";
}

function convertIntMinSecToString(int $number)
{
    return $number < 10 ? '0' . $number : $number;
}

function generateRoute($item, $urlsPages = null)
{
    if (!$urlsPages) {
        $urlsPages = Cache::remember(
            'urls_pages_' . config('app.locale_id'),
            now()->addHours(5),
            function () {
                return Page::getSlugAllStaticPages()->toArray();
            }
        );
    }

    if (!$item->link && $item->trans) {
        $link = $item->trans->link ?? $item->trans->link;
    } else {
        $link = $item->link;
    }

    /**
     * Get urls pages by group
     */
    if ($item->type_page) {
        switch ($item->type_page) {
            case 'page':
                $pageSlug = PageTrans::getByPageId($item->page_id)->value('slug');
                $params = [
                    count(config('app.locales')) > 1 ? ($item->lang_id ? config(
                        'app.locales.' . $item->lang_id . '.slug'
                    ) : config('app.locale')) : null,
                    $pageSlug
                ];
                $link = '/' . implode('/', $params);
                break;
            case 'article':
            case 'articles':
                $articlesTrans = ArticleTrans::where('article_id', $item->page_id)->lang()->first(['article_id', 'slug']);
                $params = [
                    count(config('app.locales')) > 1 ? config('app.locale') : null,
                    $urlsPages[$articlesTrans->getArticle->type],
                    $articlesTrans->slug
                ];
                $link = '/' . implode('/', $params);
                break;
        }
    }

    return $link ?? '';
}

function placeholder($width = 150, $height = null, $text = null, $bg = null, $color = null)
{
//    if (($width == $height || is_null($height)) && ($width <= 320 && $height <= 320))
//        $placeholder = asset('img/placeholder_avatar.png');
//    else
    $placeholder = 'https://via.placeholder.com/' . $width;

    if ($height && $height != $width) {
        $placeholder .= 'x' . $height;
    }

    if ($color) {
        $placeholder .= '/' . $color;
    }

    if ($bg) {
        $placeholder .= '/' . $bg;
    }

    if ($text) {
        $placeholder .= '?text=' . $text;
    }

    return $placeholder;
}

function getUserAvatar($folder, $avatar, array $size = [30, 30])
{
    if (!$avatar) {
        return placeholder($size[0], $size[1]);
    }

    return asset('storage/' . $folder . '/' . $avatar);
}

function lazyObj($src, $loading = 'img/loading.jpg', $error = 'img/loading.jpg')
{
    $data = [
        'src' => $src,
    ];
    if ($loading) {
        $data['loading'] = asset($loading);
    }

    if ($error) {
        $data['error'] = asset($error);
    }

    return $data;
}

function cut_text($text, $limit = 160)
{
    return \Illuminate\Support\Str::limit($text, $limit);
}

function slug_text($text)
{
    return \Illuminate\Support\Str::slug($text);
}
