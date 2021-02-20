<?php

namespace Modules\User\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Friend;
use Modules\User\Entities\User;

trait UserTrait
{
    public function avatarPath()
    {
        $identify = $this->id ?? \Str::random(5);

        return 'user_' . $identify . '/avatars';
    }

    /**
     * Select all items
     * @param bool $paginate
     * @return mixed
     */
    static function getAll($paginate = true)
    {
        $query = User::where('email', '<>', 'admin@admin.com')->orderBy('updated_at', 'DESC');
        if ($paginate) {
            return $query->paginate(30);
        }

        return $query->get();
    }

    public function scopeFilters($query, Request $request)
    {
        if ($request->get('group')) {
            $query->where('group_id', $request->get('group'));
        }

        if ($request->get('q')) {
            $search = $request->get('q');
            $query->where(
                function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                }
            );
        }
    }

    public function getFriends()
    {
        return Friend::where(
            function ($query) {
                $query->where('user_id', $this->id)
                    ->orWhere('friend_id', $this->id);
            }
        );
    }

    public function accessRoomIds()
    {
        return DB::table('room_user')->where('user_id', $this->id)->pluck('room_id');
    }

    public static function getSizes()
    {
        return [
            self::XS => ['width' => 50, 'height' => null],
            self::SM => ['width' => 200, 'height' => 200],
            self::MD => ['width' => 300, 'height' => 300],
        ];
    }
}