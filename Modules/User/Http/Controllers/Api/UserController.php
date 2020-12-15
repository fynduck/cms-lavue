<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Transformers\UserFormResource;
use Modules\User\Transformers\UserListResource;
use Modules\UserGroup\Entities\UserGroup;

class UserController extends Controller
{
    protected $groups;

    public function __construct()
    {
        $this->middleware('admin');

        $this->groups = \Cache::remember('list_user_groups', now()->addHour(), function () {
            return UserGroup::whereAdmin(false)->get()->pluck('name', 'id');
        });
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $users = User::whereNotIn('email', ['admin@admin.com', 'andrei@glavan.md'])
            ->filters($request)->paginate();

        $additional = [
            'groups' => $this->groups
        ];

        return UserListResource::collection($users)->additional($additional);
    }

    /**
     * Store a newly created resource in storage.
     * @param UpdateUserRequest $request
     * @return bool
     */
    public function store(UpdateUserRequest $request)
    {
        User::create([
            'username' => $request->get('username'),
            'name'     => $request->get('name'),
            'email'    => $request->get('email'),
            'group_id' => (int)$request->get('group_id') !== 1 ? $request->get('group_id') : 2,
            'password' => bcrypt($request->get('password') ?? Str::random(15)),
            'token'    => Str::random(64)
        ]);

        return true;
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return UserFormResource
     */
    public function show($id)
    {
        $item = User::find($id);

        if (!$item)
            $item = new User();

        $additional = [
            'groups' => $this->groups
        ];

        return (new UserFormResource($item))->additional($additional);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return bool
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->group_id = (int)$request->get('group_id') !== 1 ? $request->get('group_id') : 2;

        return $user->save();
    }

    /**
     * Remove the specified resource from storage.
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        return $user->delete();
    }
}