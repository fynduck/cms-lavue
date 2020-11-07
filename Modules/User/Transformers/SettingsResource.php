<?php

namespace Modules\User\Transformers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Entities\User;

class SettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'username'    => $this->username,
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'skype'       => $this->skype,
            'birthday'    => $this->birthday,
            'city_id'     => (string)$this->city_id,
            'avatar_src'  => getUserAvatar($this->avatarPath() . '/' . User::MD, $this->avatar, [270, 270]),
            'day_on_site' => now()->diffForHumans($this->created_at, ['syntax' => CarbonInterface::DIFF_ABSOLUTE]),
            'day'         => Carbon::parse($this->birthday)->day,
            'month'       => Carbon::parse($this->birthday)->month,
            'year'        => Carbon::parse($this->birthday)->year,
            'country_id'  => $this->getCity ? (string)$this->getCity->getCountry->id : null,
        ];
    }
}
