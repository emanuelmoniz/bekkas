<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppendSessionFlashToJson
{
    /**
     * Append session flash to JSON responses so AJAX clients can display the same flash UI.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only act on JSON responses (including Laravel's JsonResponse)
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            $flashMessage = session('success') ?? session('error') ?? session('warning') ?? session('info') ?? null;
            $flashType = session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : null)));

            if ($flashMessage) {
                $data['flash'] = [
                    'message' => $flashMessage,
                    'type' => $flashType ?: 'info',
                ];

                $response->setData($data);
            }
        }

        // If controller returned a redirect that has session flash, leave it unchanged
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        return $response;
    }
}
