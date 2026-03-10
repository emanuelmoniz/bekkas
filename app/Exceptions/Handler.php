<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }

    /**
     * Customize JSON response for validation exceptions to include a `flash` payload
     * so AJAX clients can show the same server flash UI used on full-page requests.
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        $response = parent::invalidJson($request, $exception);

        $errors = $exception->validator->errors();
        $first = $errors->first();

        $payload = $response->getData(true);
        $payload['flash'] = [
            'message' => $first ?: '',
            'type' => 'error',
        ];

        return new JsonResponse($payload, $response->getStatusCode(), $response->headers->all());
    }
}
