<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticable
{
    use Notifiable;

    protected $hidden = ['password'];

    public function luckyDraws()
    {
        return $this->belongsToMany(luckyDraw::class, 'members_lucky_draws', 'member_id', 'luckydraw_id');
    }

    public function memberLevels()
    {
    	return $this->hasMany(MemberLevel::class, 'member_id', 'id');
    }

    public function questionnaireSubmit()
    {
        return $this->hasMany(QuestionnaireSubmit::class, 'member_id');
    }
}
