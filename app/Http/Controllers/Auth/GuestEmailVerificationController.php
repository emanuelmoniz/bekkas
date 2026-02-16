<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class GuestEmailVerificationController extends Controller
{
    /**
     * Resend a verification email for an existing (unverified) account.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => t('auth.not_a_user') ?: 'No account found with that email.']);
        }

        if ($user->email_verified_at) {
            return back()->withErrors(['email' => t('auth.already_verified') ?: 'This account is already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
