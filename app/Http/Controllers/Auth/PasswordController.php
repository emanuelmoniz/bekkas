<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\PasswordValidation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $userHasPassword = $user->hasPassword();

        $rules = [
            'password' => [
                'required',
                'confirmed',
                PasswordValidation::rules(),
            ],
        ];

        $messages = [
            'current_password.required' => t('validation.current_password_required'),
            'current_password.current_password' => t('validation.current_password_incorrect'),
            'password.required' => t('validation.password_required'),
            'password.confirmed' => t('validation.password_mismatch'),
        ];

        // Only require current password if the user already has one set
        if ($userHasPassword) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'updatePassword')
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validator->validated()['password']),
        ]);

        return back()->with('success', t('profile.password_updated_success'));
    }
}
