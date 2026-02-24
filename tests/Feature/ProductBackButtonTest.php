<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBackButtonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function back_url_stored_when_visiting_store_index()
    {
        $product = Product::factory()->create(['active' => true]);

        // visit the listing with some query string
        $this->get('/store?name=foo&order=price_low_high');

        // session should hold the exact full URL
        $this->assertEquals(
            url('/store?name=foo&order=price_low_high'),
            session('store_return_url')
        );

        // now visit the product page
        $response = $this->get(route('store.show', $product));

        // view should receive backUrl matching the stored value
        $response->assertViewHas('backUrl', url('/store?name=foo&order=price_low_high'));

        // and the rendered html should contain a link to that URL
        $response->assertSee('href="' . e(url('/store?name=foo&order=price_low_high')) . '"', false);
    }

    /** @test */
    public function default_back_url_is_store_index_if_session_missing()
    {
        $product = Product::factory()->create(['active' => true]);

        $response = $this->get(route('store.show', $product));

        $response->assertViewHas('backUrl', route('store.index'));
    }
}
