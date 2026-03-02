<?php

namespace App\Http\Middleware;

use App\Models\Configuration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * Redirect all non-admin traffic to the maintenance page when is_maintenance is enabled.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always allow the maintenance page, admin routes, and auth (login/logout) through.
        // Use path-based check for login so both the GET (show form) and POST (submit) are exempted —
        // the POST /login route is not named, so routeIs('login') would miss it.
        if ($request->routeIs('maintenance') || $request->is('login', 'admin*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Admins can always access the site
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Check maintenance flag from latest configuration
        $config = Configuration::latest()->first();

        if ($config && $config->is_maintenance) {
            return redirect()->route('maintenance');
        }

        return $next($request);
    }
}
