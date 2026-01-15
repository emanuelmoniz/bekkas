<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\PasswordValidation;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                PasswordValidation::rules()
            ],
        ], [
            'current_password.required' => t('validation.current_password_required'),
            'current_password.current_password' => t('validation.current_password_incorrect'),
            'password.required' => t('validation.password_required'),
            'password.confirmed' => t('validation.password_mismatch'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'updatePassword')
                ->withInput();
        }

        $request->user()->update([
            'password' => Hash::make($validator->validated()['password']),
        ]);

        return back()->with('success', t('profile.password_updated_success'));
    }
}
