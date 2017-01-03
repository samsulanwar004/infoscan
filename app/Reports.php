<?php
    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class Reports extends Model {
	    protected $table = 'reports';
    	protected $fillable = [
    							'outlet_name', 
    							'products', 
    							'users_city',
    							'age',
    							'outlet_area',
    							'province',
    							'gender',
    							'usership',
    							'sec',
    							'outlet_type',
    						  ];
    }