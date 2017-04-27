<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class InternalController extends BaseApiController
{
    public function index(Request $request)
    {
    	try {
    		$auth = $request->header('Authorization');
    		if($auth) {
    			$authArr = explode(' ', $auth);
    			if ($authArr[1] != config('common.internal_api_token')){
    				throw new Exception("Authorization failed", 1);    				
    			}
    		} else {
    			throw new \Exception("Authorization null", 1);    			
    		}

    		$ocr = \DB::select("select file_code, recognition_text, file_path, created_at from snap_files where mode_type = 'image' and process_status = 'processed'");

    		if($ocr) {
    			$ocr = collect($ocr);
    			$ocr = $ocr->map(function($entry) {
    				return [
    					'file_code' => $entry->file_code,
    					'recognition_text' => $entry->recognition_text,
    					'file_path' => env('S3_URL') . $entry->file_path,
    					'created_at' => $entry->created_at,
    				];
    			});
    		}

    		return $this->success($ocr->toArray(), 200);
    	} catch (\Exception $e) {
    		return $this->error($e, 400, true);
    	}
    }
}
