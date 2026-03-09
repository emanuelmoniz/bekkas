<?php

namespace Tests\Unit;

use App\Models\StaticTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LegalAutoMergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_replacements_are_auto_merged_into_t()
    {
        // Ensure predictable config values used by legal_replacements()
        config()->set('app.name', 'MyApp');
        config()->set('app.company_contact_email', 'contact@example.test');

        // Create a DB translation containing legal placeholders
        StaticTranslation::create([
            'key' => 'legal.test_auto_merge',
            'locale' => app()->getLocale(),
            'context' => null,
            'value' => 'Company: :company; Email: :contact_email',
        ]);

        // Clear cached translations to force re-read
        Cache::forget('static_translations_all');

        $out = t('legal.test_auto_merge');

        $this->assertStringContainsString('MyApp', $out);
        $this->assertStringContainsString('contact@example.test', $out);
    }
}
