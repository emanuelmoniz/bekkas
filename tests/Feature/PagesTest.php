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

    public function test_store_order_dropdown_appears_and_correctly_labels()
    {
        config(['app.store_enabled' => true]);
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        app()->setLocale('en-UK');

        $response = $this->get(route('store.index'));
        $response->assertStatus(200);
        // ensure the select options are rendered with translations
        $response->assertSee('Name A-Z');
        $response->assertSee('Name Z-A');
        $response->assertSee('Price Low-High');
        $response->assertSee('Price High-Low');
        $response->assertSee('Featured First');
        $response->assertSee('Promo First');

        // selecting an order should mark the option as selected
        $resp2 = $this->get(route('store.index', ['order' => 'price_high_low']));
        // disable escaping because response HTML encodes quotes as &quot;
        $resp2->assertSee('value="price_high_low" selected', false);
    }

    public function test_store_products_can_be_ordered_by_query_param()
    {
        config(['app.store_enabled' => true]);
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        app()->setLocale('en-UK');

        // create two products with distinct names and prices
        $pA = \App\Models\Product::factory()->create(['price' => 5.00, 'is_featured' => false, 'is_promo' => false, 'active' => true]);
        $pB = \App\Models\Product::factory()->create(['price' => 2.00, 'is_featured' => false, 'is_promo' => false, 'active' => true]);
        // update their english translation names
        $pA->translations()->where('locale', 'en-UK')->update(['name' => 'Alpha']);
        $pB->translations()->where('locale', 'en-UK')->update(['name' => 'Beta']);

        // name ascending should show Alpha before Beta
        $resp1 = $this->get(route('store.index', ['order' => 'name_az']));
        $resp1->assertSeeInOrder(['Alpha', 'Beta']);

        // name descending should flip
        $resp2 = $this->get(route('store.index', ['order' => 'name_za']));
        $resp2->assertSeeInOrder(['Beta', 'Alpha']);

        // price low-high: B then A
        $resp3 = $this->get(route('store.index', ['order' => 'price_low_high']));
        $resp3->assertSeeInOrder(['Beta', 'Alpha']);

        // price high-low: A then B
        $resp4 = $this->get(route('store.index', ['order' => 'price_high_low']));
        $resp4->assertSeeInOrder(['Alpha', 'Beta']);

        // featured first
        $pA->update(['is_featured' => true]);
        $resp5 = $this->get(route('store.index', ['order' => 'featured_first']));
        $resp5->assertSeeInOrder(['Alpha', 'Beta']);

        // promo first
        $pB->update(['is_featured' => false, 'is_promo' => true]);
        $resp6 = $this->get(route('store.index', ['order' => 'promo_first']));
        // Beta is promo now so should appear before Alpha
        $resp6->assertSeeInOrder(['Beta', 'Alpha']);
    }

    public function test_store_displays_featured_and_promo_badges_on_product_cards()
    {
        config(['app.store_enabled' => true]);
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        app()->setLocale('en-UK');

        // create a few products with the various badge combinations
        \App\Models\Product::factory()->create(['is_featured' => true, 'is_promo' => false, 'active' => true]);
        \App\Models\Product::factory()->create(['is_featured' => false, 'is_promo' => true, 'active' => true]);
        // also one with both flags to ensure both badges can coexist
        \App\Models\Product::factory()->create(['is_featured' => true, 'is_promo' => true, 'active' => true]);

        $response = $this->get(route('store.index'));

        // badges should appear at least once with correct text and styling
        $response->assertSeeText('FEATURED');
        $response->assertSeeText('PROMO');
        // text should be white regardless of background
        $response->assertSee('text-white');
        // background colours correspond to accent primary/secondary tokens
        $response->assertSee('bg-accent-secondary');
        $response->assertSee('bg-accent-primary');
        // spacing between badges increased so check for gap class
        $response->assertSee('gap-2');
    }
}
