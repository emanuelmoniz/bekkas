<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;

class ProductUuidTest extends TestCase
{
    public function test_product_urls_use_uuid()
    {
        $product = Product::factory()->create(['active' => true]);

        $url = route('products.show', $product);

        // URL should contain UUID and the last path segment should be the UUID (avoid false positives)
        $this->assertStringContainsString($product->uuid, $url);
        $path = parse_url($url, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', $path)));
        $last = end($segments);
        $this->assertEquals($product->uuid, $last);
        $this->assertNotEquals((string) $product->id, $last);
    }

    public function test_view_product_by_uuid_returns_ok()
    {
        $product = Product::factory()->create(['active' => true]);

        $this->get(route('products.show', $product))
            ->assertOk();
    }
}
