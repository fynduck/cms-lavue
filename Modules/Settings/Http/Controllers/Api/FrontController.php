<?php

namespace Modules\Settings\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Settings;

class FrontController
{
    public function settings(Request $request): JsonResponse
    {
        $defaultKeys = [
            'name_site',
            'contact_email',
            'contact_phone',
            'logo'
        ];

        if ($request->get('key')) {
            $item = Settings::where('key', $request->get('key'))->value('value');
            if ($item && $request->get('key') == 'logo') {
                $item = asset('storage/' . Settings::FOLDER_IMG . '/' . $item->logo);
            }

            return response()->json($item);
        }

        $defaultSettings = Settings::whereIn('key', $defaultKeys)
            ->where(
                function ($query) {
                    $query->where('lang', config('app.locale_id'))
                        ->orWhere('lang', 0);
                }
            )->get(['key', 'value'])
            ->each(
                function ($item) {
                    if ($item->key == 'logo' && $item->value) {
                        $item->value = asset('storage/' . Settings::FOLDER_IMG . '/' . $item->value);
                    }
                }
            );

        return response()->json($defaultSettings);
    }
}