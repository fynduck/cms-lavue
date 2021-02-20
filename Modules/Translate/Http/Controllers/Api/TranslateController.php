<?php

namespace Modules\Translate\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class TranslateController extends AdminController
{

    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Contacts page
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', config('app.locale'));

        $data = [
            'locales' => config('app.locales')
        ];

        $path = resource_path('lang/' . $locale);
        $files = File::allFiles($path);
        foreach ($files as $file) {
            $data['files'][] = str_replace('.php', '', $file->getFilename());
        }

        $file_name = $request->get('file_name', $data['files'][0]);
        $translates = trans($file_name, [], $locale);

        foreach ($translates as $key => $item) {
            $data['items'][] = [
                'slug'  => $key,
                'trans' => $item
            ];
        }

        $data['locale'] = $locale;

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function store(Request $request)
    {
        $content = '';
        if ($request->get('items')) {
            $content = $this->arrayToString($request->get('items'));
        }

        $file = $request->get('file_name');
        $locale = $request->get('locale');

        $content = "<?php \nreturn [\n$content];";

        $path = resource_path('lang/' . $locale . '/' . $file . '.php');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0775);
        }

        file_put_contents($path, $content);

        return true;
    }

    protected function arrayToString(array $array): string
    {
        $content = '';
        foreach ($array as $key => $value) {
            if (isset($value['trans']) && isset($value['slug'])) {
                $key = $value['slug'];
                $value = $value['trans'];
            }

            if (is_array($value)) {
                $content .= "'$key' => [\n" . $this->arrayToString($value) . "],\n";
            } else {
                $content .= "'$key' => '$value',\n";
            }
        }

        return $content;
    }
}
