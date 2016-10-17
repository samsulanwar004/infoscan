<?php

namespace Rebel\Component\Util\Contracts;

use Fractal;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Pagination\LengthAwarePaginator;
use Response;

trait ApiResponseTrait
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Respond with the given data after checking
     * that everything exists and is valid
     *
     * @param $data
     * @param $callback
     * @param array $includes
     *
     * @return \Response
     */
    public function respondWith($data, $callback, $includes = [])
    {
        //if $data is null throw error
        if (!$data) {
            return $this->errorNotFound('Requested response not found.');
        } //if $data is a Collection or a Paginated Collection
        else if ($data instanceof Collection || $data instanceof LengthAwarePaginator) {
            return $this->respondWithCollection($data, $callback, $includes);
        } //if $data is an Item
        else if ($data instanceof Model) {
            return $this->respondWithItem($data, $callback, $includes);
        } else {
            return $this->errorInternalError();
        }
    }

    public function respondSuccess($message)
    {
        return $this->respondWithArray([
            'status' => 'ok',
            'message' => $message,
        ]);
    }

    /**
     * @param $item
     * @param $callback
     * @param array $includes
     * @return mixed
     */
    protected function respondWithItem($item, $callback, $includes = [])
    {
        return Fractal::includes($includes)->item($item, $callback)->responseJson();
    }

    /**
     * @param $collection
     * @param $callback
     * @param array $includes
     * @return mixed
     */
    protected function respondWithCollection($collection, $callback, $includes = [])
    {
        return Fractal::includes($includes)->collection($collection, $callback)->responseJson();
    }

    /**
     * @param array $array
     * @param array $headers
     * @return mixed
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        $response = Response::json($array, $this->statusCode, $headers);

        return $response;
    }

    /**
     * @param $message
     * @param $errorCode
     * @return mixed
     */
    public function respondWithError($message, $errorCode)
    {
        return $this->respondWithArray([
            'status' => 'error',
            'error' => [
                'code' => $errorCode,
                'http_code' => $this->statusCode,
                'message' => $message,
            ]
        ]);
    }

    /**
     * Generates a Response with a 403 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(401)
                    ->respondWithError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)
                    ->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)
                    ->respondWithError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(403)
                    ->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)
                    ->respondWithError($message, self::CODE_WRONG_ARGS);
    }

    public function validate(\Illuminate\Http\Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new HttpResponseException(
                $this->errorWrongArgs($validator->errors())
            );
        }
    }

    private function getValidationFactory()
    {
        return app(Factory::class);
    }
}
