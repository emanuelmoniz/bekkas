<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable only CSRF middleware for integration tests so form POSTs in tests
        // don't fail with 419 when no real browser session/CSRF token is present.
        // Keep auth and other middleware enabled for tests that assert middleware behavior.
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        // Disable rate-limiting (ThrottleRequests) middleware for tests to avoid
        // flaky 429 failures when many tests or repeated requests run quickly.
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        // Ensure the Blade `$errors` variable exists (normally provided by
        // ShareErrorsFromSession middleware). Tests disable CSRF only, so
        // share an empty ViewErrorBag to avoid undefined variable errors.
        $this->app['view']->share('errors', new \Illuminate\Support\ViewErrorBag());

        // Put a predictable CSRF token in the session and send it as a header
        // so VerifyCsrfToken middleware (if still active) accepts POST/JSON.
        $this->app['session']->put('_token', 'test');
        $this->withHeaders(['X-CSRF-TOKEN' => 'test']);
    }
}
