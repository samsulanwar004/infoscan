<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SnapFile;
use App\Member;
use App\User;
use App\SnapTag;

class Snap extends Model
{
    protected $table = 'snaps';

    public function files()
    {
        return $this->hasMany(SnapFile::class, 'snap_id', 'id');
    }

   	public function member()
   	{
   		return $this->belongsTo(Member::class, 'member_id');
   	}

   	public function approve()
   	{
   		return $this->belongsTo(User::class, 'approved_by');
   	}

   	public function reject()
   	{
   		return $this->belongsTo(User::class, 'rejected_by');
   	}

    public function tags()
    {
        return $this->hasManyThrough(SnapTag::class, SnapFile::class);
    }
}
