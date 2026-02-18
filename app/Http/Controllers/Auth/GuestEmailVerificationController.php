<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

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

        // Log the resend attempt so we can trace outbound verification emails
        Log::info('Verification email requested (guest resend)', ['email' => $user->email, 'user_id' => $user->id]);

        return back()->with('status', 'verification-link-sent');
    }
}
