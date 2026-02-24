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

    public function test_favorites_index_shows_translated_pagination_summary()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(13)->create();

        // record each product as favourite in the database
        foreach ($products as $p) {
            \App\Models\Favorite::create([ 'user_id' => $user->id, 'product_id' => $p->id ]);
        }

        $this->actingAs($user);
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        app()->setLocale('en-UK');

        $response = $this->get(route('favorites.index'));
        $response->assertSeeText('Showing 1 to 12 of 13 results');
        $response->assertSee('Next');
        $this->assertEquals(1, substr_count($response->getContent(), 'Showing 1 to 12 of 13 results'));

        $resp2 = $this->get(route('favorites.index', ['page' => 2]));
        $resp2->assertSeeText('Showing 13 to 13 of 13 results');
        $resp2->assertSee('Previous');

        app()->setLocale('pt-PT');
        $respPt = $this->get(route('favorites.index'));
        $respPt->assertSeeText('A mostrar 1 a 12 de 13 resultados');
        $respPt->assertSee('Seguinte');
    }
}
