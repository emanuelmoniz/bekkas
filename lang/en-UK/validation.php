<?php

return [
    'min' => [
        'string' => 'This field must be at least :min characters.',
    ],

    'password' => [
        'letters' => 'Password must contain at least one letter.',
        'mixed' => 'Password must contain both uppercase and lowercase letters.',
        'numbers' => 'Password must contain at least one number.',
        'symbols' => 'Password must contain at least one symbol.',
        'uncompromised' => 'This password has appeared in a data leak. Please choose a different one.',
    ],

    'passwords' => [
        'sent' => 'We have emailed your password reset link.',
        'reset' => 'Your password has been reset.',
        'throttled' => 'Please wait before retrying.',
        'token' => 'This password reset token is invalid.',
        'user' => 'We cannot find a user with that email address.',
    ],
];
