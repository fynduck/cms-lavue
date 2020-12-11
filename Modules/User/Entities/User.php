<?php

namespace Modules\User\Entities;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\User\Notifications\ResetPassword;
use Modules\User\Notifications\VerifyEmail;
use Modules\User\Traits\UserTrait;
use Modules\UserGroup\Entities\UserGroup;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Modules\User\Entities\User
 *
 * @property int $id
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $skype
 * @property string $birthday
 * @property string|null $avatar
 * @property int|null $group_id
 * @property string $password
 * @property string|null $slug
 * @property string|null $token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Modules\UserGroup\Entities\UserGroup|null $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User filters(\Illuminate\Http\Request $request)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\Modules\User\Entities\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User verified()
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereSkype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Modules\User\Entities\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\Modules\User\Entities\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Modules\User\Entities\User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SoftDeletes, UserTrait;

    const XS = 'xs';

    const SM = 'sm';

    const MD = 'md';

    protected $fillable = [
        'username',
        'name',
        'email',
        'avatar',
        'group_id',
        'password',
        'slug',
        'token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'birthday' => 'datetime'
    ];

    public function isAdmin()
    {
        return $this->roles->admin == 1;
    }

    public function roles()
    {
        return $this->belongsTo(UserGroup::class, 'group_id');
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
