<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MerchantSettingReport extends Model
{
    use SoftDeletes;

    protected $table = 'report_templates';

    protected $fillable = [
        'name',
        'content',
        'created_by',
        'updated_by'
    ];
}