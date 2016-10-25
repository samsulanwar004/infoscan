<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Rebel\Component\Rbac\Traits\HasRoles;

/**
 * Class User
 *
 * @package App
 * @property $filterIdentifier
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes, HasApiTokens;

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
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
