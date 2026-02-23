<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not available');
        }

        parent::setUp();
    }

    public function test_products_index_is_accessible()
    {
        $response = $this->get(route('store.index'));
        $response->assertStatus(200);
    }

    public function test_welcome_page_loads()
    {
        // Allow seeing exception details in test output for debugging
        $this->withoutExceptionHandling();

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_navigation_displays_store_submenu()
    {
        // ensure store menu and its children are rendered when store is enabled
        config(['app.store_enabled' => true]);

        // seed translations so `t()` returns human text instead of key names
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        // explicitly use English for predictable assertions
        app()->setLocale('en-UK');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeText('Store');
        $response->assertSeeText('All Products');
        $response->assertSeeText('Featured');
        $response->assertSeeText('Promotion');

        // ensure the hrefs include the proper query parameters for filters
        $response->assertSee(route('store.index'));
        $response->assertSee(route('store.index', ['is_featured' => 1]));
        $response->assertSee(route('store.index', ['is_promo' => 1]));
    }
}
