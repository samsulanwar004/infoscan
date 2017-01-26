<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Province;

class LocationService
{
	public function getAllProvince()
	{
		return Province::with('regencies')
			->orderBy('id')->get();
	}
}