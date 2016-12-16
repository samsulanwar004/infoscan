<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\MerchantUser;
use Auth;

class MerchantService {

	public function getMerchantIdByAuth()
    {
        $mu = MerchantUser::where('user_id', '=', Auth::user()->id)->first();
        
        return $mu;
    }
}