<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class VerifyEmailException extends ValidationException
{
    /**
     * @param  \Modules\User\Entities\User $user
     * @return static
     */
    public static function forUser($user)
    {
        return static::withMessages([
            'email' => trans('passwords.must_verify', [
                'linkOpen' => '<a href="/email/resend?email='.urlencode($user->email).'">',
                'linkClose' => '</a>',
            ]),
        ]);
    }
}
