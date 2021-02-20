<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Carbon\Carbon;

Broadcast::channel(
    'Modules.User.Entities.User.{id}',
    function ($user, $id) {
        return (int)$user->id === (int)$id;
    }
);

Broadcast::channel(
    'online',
    function ($user) {
        return [
            'id'       => $user->id,
            'username' => $user->username,
            'city'     => $user->getCityTrans()->lang()->value('title'),
            'age'      => Carbon::parse($user->birthday)->age,
            'avatar'   => getUserAvatar($user->avatarPath(), $user->avatar, [100, 100])
        ];
    }
);

Broadcast::channel(
    'room.{id}',
    function ($user, $id) {
        return $user->rooms()->where('rooms.id', $id)->count();
    }
);
