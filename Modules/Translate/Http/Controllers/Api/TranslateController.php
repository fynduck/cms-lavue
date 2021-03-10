<?php

namespace Modules\Translate\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Modules\Translate\Http\Requests\TranslateValidate;
use Nwidart\Modules\Facades\Module;

class TranslateController extends AdminController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Contacts page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data['modules'] = [];
        foreach (Module::allEnabled() as $module) {
            $data['modules'][] = $module->getName();
        }

        return response()->json($data);
    }

    /**
     * @param TranslateValidate $request
     * @return mixed
     */
    public function store(TranslateValidate $request)
    {
        $module = Module::find($request->get('module'));

        if (!$module) {
            abort(422, 'Invalid data');
        }

        foreach ($request->get('files') as $lang => $items) {
            $content = collect($items)->pluck('value', 'slug');
            if ($content->count()) {
                File::put($module->getPath() . '/Resources/lang/' . $lang . '.json', $content->toJson(JSON_UNESCAPED_UNICODE));
            }
        }

        return true;
    }

    public function show(string $module)
    {
        $module = Module::find($module);

        if (!$module) {
            return null;
        }

        $data['files'] = [];
        $files = File::files($module->getPath() . '/Resources/lang');
        foreach ($files as $file) {
            $fileName = str_replace('.' . $file->getExtension(), '', $file->getFilename());
            foreach (json_decode($file->getContents(), true) as $key => $value) {
                $data['files'][$fileName][] = [
                    'slug'  => $key,
                    'value' => $value
                ];
            }
        }

        return response()->json($data);
    }
}
