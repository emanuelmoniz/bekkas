<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_page_displays_description_and_technical_info()
    {
        // ensure UI translations are available in tests
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);

        $product = Product::factory()->create();

        // give dimensions so we can assert they render on the page
        $product->update([
            'width' => 10,
            'length' => 20,
            'height' => 30,
        ]);

        // overwrite the auto‑created translations with deterministic
        // values so we can assert them later
        $product->translations()->each(function ($trans) {
            $locale = $trans->locale;
            $trans->update([
                'name' => "Name $locale",
                'description' => "Desc $locale",
                'technical_info' => "Tech $locale",
            ]);
        });

        $response = $this->get(route('store.show', $product));
        $response->assertStatus(200);

        // verify name is shown somewhere on the page, and that the correct
        // price is rendered as well. the header was removed so the name
        // should appear in the body above the price element.
        $response->assertSee($product->translation()->name);

        // labels should be translated as they come from the static translations
        $response->assertSee(t('store.description')); // ensures helper works

        // only one of the locales will be returned by translation(), but we
        // can at least verify the english copy is shown (app locale defaults to?
        // test environment default is likely en-UK but we'll check both possibilities).
        $expected = $product->translation()->description;
        $response->assertSee($expected, false);
        $response->assertSee($product->translation()->technical_info, false);

        // price formatting assertion
        $formatted = '€'.number_format($product->price,2);
        $response->assertSee($formatted);

        // dimensions should render with mm unit (factory stores decimals)
        $response->assertSee('10.00 × 20.00 × 30.00 mm');
    }
}
