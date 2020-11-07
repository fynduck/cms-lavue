<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SetGlobal;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Fynduck\FilesUpload\PrepareFile;
use Illuminate\Http\Request;
use Modules\Gallery\Services\GalleryService;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;
use Modules\Parameter\Entities\Parameter;
use Modules\Parameter\Transformers\ParameterUserResource;
use Modules\User\Entities\Blacklist;
use Modules\User\Entities\Friend;
use Modules\User\Entities\OnlineUsers;
use Modules\User\Entities\User;
use Modules\User\Entities\UserSettings;
use Modules\User\Http\Requests\SettingsRequest;
use Modules\User\Notifications\NotifyNewNotification;
use Modules\User\Services\FriendService;
use Modules\User\Services\UserService;
use Modules\User\Transformers\FriendsResource;
use Modules\User\Transformers\ProfileResource;
use Modules\User\Transformers\SettingsResource;
use Modules\User\Transformers\UserResource;
use Modules\UserGroup\Entities\UserGroup;

class FrontController extends Controller
{
    public function pageSettings()
    {
        $this->data['page'] = Page::getPageByMethod('settings');
        SetGlobal::setMeta($this->data, $this->data['page']);

        $urlsPage = PageTrans::getSlugs($this->data['page']->page_id);
        SetGlobal::setLanguagesMenu($this->data, $urlsPage);

        $this->data['breadcrumbs'][] = ['title' => trans('global.personal_cabinet'), 'url' => null];

        $this->data['day_on_site'] = now()->diffForHumans(auth()->user()->created_at, ['syntax' => CarbonInterface::DIFF_ABSOLUTE]);

        return view('user::cabinet.settings', $this->data);
    }

    public function settings()
    {
        $userInfo = auth()->user()->infoShow;
        $response = [
            'user'              => new SettingsResource(auth()->user()),
            'list_month'        => listOfMonth(),
            'additional_params' => ParameterUserResource::collection(Parameter::parameters()),
            'list_years'        => listYears(),
            'types_show'        => UserSettings::getWhomShow(),
            'info_show'         => [
                'show_email' => $userInfo->contains('field_name', 'email') ? $userInfo->firstWhere('field_name', 'email')->value : UserSettings::TO_ALL,
                'show_name'  => $userInfo->contains('field_name', 'name') ? $userInfo->firstWhere('field_name', 'name')->value : UserSettings::TO_ALL,
                'show_phone' => $userInfo->contains('field_name', 'phone') ? $userInfo->firstWhere('field_name', 'phone')->value : UserSettings::TO_ALL,
                'show_skype' => $userInfo->contains('field_name', 'skype') ? $userInfo->firstWhere('field_name', 'skype')->value : UserSettings::TO_ALL,
            ],
            'trans'             => [
                'add_profile_photo'    => trans('auth.add_profile_photo'),
                'crop'                 => trans('auth.crop'),
                'you_are_on_the_site'  => trans('auth.you_are_on_the_site'),
                'username'             => trans('auth.username'),
                'email'                => trans('auth.email'),
                'show'                 => trans('auth.show'),
                'name'                 => trans('auth.name'),
                'to_all'               => trans('auth.to_all'),
                'to_friends'           => trans('auth.to_friends'),
                'to_nobody'            => trans('auth.to_nobody'),
                'birthday'             => trans('auth.birthday'),
                'day'                  => trans('auth.day'),
                'month'                => trans('auth.month'),
                'year'                 => trans('auth.year'),
                'your_country'         => trans('auth.your_country'),
                'country_not_selected' => trans('auth.country_not_selected'),
                'your_city'            => trans('auth.your_city'),
                'city_not_selected'    => trans('auth.city_not_selected'),
                'number_phone'         => trans('auth.number_phone'),
                'skype'                => trans('auth.skype'),
                'save'                 => trans('global.save'),
            ],
        ];

        return response()->json($response);
    }

    public function updateSettings(SettingsRequest $request)
    {
        $user = auth()->user();
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->skype = $request->get('skype');
        $user->birthday = Carbon::createFromDate($request->get('year'), $request->get('month'), $request->get('day'))->toDateTime();
        $user->city_id = $request->get('city_id');

        if ($request->get('avatar')) {
            $nameAvatar = $user->id . '_' . $user->username;
            $user->avatar = PrepareFile::uploadBase64($user->avatarPath(), 'image', $request->get('avatar'), $nameAvatar, $user->avatar, User::getSizes());
        }

        $user->save();

        return response()->json(['message' => trans('auth.data_saved')]);
    }

    public function friendsRequest(Request $request, FriendService $friendService)
    {
        $response = [
            'message' => trans('global.friend_request_send')
        ];
        if ($request->get('friend_id') && auth()->check()) {
            $user = User::find($request->get('friend_id'));
            if ($user && !$friendService->checkIsFriends(auth()->id(), $request->get('friend_id'))) {
                $friendService->createUpdate(auth()->id(), $request->get('friend_id'));

                $user->notify(new NotifyNewNotification('friends'));
            }
        }

        return response()->json($response);
    }

    public function requestAddFriend(Request $request, FriendService $friendService)
    {
        $response = [
            'message' => trans('global.remove_from_friends')
        ];
        if ($request->get('user_id') && $request->get('action')) {
            $response['user'] = new UserResource(User::find($request->get('user_id')));
            if ($request->get('action') == 'accept') {
                $friendService->confirmFriendsRequest($request);
            } elseif ($request->get('action') == 'reject') {
                Friend::where('user_id', $request->get('user_id'))->where('friend_id', auth()->id())->delete();
            }
        }

        return response()->json($response);
    }

