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
}
