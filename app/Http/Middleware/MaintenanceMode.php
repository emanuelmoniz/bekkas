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
        // Always allow the maintenance page itself and admin routes through
        if ($request->routeIs('maintenance') || $request->is('admin*')) {
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
