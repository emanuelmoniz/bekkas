<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermsPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure the terms page is accessible and contains the new sections.
     */
    public function test_terms_page_contains_new_sections()
    {
        // ensure static translations are available
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);

        // default locale in tests is pt-PT, so expect Portuguese section titles
        $response = $this->get('/terms');

        $response->assertStatus(200);
        // After seeding, t() returns the locale-specific value (not the bilingual fallback)
        $response->assertSee('Termos de Serviço');
        $response->assertSee('Política de Devoluções e Reembolsos');
        $response->assertSee('Política de Envios');

        // also check English locale just in case
        app()->setLocale('en-UK');
        $response = $this->get('/terms');
        $response->assertSee('Return and Refunds Policy');
        $response->assertSee('Shipping Policy');
    }

    /**
     * Footer should include updated links pointing to the terms anchors.
     */
    public function test_footer_links_updated()
    {
        $this->seed(\Database\Seeders\StaticTranslationsSeeder::class);
        app()->setLocale('en-UK');

        $response = $this->get('/');

        $response->assertStatus(200);
        // footer.terms en-UK value is 'Service Terms' (locale-specific after seeding)
        $response->assertSee('Service Terms');
        $response->assertSee('Return and Refunds Policy');
        $response->assertSee('Shipping Policy');
        $response->assertSee('Privacy Policy');
    }
}
