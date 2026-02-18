<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\SocialAuthException;
use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        try {
            $providerUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
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
