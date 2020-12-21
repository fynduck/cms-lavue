<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class FrontController extends Controller
{
    public function getAppData()
    {
        return [
            'appName'  => config('app.name'),
            'locale'   => config('app.locale'),
            'locales'  => config('app.locales'),
            'fallback' => config('app.fallback_locale')
        ];
    }
}
