<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\AuthServiceProvider::class,
        App\Providers\TranslationServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle model not found exceptions
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->view('errors.404', [], 404);
            }

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                // Preserve intended URL for guests so they return after login/register
                return redirect()->guest(route('login'));
            }

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->view('errors.403', [], 403);
            }

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return back()->withErrors($e->errors())->withInput();
            }

            // Log unexpected exceptions in production
            if (app()->isProduction()) {
                \Illuminate\Support\Facades\Log::error('Unexpected error', [
                    'exception' => get_class($e),
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'user_id' => Auth::check() ? Auth::id() : null,
                ]);
            }

            // Return generic error in production
            if (app()->isProduction()) {
                return response()->view('errors.500', [], 500);
            }
        });
    })->create();
