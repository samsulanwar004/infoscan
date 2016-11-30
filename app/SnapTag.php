<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SnapFile;

class SnapTag extends Model
{
    protected $table = 'snap_tags';

    public function file()
    {
        return $this->belongsTo(SnapFile::class, 'snap_tag_id');
    }
}
