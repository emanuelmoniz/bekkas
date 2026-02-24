<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProductPhoto;
use App\Models\ProjectPhoto;

class ImageScrollerTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_product_produces_correct_urls()
    {
        $product = Product::factory()->create();
        $product->photos()->create(['path' => 'foo.jpg']);
        $product->photos()->create(['path' => 'bar.jpg']);

        $images = image_scroller_images(['product' => $product->id]);

        $this->assertCount(2, $images);
        $this->assertStringEndsWith('foo.jpg', $images->first());
        $this->assertStringEndsWith('bar.jpg', $images->last());
    }

    public function test_multiple_products_with_filters_and_per_item()
    {
        $p1 = Product::factory()->create(['is_featured' => true, 'active' => true]);
        $p2 = Product::factory()->create(['is_featured' => false, 'active' => true]);
        // product without photos should be ignored gracefully
        $p3 = Product::factory()->create(['is_featured' => true, 'active' => false]);

        $p1->photos()->create(['path' => 'p1a.jpg']);
        $p1->photos()->create(['path' => 'p1b.jpg']);
        $p2->photos()->create(['path' => 'p2a.jpg']);
        $p3->photos()->create(['path' => 'p3a.jpg']);

        $conf = [
            'products' => [
                'featured' => true,
                'active' => true,
                'per_item' => 1,
            ],
        ];

        $images = image_scroller_images($conf);

        $this->assertCount(1, $images); // only p1 and only first photo
        $this->assertStringEndsWith('p1a.jpg', $images->first());
    }

    public function test_projects_behaviour_is_similar()
    {
        $proj = Project::factory()->create(['is_active' => true, 'is_featured' => false]);
        $proj->photos()->create(['path' => 'proj1.jpg']);

        $images = image_scroller_images(['project' => $proj->id]);
        $this->assertCount(1, $images);
        $this->assertStringEndsWith('proj1.jpg', $images->first());
    }

    public function test_maximum_limit_applies_across_sources()
    {
        $p = Product::factory()->create();
        $p->photos()->create(['path' => 'a.jpg']);
        $p->photos()->create(['path' => 'b.jpg']);

        $images = image_scroller_images(['product' => $p->id, 'max' => 1]);
        $this->assertCount(1, $images);
    }

    public function test_component_renders_markup()
    {
        $images = collect(['https://foo/bar.jpg', 'https://foo/baz.jpg']);
        $html = view('components.image-scroller', ['images' => $images, 'config' => ['interval' => 1234]])->render();

        $this->assertStringContainsString('data-image-scroller', $html);
        $this->assertStringContainsString('background-image:url', $html);
    }

    public function test_autoplay_config_serializes_into_attribute()
    {
        $images = collect(['https://foo/bar.jpg']);
        $html = view('components.image-scroller', ['images' => $images, 'config' => ['interval' => 5000, 'autoplay' => false]])->render();

        // ensure the JSON output includes both keys and correct boolean
        // note: attribute is HTML‑escaped so quotes appear as &quot;
        $this->assertStringContainsString('&quot;interval&quot;:5000', $html);
        $this->assertStringContainsString('&quot;autoplay&quot;:false', $html);
    }

    public function test_desktop_and_mobile_autoplay_defaults_are_present()
    {
        $images = collect(['https://foo/bar.jpg']);
        $html = view('components.image-scroller', ['images' => $images, 'config' => ['interval' => 2000]])->render();

        $this->assertStringContainsString('&quot;autoplay_desktop&quot;:false', $html);
        $this->assertStringContainsString('&quot;autoplay_mobile&quot;:true', $html);
    }

    public function test_device_specific_autoplay_can_be_overridden()
    {
        $images = collect(['https://foo/bar.jpg']);
        $conf = ['interval' => 1111, 'autoplay_desktop' => true, 'autoplay_mobile' => false];
        $html = view('components.image-scroller', ['images' => $images, 'config' => $conf])->render();

        $this->assertStringContainsString('&quot;autoplay_desktop&quot;:true', $html);
        $this->assertStringContainsString('&quot;autoplay_mobile&quot;:false', $html);
    }

    public function test_explicit_autoplay_overrides_device_flags()
    {
        $images = collect(['https://foo/bar.jpg']);
        $conf = ['interval' => 2222, 'autoplay' => true, 'autoplay_desktop' => false, 'autoplay_mobile' => false];
        $html = view('components.image-scroller', ['images' => $images, 'config' => $conf])->render();

        $this->assertStringContainsString('&quot;autoplay&quot;:true', $html);
        $this->assertStringNotContainsString('autoplay_desktop', $html);
        $this->assertStringNotContainsString('autoplay_mobile', $html);
    }}
