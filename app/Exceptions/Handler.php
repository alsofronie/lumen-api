<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $status = 500;
        $details = null;
        $type = null;
        if ($e instanceof ApiException) {
            $details = $e->getDetails();
            $type = $e->getType();
            $status = $e->getStatusCode();
        } elseif ($e instanceof HttpExceptionInterface || method_exists($e, 'getStatusCode')) {
            $status = $e->getStatusCode();
        } elseif ($e instanceof ModelNotFoundException) {
            $status = 404;
            $type = 'resource_not_found';
        } elseif ($e instanceof AuthorizationException) {
            $status = 403;
            $type = 'authorization';
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            $status = 422;
            $response = $e->getResponse()->getContent();
            $details = json_decode($response);
        }

        $code = $e->getCode();
        if (!$code) {
            $code = 10000 + $status;
        }

        $error = [
            'error' => true,
            'code' => $code,
            'status' => $status,
            'message' => $e->getMessage(),
            'type' => $type ? $type : str_replace('_exception', '', strtolower(snake_case(class_basename($e)))),
        ];

        if (null !== $details) {
            $error['details'] = $details;
        }

        /**
         * Don't test the debug :)
         */

        // @codeCoverageIgnoreStart
        if (env('APP_DEBUG') && $request->has('_debug')) {
            $error['debug'] = [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTrace(),
            ];
        }
        // @codeCoverageIgnoreEnd

        return response()->json($error, $status);
    }
}
