<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rebel\Component\Rbac\Traits\HasRoles;

class MerchantSettingReport extends Model {
    use Notifiable, HasRoles, SoftDeletes;
    protected $table = 'report_templates';
    protected $fillable = [
        'name',
        'content',
        'created_by',
        'updated_by'
    ];
}