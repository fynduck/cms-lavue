<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Modules\UserGroup\Entities\UserGroup;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function registered(Request $request, User $user)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json(['verifyEmail' => true]);
        }

        return response()->json($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'name'     => 'required|max:255',
                'email'    => 'required|email:strict,dns|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \Modules\User\Entities\User
     */
    protected function create(array $data)
    {
        $username = $data['name'] . '_' . Str::random(2);
        return User::create(
            [
                'username' => Str::slug($username, '_'),
                'name'     => $data['name'],
                'email'    => $data['email'],
                'birthday' => now()->toDateTime(),
                'password' => bcrypt($data['password']),
                'group_id' => UserGroup::whereName(UserGroup::USERS)->value('id'),
            ]
        );
    }
}
