<?php


namespace Modules\User\Services;

use Illuminate\Http\Request;
use Modules\Chat\Services\ChatService;
use Modules\User\Entities\Friend;

class FriendService
{
    public function createUpdate($user_id, $friend_id, $status = Friend::REQUEST)
    {
        return Friend::updateOrCreate(
            [
                'user_id'   => $user_id,
                'friend_id' => $friend_id
            ],
            [
                'status' => $status
            ]
        );
    }

    public function checkIsFriends($user_id, $friend_id)
    {
        return Friend::where('user_id', $user_id)
            ->where('friend_id', $friend_id)
            ->orWhere(function ($query) use ($user_id, $friend_id) {
                $query->where('friend_id', $user_id)
                    ->where('user_id', $friend_id);
            })->latest()->first();
    }

    public function confirmFriendsRequest(Request $request)
    {
        $friend = Friend::where('user_id', $request->get('user_id'))
            ->where('friend_id', auth()->id())->first();

        if ($friend) {
            $friend->status = Friend::CONFIRMED;
            $friend->save();

            $ids = [
                $friend->user_id, $friend->friend_id
            ];
            $dataRoom = [
                'ids' => $ids
            ];
            (new ChatService())->checkExistRoom($ids, $dataRoom);
        }
    }
}