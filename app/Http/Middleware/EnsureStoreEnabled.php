<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStoreEnabled
{
    /**
     * Handle an incoming request.
     * If the store is disabled, abort with 404 to hide store pages.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! config('app.store_enabled', true)) {
            abort(404);
        }

        return $next($request);
    }
}
