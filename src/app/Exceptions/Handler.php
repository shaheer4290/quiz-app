<?php

namespace App\Exceptions;

use App\Utils\ResponseUtils;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->isJson()) {
            if (get_class($exception) == "Illuminate\Database\Eloquent\ModelNotFoundException") {
                return ResponseUtils::sendResponseWithError($exception->getMessage(), Response::HTTP_NOT_FOUND);
            } else {
                if (method_exists($exception, 'getStatusCode')) {
                    $message = empty($exception->getMessage()) ? 'This route does not exist' : $exception->getMessage();

                    return ResponseUtils::sendResponseWithError($message, $exception->getStatusCode());
                }
            }
        }

        return parent::render($request, $exception);
    }
}
