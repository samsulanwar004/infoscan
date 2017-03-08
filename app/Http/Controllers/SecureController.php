<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\SecureService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Validator;

class SecureController extends BaseApiController
{
    public function redirect(Request $request, $requestCode, $social)
    {
        // validate the input
        $validation = Validator::make([
            'request_code' => $requestCode,
            'social' => $social,
        ], [
            'request_code' => 'required',
            'social' => 'required|in:facebook,linkedin,instagram,google',
        ]);

        try {
            if ($validation->fails()) {
                $this->error($validation->errors(), 400, true);
            }

            return Socialite::with($social)->redirect();
        } catch (\Exception $e) {
            return $this->error($e, 400);
        }
    }

    public function callback(Request $request, $social)
    {
        try {
            /** @var \Laravel\Socialite\Two\User $object */
            $object = Socialite::driver($social)->user();

            $user = [
                'member_code' => $object->getId() ?: strtolower(str_random(10)),
                'name' => $object->getName(),
                'email' => $object->getEmail(),
                'avatar' => $object->getAvatar(),
                'link' => isset($object->user['link']) ? $object->user['link'] : null,
            ];

            $secure = (new SecureService)->socialHandle($social, $user);

            return $this->isIphone($request) ? view('auth.api_register_callback', [
                'register' => [
                    'status' => 'ok',
                    'message' => 'Success',
                    'data' => $secure['data'],
                ]
            ]) : $this->success($secure);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Login action for member
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if ($member = auth('api')->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ])
        ) {
            return $this->success(['token' => $member->api_token]);
        }

        return $this->error('Unauthenticated', 401);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'social_media_type' => 'required|in:facebook,google,instagram',
            ]);

            if ($validation->fails()) {
                return $this->error($validation->errors(), 400, true);
            }

            $secure = (new SecureService)->registerManualHandle($request);

            return $this->success($secure);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    private function isIphone(Request $request)
    {
        if(preg_match("/iphone/", strtolower($request->header('User-Agent')))) {
            return true;
        }

        return false;
    }
}
