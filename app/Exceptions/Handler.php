<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
            // Only proceed if the environment is set to production
            // if ('production' === App::environment()) {

            //     $errorLog = collect([
            //         [
            //             'file' => $e->getFile(),
            //             'line' => $e->getLine(),
            //             'code' => $e->getCode(),
            //         ]
            //     ])->concat(collect($e->getTrace())->take(20))->toArray();

            //     Log::channel('slack_error_logs')->error($e->getMessage(), $errorLog);
            // }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {

        // if ($exception instanceof HttpException) {
        //     $statusCode = $exception->getStatusCode();

        //     if (in_array($statusCode, [401, 403, 419, 500])) {
        //         return response()->view("errors.custom-{$statusCode}", [], $statusCode);
        //     }
        // }

        if ($request->expectsJson()) {
            // Handle validation exceptions
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'errors' => $exception->errors(),
                ], 422); // Unprocessable Entity
            }

            // Handle model not found exceptions
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404); // Not Found
            }

            // Handle specific exception: Route not found
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            }

            // Handle authorization exceptions
            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403); // Forbidden
            }

            // Handle authentication exceptions
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401); // Unauthorized
            }

            // Handle other HTTP exceptions
            if ($this->isHttpException($exception)) {
                $statusCode = $exception->getStatusCode();
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], $statusCode);
            }

            // Default fallback for other exceptions
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500); // Internal Server Error
        }

        // For other exceptions or if not an HttpException, let the default handler render
        return parent::render($request, $exception);
    }
}
