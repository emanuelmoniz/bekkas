<?php

namespace Tests\Feature;

use App\Http\Controllers\CartController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddToCartAjaxTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
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
            ->assertJson(['success' => true, 'cartCount' => 3]);

        // session should have cart entry (composite key: "{id}_" with array value)
        $cartKey = CartController::makeCartKey($product->id, []);
        $cart = session('cart');
        $this->assertArrayHasKey($cartKey, $cart);
        $this->assertEquals(3, $cart[$cartKey]['quantity']);
    }

    #[Test]
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

        $response->assertJson(['cartCount' => 3]);
        $cartKey = CartController::makeCartKey($product->id, []);
        $this->assertEquals(3, session('cart')[$cartKey]['quantity']);
    }

    #[Test]
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

    #[Test]
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
