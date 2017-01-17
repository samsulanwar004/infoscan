<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rebel\Component\Rbac\Traits\HasRoles;

/**
 * Class User
 *
 * @package App
 * @property $filterIdentifier
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    protected $table = 'users';


    public $filterIdentifier = [
        'name' => 'name',
        'active' => 'is_active',
        'roles' => 'roles'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function merchantUsers()
    {
        return $this->hasOne(MerchantUser::class, 'user_id', 'id');
    }

    public function userActivities()
    {
        return $this->hasMany(UserActivities::class, 'user_id', 'id')->limit(10);
    }
}
