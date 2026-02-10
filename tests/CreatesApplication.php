<?php

namespace Tests;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // If a local `.env.testing` exists, load it so tests run with the
        // developer-provided test environment automatically.
        // This file is intentionally ignored by git (`.env.testing`) and a
        // `.env.testing.example` template is provided for convenience.
        $envFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'.env.testing';
        if (file_exists($envFile)) {
            // Use vlucas/phpdotenv via the Dotenv class Laravel already uses
            try {
                \Dotenv\Dotenv::createImmutable(dirname(__DIR__), '.env.testing')->safeLoad();
            } catch (\Throwable $e) {
                // If dotenv isn't available or loading fails, continue — tests
                // will still run but may require manual environment variables.
            }
        }

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
