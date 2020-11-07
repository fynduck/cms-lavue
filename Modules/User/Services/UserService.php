<?php

namespace Modules\User\Services;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\Chat\Entities\Message;
use Modules\Parameter\Entities\Parameter;
use Modules\ParameterValue\Entities\ParameterValue;
use Modules\User\Entities\Friend;
use Modules\User\Entities\OnlineUsers;
use Modules\User\Entities\User;
use Modules\User\Entities\UserParams;
use Modules\User\Entities\UserSettings;

class UserService
{
    public function parsePhoneByCountry($phone, $country_iso = 'ru')
    {
        try {
            $number = PhoneNumber::parse('+' . $phone)->formatForCallingFrom($country_iso);
        } catch (PhoneNumberParseException $e) {
            $number = $phone;
        };

        return $number;
    }
}
