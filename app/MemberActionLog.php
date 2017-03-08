<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberActionLog extends Model
{
    protected $table = 'member_action_logs';
    protected $casts = [
    	'content' => 'array'
    ];
}
