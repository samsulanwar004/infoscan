<?php

namespace Rebel\Component\Setting\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 *
 * @package Rebel\Component\Setting\Model
 * @property $setting_group
 * @property $setting_name
 * @property $setting_display_name
 * @property $setting_value
 */
class Setting extends Model
{

    protected $softDelete = true;

    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->table = config('setting.table', 'settings');

        parent::__construct($attributes);
    }
}