<?php
/**
 * Created by PhpStorm.
 * User: stass
 * Date: 14.05.2017
 * Time: 14:23
 */

namespace Modules\Settings\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Settings\Entities\Pagination;
use Modules\Settings\Entities\Settings;
use Modules\Settings\Entities\Social;

class SettingsService
{
    public static function addUpdateSocials($items)
    {
        foreach ($items as $item) {

            $social = Social::updateOrCreate([
                'name' => $item['name'],
            ], [
                    'url'        => $item['url'],
                    'class_icon' => $item['class_icon'],
                    'priority'   => $item['priority'],
                ]
            );

            if ($social == false)
                return back()->withErrors(trans('admin.data_not_save'));
        }
    }

    public static function addUpdatePagination(Request $request)
    {
        foreach ($request->all() as $item) {
            Pagination::updateOrCreate([
                'id' => $item['id'] ?? null,
            ], [
                    'on'      => $item['on'],
                    'for'     => $item['for'],
                    'value'   => $item['value'],
                    'user_id' => Auth::id()
                ]
            );
        }
    }

    public static function formatShowSettings(): array
    {
        $settings = [];
        Settings::all()->each(function ($item) use (&$settings) {
            $value = $item['value'];
            if ($item['key'] == 'logo' && $item['value']) {
                $value = asset('storage/' . Settings::FOLDER_IMG . '/' . $item['value']);
            }
            $settings[$item['lang']][$item['key']] = $value;
        });

        $check = $settings;
        unset($check[0]);

        if (count($check) != count(config('app.locales'))) {
            $locales = config('app.locales');
            foreach ($check as $lang_id => $item)
                unset($locales[$lang_id]);

            foreach ($locales as $lang_id => $locale) {
                $settings[$lang_id] = (object)[];
            }
        }

        if (!array_key_exists(0, $settings)) {
            $settings[0] = (object)[];
        }

        return $settings;
    }
}
