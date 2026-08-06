<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        try {
            $customResponse = $this->renderCustomErrorPage($request, $e);

            if ($customResponse !== null) {
                return $customResponse;
            }
        } catch (Throwable $renderError) {
            report($renderError);

            if (!$request->expectsJson() && !$request->is('api/*')) {
                return response('Terjadi kendala pada sistem. Silakan coba lagi beberapa saat.', 500);
            }
        }

        return parent::render($request, $e);
    }

    protected function renderCustomErrorPage(Request $request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        $statusCode = $this->resolveCustomErrorStatus($e);

        if ($statusCode === null) {
            return null;
        }

        $view = 'errors.' . $statusCode;

        if (!view()->exists($view)) {
            return null;
        }

        try {
            return response()->view($view, [], $statusCode);
        } catch (Throwable $viewError) {
            report($viewError);

            return response('Terjadi kendala pada sistem. Silakan coba lagi beberapa saat.', $statusCode);
        }
    }

    protected function resolveCustomErrorStatus(Throwable $e): ?int
    {
        return match (true) {
            $e instanceof TokenMismatchException => 419,
            $e instanceof AuthorizationException,
            $e instanceof AccessDeniedHttpException => 403,
            $e instanceof ModelNotFoundException,
            $e instanceof NotFoundHttpException => 404,
            $e instanceof TooManyRequestsHttpException => 429,
            $e instanceof ServiceUnavailableHttpException => 503,
            $e instanceof HttpExceptionInterface => in_array($e->getStatusCode(), [403, 404, 405, 419, 429, 500, 503], true)
                ? $e->getStatusCode()
                : null,
            default => null,
        };
    }
}
