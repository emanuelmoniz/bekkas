<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Rules\Recaptcha;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'confirmed'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', new Recaptcha],
        ], [
            'name.required' => t('validation.name_required') ?: 'Please enter your name.',
            'name.max' => t('validation.name_max') ?: 'Name cannot exceed 255 characters.',
            'email.required' => t('validation.email_required') ?: 'Please enter your email address.',
            'email.email' => t('validation.email_invalid') ?: 'Please enter a valid email address.',
            'email.unique' => t('validation.email_exists') ?: 'This email address is already registered.',
            'email.confirmed' => t('validation.email_mismatch') ?: 'Email addresses do not match.',
            'password.required' => t('validation.password_required') ?: 'Please enter a password.',
            'password.min' => t('validation.password_min') ?: 'Password must be at least 8 characters.',
            'password.confirmed' => t('validation.password_mismatch') ?: 'Passwords do not match.',
            'g-recaptcha-response.required' => t('validation.recaptcha_required') ?: 'Please verify that you are not a robot.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign default "client" role
        $clientRole = Role::where('name', 'client')->first();

        if ($clientRole) {
            $user->roles()->attach($clientRole->id);
        }

        event(new Registered($user));

        Auth::login($user);

        // New users are clients by default, redirect to home
        return redirect()->intended('/');
    }
}
