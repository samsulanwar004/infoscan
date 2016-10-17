<?php

namespace Rebel\Component\Util\Contracts;

use Illuminate\Http\Request;

trait ControllerResponseTrait
{
    /**
     * @param array|string $message
     * @param \Illuminate\Http\Request|null $request
     *
     * @param null|string $to
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function sendError($message, Request $request = null, $to = null)
    {
        $request = $this->requestInstance($request);
        $message = is_string($message) ? [$message] : $message;

        logger($message);

        if ($request->ajax() || $this->responseShouldBeJson($request)) {
            return response()->json([
                'status' => 'error',
                'messages' => $message,
            ]);
        }

        return null === $to ? redirect()->back()->with('messages', $message) :
            redirect($to)->with('messages', $message);
    }

    /**
     * @param string|array $message
     * @param \Illuminate\Http\Request|null $request
     * @param null|string $to
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function sendSuccess($message = null, Request $request = null, $to = null)
    {
        $defaultMessage = [
            'status' => 'ok',
            'messages' => ['Success'],
        ];
        $request = $this->requestInstance($request);
        if(! is_null($message)) {
            $message = is_string($message) ? ['messages' => $message] : $message;
            $message = array_merge($defaultMessage, $message);
        } else {
            $message = $defaultMessage;
        }

        if ($request->ajax() || $request->hasHeader('X-RESPONSE-JSON') || $request->wantsJson()) {
            return response()->json($message);
        }

        return null === $to ? redirect()->back()->with('messages', $message) :
            redirect($to)->with('messages', $message);
    }

    private function requestInstance($request = null)
    {
        return null == $request ? app('request') : $request;
    }

    protected function responseShouldBeJson($request)
    {
        return 'response-json' === $request->headers->get('X-RESPONSE-JSON');
    }

    protected function handleException($e)
    {
        return $this->sendError($e->getMessage(), null, 500);
    }
}
