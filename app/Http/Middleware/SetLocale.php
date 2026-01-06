<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, fallback to app config
        $locale = session('locale') ?? config('app.locale');

        // Validate the locale is supported
        if (array_key_exists($locale, config('app.locales'))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
