<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model {
    protected $table = 'reports';

    public function member() {
        return $this->belongsTo(\App\Member::class, 'member_id');
    }
}