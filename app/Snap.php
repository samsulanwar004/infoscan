<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SnapFile;

class Snap extends Model
{
    protected $table = 'snaps';

    public function files()
    {
        return $this->hasMany(SnapFile::class, 'snap_id', 'id');
    }
}
