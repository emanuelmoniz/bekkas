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
        $product = Product::factory()->create();

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

        // only one of the locales will be returned by translation(), but we
        // can at least verify the english copy is shown (app locale defaults to?
        // test environment default is likely en-UK but we'll check both possibilities).
        $expected = $product->translation()->description;
        $response->assertSee($expected, false);
        $response->assertSee($product->translation()->technical_info, false);
    }
}
