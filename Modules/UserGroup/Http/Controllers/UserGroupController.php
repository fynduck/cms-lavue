<?php

namespace Modules\UserGroup\Http\Controllers;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\UserGroup\Entities\Permission;
use Modules\UserGroup\Entities\UserGroup;
use Modules\UserGroup\Http\Requests\StoreGroupUserRequest;
use Modules\UserGroup\Transformers\UserGroupFormResource;
use Modules\UserGroup\Transformers\UserGroupListResource;

class UserGroupController extends AdminController
{
    protected $fileManagerRights = [
        'show'      => [
            'show',
            'getErrors',
            'performResize',
            'getDownload',
            'getCrop',
            'getCropimage',
            'getRename',
            'getResize',
            'getItems',
            'getFolders'
        ],
        'upload'    => [
            'upload',
            'getAddfolder',
        ],
        'getDelete' => [
            'getDelete'
        ]
    ];

    public function __construct()
    {
        $this->middleware('admin:view');
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $groups = UserGroup::paginate();

        return UserGroupListResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return bool
     */
    public function store(StoreGroupUserRequest $request)
    {
        $items = $request->get('items');
        $syncIds = [];
        foreach ($items as $name => $rights) {
            foreach ($rights as $right => $value) {
                if ($value) {
                    if ($name === 'unisharp') {
                        foreach ($this->fileManagerRights[$right] as $item) {
                            $permission = Permission::firstOrCreate(
                                [
                                    'name'   => $name,
                                    'rights' => $item
                                ]
                            );
                            $syncIds[] = $permission->id;
                        }
                    } else {
                        $permission = Permission::firstOrCreate(
                            [
                                'name'   => $name,
                                'rights' => $right
                            ]
                        );
                        $syncIds[] = $permission->id;
                    }
                }
            }
        }

        $itemAdd = UserGroup::create(
            [
                'name' => $request->get('name'),
            ]
        );
        $itemAdd->groupPermission()->sync($syncIds);

        return true;
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return UserGroupFormResource
     */
    public function show($id)
    {
        $item = UserGroup::find($id);

        $additional = [];

        if (!$item) {
            $item = new UserGroup();
        } else {
            $additional['rights'] = $item->groupPermission->mapToGroups(
                function ($item) {
                    return [$item['name'] => $item['rights']];
                }
            )->toArray();
        }

        //admin routes
        $routes = Route::getRoutes();
        $actionAdd = ['create', 'store'];
        $actionEdit = ['edit', 'update'];

        //exclude routes
        $exclude = ['dashboard'];

        //Filemanager
        $fileManagerPermissions = [
            'show',
            'upload',
            'getDelete'
        ];

        foreach ($routes as $route) {
            if (Str::contains($route->getPrefix(), 'admin')) {
                $name = $route->getName();
                $nameParse = explode('.', $name);
                $action = 'index';

                if (count($nameParse) > 2) {
                    if (array_key_exists(2, $nameParse)) {
                        $action = $nameParse[2];
                    } else {
                        $action = 'view';
                    }

                    if ($action == 'show' && $nameParse[0] != 'unisharp') {
                        continue;
                    }

                    if (in_array($action, $actionAdd)) {
                        $action = 'create';
                    }
                    if (in_array($action, $actionEdit)) {
                        $action = 'update';
                    }

                    //filemanager route
                    if ($nameParse[0] == 'unisharp') {
                        if (!in_array($action, $fileManagerPermissions)) {
                            continue;
                        }

                        $additional['routes'][$nameParse[0]][$action] = false;
                    } elseif ($action) {
                        $additional['routes'][$nameParse[0]][$action] = false;
                    }
                } elseif (count($nameParse) > 1 && !in_array($nameParse[0], $exclude)) {
                    if (array_key_exists(1, $nameParse)) {
                        $action = $nameParse[1];
                    } else {
                        $action = 'view';
                    }

                    if ($action == 'show') {
                        continue;
                    }

                    if (in_array($action, $actionAdd)) {
                        $action = 'create';
                    }
                    if (in_array($action, $actionEdit)) {
                        $action = 'edit';
                    }

                    $additional['routes'][$nameParse[0]][$action] = false;
                }

                if (!empty($additional['rights'][$nameParse[0]]) && array_search(
                        $action,
                        $additional['rights'][$nameParse[0]]
                    ) !== false) {
                    $additional['routes'][$nameParse[0]][$action] = true;
                }
            }
        }

        return (new UserGroupFormResource($item))->additional($additional);
    }

    /**
     * Update the specified resource in storage.
     * @param StoreGroupUserRequest $request
     * @param $id
     * @return bool
     */
    public function update(StoreGroupUserRequest $request, $id)
    {
        $items = $request->get('items');
        $syncIds = [];
        foreach ($items as $name => $rights) {
            foreach ($rights as $right => $value) {
                if ($value) {
                    if ($name === 'unisharp') {
                        foreach ($this->fileManagerRights[$right] as $item) {
                            $permission = Permission::firstOrCreate(
                                [
                                    'name'   => $name,
                                    'rights' => $item
                                ]
                            );
                            $syncIds[] = $permission->id;
                        }
                    } else {
                        $permission = Permission::firstOrCreate(
                            [
                                'name'   => $name,
                                'rights' => $right
                            ]
                        );
                        $syncIds[] = $permission->id;
                    }
                }
            }
        }

        $group = UserGroup::find($id);

        $group->name = $request->get('form')['name'];
        $group->save();

        $group->groupPermission()->sync($syncIds);

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function destroy($id)
    {
        $item = UserGroup::find($id);

        if (!$item->admin) {
            $item->groupPermission()->delete();

            return $item->delete();
        }
    }
}
