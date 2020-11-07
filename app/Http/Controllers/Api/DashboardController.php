<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.02.2016
 * Time: 10:45
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        return view('layouts.admin');
    }

    public function customAuth($id)
    {
        if (auth()->check() && auth()->user()->isAdmin)
            \Auth::loginUsingId($id);

        return redirect()->route('pages');
    }

    public function getAppData()
    {
        return [
            'appName'  => config('app.name'),
            'locale'   => config('app.locale'),
            'locales'  => config('app.locales'),
            'fallback' => config('app.fallback_locale')
        ];
    }

    public function transSlug(Request $request)
    {
        $slug = '';
        if ($request->get('txt'))
            $slug = \Str::slug($request->get('txt'));

        return response()->json($slug);
    }
}