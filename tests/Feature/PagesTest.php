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
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
    }

    public function test_welcome_page_loads()
    {
        // Allow seeing exception details in test output for debugging
        $this->withoutExceptionHandling();

        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
