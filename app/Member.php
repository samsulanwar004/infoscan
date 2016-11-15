<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticable
{
    use Notifiable;

    protected $hidden = ['password'];
}
