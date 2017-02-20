<?php

namespace App\Http\Controllers\Api;

use App\Events\MemberActivityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Providers\Auth\Illuminate;

class BaseApiController extends Controller
{
    /**
     * Send the error response.
     *
     * @param  string|\Exception $message
     * @param  int $httpCode
     * @return \Illuminate\Http\Response|JsonResponse
     */
    protected function error($message, $httpCode = 500, $isValidationMessage = false)
    {
        logger($message);
        if(config('app.debug') || $isValidationMessage) {
            if ($message instanceof \Exception || $message instanceof \InvalidArgumentException) {
                $message = $message->getMessage();
            }
        } else {
            $message = 'Something error with your request. Please contact your administrator';
        }

        return response()->json(
            $this->generateMessage('Error', $message),
            $httpCode
        );
    }

    /**
     * Send the not found response.
     *
     * @return \Illuminate\Http\Response|JsonResponse
     */
    protected function notFound()
    {
        return response()->json(
            $this->generateMessage('Error', 'Not Found'), 404
        );
    }

    /**
     * Send the success response.
     *
     * @param  mixed|null $message
     * @param  integer $httpCode
     * @return \Illuminate\Http\Response|JsonResponse
     */
    protected function success($message = null, $httpCode = 200)
    {
        if (null == $message) {
            return response()->json(
                $this->generateMessage('Ok', 'Success'),
                $httpCode
            );
        }

        if (is_string($message)) {
            return response()->json(
                $this->generateMessage('Ok', $message),
                $httpCode
            );
        }

        if (is_array($message)) {
            $m = [
                'status' => 'Ok',
                'message' => 'Success',
            ];

            $message = array_merge($m, isset($message['data']) ? $message : ['data' => $message]);

            return response()->json(
                $message,
                $httpCode
            );
        }
    }

    /**
     * Record the member actovity action on database log
     *
     * @param  string $action
     * @param  string $description
     * @return $this
     */
    protected function activityLogger($action, $description = '')
    {
        $member = 'wxwuu9cqus'; // TODO: get the member by session request
        event(new MemberActivityEvent($member, $action, $description));

        return $this;
    }

    /**
     * Generate response message.
     *
     * @param  string $status
     * @param  mixed $message
     * @return array
     */
    private function generateMessage($status, $message)
    {
        return [
            'status' => $status,
            'message' => $message,
        ];
    }

    /**
     * Get the active member by bearer token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|\Illuminate\Database\Eloquent\Model
     */
    protected function getActiveMember()
    {
        return auth('api')->user();
    }
}
