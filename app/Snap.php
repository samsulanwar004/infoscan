<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SnapFile;

class Snap extends Model
{
    protected $table = 'snaps';

    public function files()
    {
        return $this->hasMany(SnapFile::class, 'snap_id', 'id');
    }

   	public function member()
   	{
   		return $this->belongsTo(\App\Member::class, 'member_id');
   	}

   	public function approve()
   	{
   		return $this->belongsTo(\App\User::class, 'approved_by');
   	}

   	public function reject()
   	{
   		return $this->belongsTo(\App\User::class, 'reject_by');
   	}
}
