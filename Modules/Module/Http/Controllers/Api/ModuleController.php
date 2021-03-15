<?php

namespace Modules\Module\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Modules\Module\Transformers\ModuleRespurce;
use Nwidart\Modules\Facades\Module;

class ModuleController extends Controller
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
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('module::create');
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('module::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('module::edit');
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
