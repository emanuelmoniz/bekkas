<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddToCartAjaxTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_json_and_does_not_redirect_when_adding_via_ajax()
    {
        $product = Product::factory()->create([
            'active' => true,
            'stock' => 10,
        ]);

        $response = $this->postJson(route('cart.add', $product), [
            'quantity' => 3,
        ]);

        $response->assertStatus(200)
                 ->assertJson([ 'success' => true, 'cartCount' => 3 ]);

        // session should have cart entry
        $this->assertEquals(
            session('cart')[$product->id],
            3
        );
    }

    /** @test */
    public function it_respects_existing_quantity_when_adding_more()
    {
        $product = Product::factory()->create([
            'active' => true,
            'stock' => 5,
        ]);

        // first add
        $this->postJson(route('cart.add', $product), ['quantity' => 2]);
        // second add
        $response = $this->postJson(route('cart.add', $product), ['quantity' => 1]);

        $response->assertJson([ 'cartCount' => 3 ]);
        $this->assertEquals(session('cart')[$product->id], 3);
    }

    /** @test */
    public function it_returns_404_json_if_product_inactive()
    {
        $product = Product::factory()->create([
            'active' => false,
            'stock' => 10,
        ]);

        $response = $this->postJson(route('cart.add', $product), ['quantity' => 1]);
        $response->assertStatus(404)
                 ->assertJson(['success' => false]);
    }

    /** @test */
    public function it_returns_error_when_stock_unavailable()
    {
        $product = Product::factory()->create([
            'active' => true,
            'stock' => 0,
            'is_backorder' => false,
        ]);

        $response = $this->postJson(route('cart.add', $product), ['quantity' => 1]);
        $response->assertStatus(422)
                 ->assertJson(['success' => false]);
    }
}
