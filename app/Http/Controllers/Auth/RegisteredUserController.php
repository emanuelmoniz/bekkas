<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\PasswordValidation;
use App\Rules\Recaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Ensure guest auth pages always render using the active site locale (session or app fallback).
        app()->setLocale(session('locale') ?? config('app.locale'));

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // If an account already exists and is not verified, prompt to resend instead of creating a new account
        if ($request->filled('email')) {
            $existing = User::where('email', $request->email)->first();

            if ($existing && ! $existing->email_verified_at) {
                return back()
                    ->withErrors(['email' => t('auth.email_unverified') ?: 'This email address exists but has not been verified.'])
                    ->withInput()
                    ->with('unverified_email', $request->email);
            }
        }

        // Build rules; require reCAPTCHA only when configured
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'confirmed'],
            'password' => ['required', 'confirmed', PasswordValidation::rules()],
        ];

        $messages = [
            'name.required' => t('validation.name_required') ?: 'Please enter your name.',
            'name.max' => t('validation.name_max') ?: 'Name cannot exceed 255 characters.',
            'email.required' => t('validation.email_required') ?: 'Please enter your email address.',
            'email.email' => t('validation.email_invalid') ?: 'Please enter a valid email address.',
            'email.unique' => t('validation.email_exists') ?: 'This email address is already registered.',
            'email.confirmed' => t('validation.email_mismatch') ?: 'Email addresses do not match.',
            'password.required' => t('validation.password_required') ?: 'Please enter a password.',
            'password.min' => t('validation.password_min') ?: 'Password must be at least :min characters.',
            'password.confirmed' => t('validation.password_mismatch') ?: 'Passwords do not match.',
        ];

        if (! empty(config('services.recaptcha.secret_key'))) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
            $messages['g-recaptcha-response.required'] = t('validation.recaptcha_required') ?: 'Please verify that you are not a robot.';
        }

        $request->validate($rules, $messages);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            // set initial language from active locale so verification email uses current site language
            'language' => app()->getLocale(),
        ]);

        // Assign default "client" role
        $clientRole = Role::where('name', 'client')->first();

        if ($clientRole) {
            $user->roles()->attach($clientRole->id);
        }

        // Fire Registered event (framework will send the verification notification).
        event(new Registered($user));

        // Do NOT log the user in until they verify their email.
        return redirect()->route('verification.sent')->with('email', $user->email);
    }

    /**
     * Show guest "check your email" page after registration.
     */
    public function verifyEmailSent(): View
    {
        return view('auth.verify-email-sent');
    }
}
