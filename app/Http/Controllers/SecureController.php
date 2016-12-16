<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\SecureService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SecureController extends BaseApiController
{
    public function redirect($requestCode, $social)
    {
        //->stateless()
        return Socialite::driver($social)
                        ->redirect();
    }

    public function callback(Request $request, $social)
    {
        try {
            /** @var \Laravel\Socialite\Two\User $object */
            $object = Socialite::driver($social)->user();
            $user = [
                'name' => $object->getName(),
                'email' => $object->getEmail(),
                'avatar' => $object->getAvatar(),
                'link' => $object->user['link'],
            ];
            $secure = (new SecureService)->socialHandle($social, $user);

            return $this->success($secure);
        } catch (\Exception $e) {
            return $this->error($e);
        } catch (\InvalidArgumentException $e) {
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

            return response()->json([
                'status' => 'ok',
                'message' => 'Authenticated',
                'data' => [
                    'token' => $member->api_token,
                ],
            ]);
        }
    }
}
