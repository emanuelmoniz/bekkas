<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not available');
        }

        parent::setUp();
    }

    public function test_404_page_shows_logo_button_and_contact()
    {
        // Seed DB translations used by the error view
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);

        $response = $this->get('/a-page-that-does-not-exist');

        $response->assertStatus(404);
        $response->assertSee('hero-logo.png');
        $response->assertSee(t('error.back_home'));
        $response->assertSee(config('mail.contact_address'));
    }

    public function test_500_page_renders_custom_view_on_exception()
    {
        // Seed DB translations used by the error view
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);

        // Ensure the exception handler renders the error view (not debug page)
        $this->app['config']->set('app.debug', false);

        // Register a temporary route that throws to produce a 500
        $this->app['router']->get('/_test-error-500', function () {
            throw new \RuntimeException('test-exception');
        });

        $response = $this->get('/_test-error-500');

        $response->assertStatus(500);
        $response->assertSee(t('error.500.title'));
        $response->assertSee('hero-logo.png');
        $response->assertSee(config('mail.contact_address'));
    }
}
