<?php
/**
 * Created by PhpStorm.
 * User: ryanr
 * Date: 11/30/2016
 * Time: 5:30 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class SocioEconomicStatus extends Model
{
    protected $table = 'socio_economic_status';
    protected $fillable = [
        'code',
        'range_start',
        'range_end'
    ];
}