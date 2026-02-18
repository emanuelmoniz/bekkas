<?php

namespace App\Exceptions;

use Exception;

class SocialAuthException extends Exception
{
    public const UNVERIFIED_EMAIL = 1;
    public const NO_EMAIL = 2;
    public const PROVIDER_ALREADY_LINKED = 3;
    public const CANNOT_UNLINK_LAST_AUTH = 4;

    protected ?string $email = null;

    public function __construct(string $message = "", int $code = 0, ?string $email = null)
    {
        parent::__construct($message, $code);
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
