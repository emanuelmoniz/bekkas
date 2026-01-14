<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable all middleware for integration tests so form POSTs in tests
        // don't fail with 419 when no real browser session/CSRF token is present.
        $this->withoutMiddleware();

        // Ensure the Blade `$errors` variable exists (normally provided by
        // ShareErrorsFromSession middleware). Tests disable middleware, so
        // share an empty ViewErrorBag to avoid undefined variable errors.
        $this->app['view']->share('errors', new \Illuminate\Support\ViewErrorBag());
    }
}
