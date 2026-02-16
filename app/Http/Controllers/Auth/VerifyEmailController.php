<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Verify a user's email using the signed verification URL (guest or authenticated).
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        $emailForVerification = method_exists($user, 'getEmailForVerification') ? $user->getEmailForVerification() : $user->email;

        if (! hash_equals((string) $hash, sha1($emailForVerification))) {
            abort(403);
        }

        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
            event(new Verified($user));
        }

        // After verification redirect to login (user must authenticate)
        // Flash a localized success message (DB-driven `t()` with sensible fallback).
        return redirect('/login?verified=1')->with('status', t('auth.verification_verified') ?: 'Your email address has been verified.');
    }
}
