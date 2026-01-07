<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => t('validation.current_password_required') ?: 'Please enter your current password.',
            'current_password.current_password' => t('validation.current_password_incorrect') ?: 'Current password is incorrect.',
            'password.required' => t('validation.password_required') ?: 'Please enter a new password.',
            'password.min' => t('validation.password_min') ?: 'Password must be at least 8 characters.',
            'password.confirmed' => t('validation.password_mismatch') ?: 'Passwords do not match.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
