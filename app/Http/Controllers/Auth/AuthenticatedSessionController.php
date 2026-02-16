<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        // Render login page with the active site locale (session locale) — do not use any stored user language.
        app()->setLocale(session('locale') ?? config('app.locale'));

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Your account is disabled.',
            ]);
        }

        $request->session()->regenerate();

        // Merge session favorites to user account
        $sessionFavorites = session('favorites', []);
        if (! empty($sessionFavorites)) {
            foreach ($sessionFavorites as $productId) {
                $user->favorites()->firstOrCreate(['product_id' => $productId]);
            }
            session()->forget('favorites');
        }

        $defaultRoute = $user->isAdmin() ? route('admin.dashboard', absolute: false) : '/';

        return redirect()->intended($defaultRoute);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
