<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Snap;
use App\SnapTag;

class SnapFile extends Model
{
    protected $table = 'snap_files';

    public function snap()
    {
        return $this->belongsTo(Snap::class, 'snap_id');
    }

    public function tag()
    {
        return $this->hasOne(SnapTag::class, 'snap_file_id', 'id');
    }
}
