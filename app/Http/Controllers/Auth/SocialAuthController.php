<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\SocialAuthException;
use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        // Some providers need explicit scopes (Microsoft requires openid/profile/email to return email)
        if ($provider === 'microsoft') {
            return Socialite::driver('microsoft')->scopes(['openid', 'profile', 'email'])->redirect();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        // Log incoming callback query (mask sensitive values like 'code') for deterministic debugging
        $reqQuery = request()->query();
        $masked = $reqQuery;
        if (isset($masked['code'])) { $masked['code'] = '***masked***'; }
        Log::debug('Social callback incoming', ['provider' => $provider, 'query' => $masked]);

        // If provider returned an OAuth error in the callback, surface a user-friendly message and log details
        if (request()->has('error')) {
            Log::warning('Provider returned error in OAuth callback', ['provider' => $provider, 'error' => request('error'), 'error_description' => request('error_description')]);

            if (auth()->check()) {
                return redirect()->route('profile.edit')->withErrors(['social' => t('auth.social_failed') ?: 'Social operation failed.']);
            }

            return redirect()->route('login')->withErrors(['social' => t('auth.social_failed') ?: 'Social login failed.']);
        }

        // Defensive: if the authorization code is missing or empty, avoid attempting token exchange and log for diagnostics
        if (! request()->filled('code')) {
            Log::warning('Missing or empty authorization code in OAuth callback', [
                'provider' => $provider,
                'query_keys' => array_keys($reqQuery),
                'code_empty' => request()->has('code') && request('code') === ''
            ]);

            if (auth()->check()) {
                return redirect()->route('profile.edit')->withErrors(['social' => t('auth.social_failed') ?: 'Social operation failed.']);
            }

            return redirect()->route('login')->withErrors(['social' => t('auth.social_failed') ?: 'Social login failed.']);
        }

        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            // Log provider callback failures for deterministic debugging (mask any code present)
            $reqQuery = request()->query();
            if (isset($reqQuery['code'])) { $reqQuery['code'] = '***masked***'; }
            Log::error('Socialite callback failed', ['provider' => $provider, 'query' => $reqQuery, 'has_code' => request()->has('code'), 'error_param' => request('error'), 'exception' => $e]);

            if (auth()->check()) {
                return redirect()->route('profile.edit')->withErrors(['social' => t('auth.social_failed') ?: 'Social operation failed.']);
            }

            return redirect()->route('login')->withErrors(['social' => t('auth.social_failed') ?: 'Social login failed.']);
        }

        // If an authenticated user initiated the flow, treat this as a "link" operation
        if (auth()->check()) {
            try {
                (new SocialAuthService())->linkProviderToUser(auth()->user(), $provider, $providerUser);
            } catch (SocialAuthException $e) {
                // Map known exception codes to DB-driven translation keys
                if ($e->getCode() === SocialAuthException::PROVIDER_ALREADY_LINKED) {
                    $msg = t('profile.provider_already_linked') ?: $e->getMessage();
                } else {
                    $msg = $e->getMessage();
                }

                return redirect()->route('profile.edit')->withErrors(['social' => $msg]);
            }

            return redirect()->route('profile.edit')->with('status', 'social-linked');
        }

        // Unauthenticated: normal sign-in / registration flow
        try {
            $user = (new SocialAuthService())->findOrCreateUserFromProvider($provider, $providerUser);
        } catch (SocialAuthException $e) {
            if ($e->getCode() === SocialAuthException::UNVERIFIED_EMAIL) {
                return redirect()
                    ->route('login')
                    ->withErrors(['email' => t('auth.email_unverified') ?: 'This email address exists but has not been verified.'])
                    ->with('unverified_email', $e->getEmail());
            }

            return redirect()->route('login')->withErrors(['social' => $e->getMessage()]);
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }

    /**
     * Unlink a social provider from the authenticated user's account.
     */
    public function unlinkProvider(string $provider)
    {
        $user = auth()->user();

        try {
            (new \App\Services\SocialAuthService())->unlinkProviderFromUser($user, $provider);
        } catch (SocialAuthException $e) {
            $msg = $e->getMessage();

            if ($e->getCode() === SocialAuthException::CANNOT_UNLINK_LAST_AUTH) {
                $msg = t('profile.cannot_unlink_last_auth') ?: $msg;
            }

            return redirect()->route('profile.edit')->withErrors(['social' => $msg]);
        }

        return redirect()->route('profile.edit')->with('status', 'social-unlinked');
    }
}
