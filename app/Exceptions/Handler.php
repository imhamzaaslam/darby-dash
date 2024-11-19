<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use ErrorException;
use Error;

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
     * Report internal exceptions as notice and other exceptions as warning
     *
     * @param \Throwable $e
     * @return void
     */
    public function report(Throwable $e)
    {
        if(in_array(get_class($e), $this->internalDontReport, true) && !($e instanceof AuthorizationException || $e instanceof HttpException)) {
            Log::notice($e->__toString()); // Log internal exceptions as notice
            return;
        }

        Log::warning("Exception: " . $e->getMessage(), [
            'ip' => request()->ip(),
            'uri' => request()->getRequestUri(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if($e instanceof AuthenticationException) {
            return response()->json(['error' => 'unauthenticated', 'message' => $e->getMessage()], 401);
        }

        if($e instanceof AuthorizationException || $e instanceof HttpException) {
            return response()->json(['error' => 'unauthorized', 'message' => $e->getMessage()], 403);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json(['error' => 'object-not-found', 'message' => $e->getMessage()], 404);
        }

        if ($e instanceof ErrorException || $e instanceof Error) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Something went wrong. Please try again later.'], 500);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json(['error' => 'method-not-allowed', 'message' => $e->getMessage()], 405);
        }

        if ($e instanceof ValidationException) {
            return response()->json(['error' => 'validation-error', 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        }

        if ($e instanceof TenantCouldNotBeIdentifiedOnDomainException) {
            return response()->json(['error' => 'unauthorized', 'message' => $e->getMessage()], 404);
        }

        return parent::render($request, $e);
    }

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
}
