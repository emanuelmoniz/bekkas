<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password;

class PasswordValidation
{
    /**
     * Get the password validation rules used across the application.
     */
    public static function rules(): Password
    {
        return Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();
    }
}
