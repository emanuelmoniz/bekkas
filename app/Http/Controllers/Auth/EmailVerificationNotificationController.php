<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $defaultRoute = $request->user()->isAdmin() ? route('admin.dashboard', absolute: false) : '/';

            return redirect()->intended($defaultRoute);
        }

        $request->user()->sendEmailVerificationNotification();

        Log::info('Verification email requested (user resend)', ['email' => $request->user()->email, 'user_id' => $request->user()->id]);

        return back()->with('status', 'verification-link-sent');
    }
}
