<?php

namespace Modules\Module\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class ModuleController extends AdminController
{
    public function __construct()
    {
        $this->middleware('admin:view');
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $statusModules = json_decode(file_get_contents(base_path('modules_statuses.json')), true);
        $modules = [];
        foreach (Module::getCached() as $module) {
            $modules[] = [
                'name'        => $module['name'],
                'active'      => array_key_exists($module['name'], $statusModules) && $statusModules[$module['name']] ? 1 : 0,
                'permissions' => [
                    'destroy' => checkModulePermission('module', 'destroy')
                ]
            ];
        }

        return response($modules);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $name
     * @return false
     */
    public function update(Request $request, string $name): bool
    {
        $module = Module::find($name);
        if ($module) {
            $command = 'module:' . ($request->get('status') ? 'enable ' : 'disable ');

            Artisan::call("$command$name");

            return true;
        }

        return false;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
