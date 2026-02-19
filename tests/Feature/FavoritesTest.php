<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_toggle_updates_session_and_returns_json()
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/favorites/toggle/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['isFavorite' => true]);

        $this->assertEquals([$product->id], session('favorites'));
    }

    public function test_authenticated_toggle_creates_and_removes_db_record()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // add
        $this->actingAs($user)
            ->postJson("/favorites/toggle/{$product->id}")
            ->assertStatus(200)
            ->assertJson(['isFavorite' => true]);

        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'product_id' => $product->id]);

        // remove
        $this->actingAs($user)
            ->postJson("/favorites/toggle/{$product->id}")
            ->assertStatus(200)
            ->assertJson(['isFavorite' => false]);

        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'product_id' => $product->id]);
    }
}
