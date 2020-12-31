<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Lang;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*')) {
            $message = Lang::get('messages.general.laravel_error');
            $status = 500;

            if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
                $message = 'You do not have the required authorization.';
                $status  = 403;
            }

            if ($exception instanceof PostTooLargeException) {
                $message = 'File too large!';
                $status = 422;
            }

            if ($exception instanceof AuthenticationException) {
                $message = Lang::get('messages.user.token_invalid');
                $status = 401;
            }

            $jsonData = array();
            $jsonData['result'] = [];
            $jsonData['other_result'] = [];
            $jsonData['error'] = true;
            $jsonData['message'] = $message . ' - ' . $exception->getMessage();
            $jsonData['status_code'] = $status;
            return response()->json($jsonData, $status);
        }
        return parent::render($request, $exception);
    }
}
