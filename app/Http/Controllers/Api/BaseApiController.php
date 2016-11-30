<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;

class BaseApiController extends Controller
{
    /**
     * Send the error response.
     *
     * @param  string|\Exception $message
     * @param  int $httpCode
     * @return \Illuminate\Http\Response
     */
    protected function error($message, $httpCode = 500)
    {
        logger($message);
        if ($message instanceof \Exception || $message instanceof \InvalidArgumentException) {
            $message = $message->getMessage();
        }

        return response()->json(
            $this->generateMessage('Error', $message),
            $httpCode
        );
    }

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
                'status'  => 'Ok',
                'message' => 'Success',
            ];

            $message = array_merge($m, ['data' => $message]);

            return response()->json(
                $message,
                $httpCode
            );
        }
    }

    private function generateMessage($status, $message)
    {
        return [
            'status'  => $status,
            'message' => $message,
        ];
    }
}
