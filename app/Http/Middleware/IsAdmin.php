<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        // Non-admins (including guests) are sent to the public home instead of seeing a 403.
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            return redirect()->route('store.index');
        }

        return $next($request);
    }
}
