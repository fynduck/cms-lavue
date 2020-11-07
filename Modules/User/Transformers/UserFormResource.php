<?php

namespace Modules\User\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFormResource extends JsonResource
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
            'username' => $this->username,
            'name'     => $this->name,
            'email'    => $this->email,
            'birthday' => Carbon::parse($this->birthday)->format('Y-m-d'),
            'phone'    => $this->phone,
            'password' => null,
            'group_id' => $this->group_id,
        ];
    }
}
