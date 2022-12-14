<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        /*if ($this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }*/
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \App\Exceptions\PermissionDeniedException) {
            return response()->view('errors.403');
        }

        if($exception instanceof \Illuminate\Session\TokenMismatchException) {
            $req = request();

            if($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Can not process the login form. Please try again!'
                ], 400);
            }

            return redirect()->back()
                             ->withInput($req->all())
                             ->withDanger('Can not process the login form. Please try again!');
        }

        // if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
        //     return redirect('pages/404');
        // }

        // if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
        //     return redirect('pages/404');
        // }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || Str::contains($request->header('content-type'), ['/json', 'json'])) {
            return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
        }

        return redirect()->guest('login');
    }
}