    public function getNotifications(UserService $userService)
    {
        $response = [];
        $response['count_request_friends'] = Friend::where('friend_id', auth()->id())
            ->where('status', Friend::REQUEST)->count();

        $response['not_read_messages'] = $userService->notReadMessages();

        $userService->setUpdateOnline();

        return response()->json($response);
    }

    public function getUsers(Request $request)
    {
        $userQuery = User::verified()->where('group_id', UserGroup::groupIdByName(UserGroup::USERS));
        if (!$request->get('leaders'))
            $userQuery->orderBy('id', 'desc');

        $users = $userQuery->simplePaginate();

        $additional = [
            'trans' => [
                'show_more'    => trans('global.show_more'),
                'send_message' => trans('global.send_message'),
                'title'        => $request->get('leaders') ? trans('global.community_leaders') : trans('global.beginners_on_site')
            ]
        ];

        return UserResource::collection($users)->additional($additional);
    }

    public function friends()
    {
        $this->data['page'] = Page::getPageByMethod('friends');
        SetGlobal::setMeta($this->data, $this->data['page']);

        $this->data['breadcrumbs'][] = ['title' => trans('global.personal_cabinet'), 'url' => null];

        return view('user::cabinet.friends', $this->data);
    }

    public function getFriends(Request $request)
    {
        $query = auth()->user()->getFriends();

        if ($request->get('friends_request')) {
            $query->where('status', Friend::REQUEST)
                ->where('user_id', '<>', auth()->id());
        } else {
            $query->where('status', Friend::CONFIRMED);
        }

        $friends = $query->paginate();

        $totalRequest = auth()->user()->getFriends()
            ->where('user_id', '<>', auth()->id())
            ->where('status', Friend::REQUEST)->count();

        $totalConfirmed = auth()->user()->getFriends()->where('status', Friend::CONFIRMED)->count();

        $response = [
            'items'         => FriendsResource::collection($friends),
            'total'         => $totalConfirmed,
            'total_request' => $totalRequest,
            'pagination'    => paginateApi($friends),
            'trans'         => [
                'show_more'          => trans('global.show_more'),
                'send_message'       => trans('global.send_message'),
                'request_to_friends' => trans('global.request_to_friends'),
                'total_friends'      => trans('global.total_friends'),
            ]
        ];

        return response()->json($response);
    }

    public function setUserActive(UserService $userService)
    {
        $userService->setUpdateOnline();

        return response()->json(['message' => 'heartbeat']);
    }

    public function getLastTimesIsOnline($user_id)
    {
        $dateTimeOnline = OnlineUsers::where('user_id', $user_id)->value('online_at');
        if (is_null($dateTimeOnline))
            $dateTimeOnline = User::find($user_id)->value('created_at');

        $timeOnline = Carbon::parse($dateTimeOnline);

        if (now()->diffInSeconds($timeOnline) < 60)
            $timeOnline = now()->diffInSeconds($timeOnline);
        else
            $timeOnline = $timeOnline->diffForHumans();

        return response()->json(['last_online' => $timeOnline]);
    }

    public function profile(GalleryService $galleryService, $slug, $gallery_slug = null)
    {
        $this->data['user'] = User::where('slug', $slug)->firstOrFail();

        $this->data['page'] = Page::getPageByMethod('profile');
        if (!$this->data['page'])
            abort(404);

        SetGlobal::setMeta($this->data, $this->data['page']);
        $this->data['source_gallery_image'] = false;
        SetGlobal::setBreadcrumbs($this->data, route('user-profile', $this->data['user']->slug), $this->data['user']->name, false);
        if ($gallery_slug) {
            $query = $galleryService->conditionShowGallery($this->data['user']->id, $this->request->get('token'));
            $gallery = $query->where('slug', $gallery_slug)->firstOrfail(['galleries.id', 'gallery_trans.title']);
            SetGlobal::setBreadcrumbs($this->data, '', $gallery->title, false);
            $this->data['source_gallery_image'] = route('gallery-images', ['gallery_id' => $gallery->id]);
        }

        return view('user::profile', $this->data);
    }

    public function infoProfile($user_id, UserService $userService)
    {
        $user = User::findOrFail($user_id);

        return response()->json(new ProfileResource($user));
    }

    public function mutualFriends(Request $request)
    {
        $response = [
            'items' => []
        ];
        if ($request->get('user_id')) {
            $user = User::find($request->get('user_id'));
            $myFriends = auth()->user()->getFriends()
                ->where('status', Friend::CONFIRMED)
                ->where('user_id', '<>', $user->id)
                ->where('friend_id', '<>', $user->id)
                ->selectRaw('case when user_id != ' . auth()->id() . ' THEN user_id ELSE friend_id END as friend_id')
                ->get();

            $userFriends = $user->getFriends()
                ->where('status', Friend::CONFIRMED)
                ->where('user_id', '<>', auth()->id())
                ->where('friend_id', '<>', auth()->id())
                ->selectRaw('case when user_id = ' . auth()->id() . ' THEN user_id ELSE friend_id END as friend_id')
                ->get();

            $friendIds = $myFriends->intersect($userFriends);

            $mutualFriends = User::whereIn('id', $friendIds->pluck('friend_id')->toArray())->get();

            $response['items'] = UserResource::collection($mutualFriends);
        }

        return response()->json($response);
    }

    public function addBlacklist(Request $request)
    {
        $response = [
            'status' => false
        ];
        if ($request->get('user_id') && auth()->check()) {
            $blacklist = Blacklist::firstOrCreate([
                'user_id'       => auth()->id(),
                'user_id_block' => $request->get('user_id')
            ]);

            $blacklist->increment('count');

            $response['status'] = true;
        }

        return response()->json($response);
    }
}
