<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Modules\User\Entities\User;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\User\Entities\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, User $user)
    {
        if (!URL::hasValidSignature($request)) {
            return response()->json(
                [
                    'status' => trans('passwords.invalid'),
                ],
                400
            );
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(
                [
                    'status' => trans('passwords.already_verified'),
                ],
                400
            );
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json(
            [
                'status' => trans('passwords.verified'),
            ]
        );
    }

    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $this->validate($request, ['email' => 'required|email:strict,dns']);

        $user = User::where('email', $request->email)->first();

        if (is_null($user)) {
            throw ValidationException::withMessages(
                [
                    'email' => [trans('passwords.user')],
                ]
            );
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages(
                [
                    'email' => [trans('passwords.already_verified')],
                ]
            );
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => trans('passwords.sent')]);
    }
}
