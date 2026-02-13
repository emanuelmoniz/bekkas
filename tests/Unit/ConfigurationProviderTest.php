<?php

namespace Tests\Unit;

use App\Models\Configuration;
use App\Providers\ConfigurationServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfigurationProviderTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function database_configuration_overrides_config_values()
    {
        // Create a DB configuration with some fields populated
        $cfg = Configuration::create([
            'app_name' => 'DB App Name',
            'mail_admin' => 'admin-db@example.test',
            'easypay_enabled' => true,
            'easypay_payment_methods' => '["mb","cc"]',
            'easypay_sdk_url' => 'https://sdk.db.test/easypay.js',
        ]);

        // Re-run provider boot so it picks up the DB row
        $provider = $this->app->getProvider(ConfigurationServiceProvider::class);
        $provider->boot();

        $this->assertEquals('DB App Name', config('app.name'));
        $this->assertEquals('admin-db@example.test', config('mail.admin_address'));
        $this->assertTrue(config('easypay.enabled'));
        $this->assertEquals('["mb","cc"]', config('easypay.payment_methods'));
        $this->assertEquals('https://sdk.db.test/easypay.js', config('easypay.sdk_url'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function if_configuration_is_missing_env_values_remain_as_fallback()
    {
        // ensure no DB configuration exists
        $this->assertDatabaseCount('configurations', 0);

        // Provider boot should do nothing harmful
        $provider = $this->app->getProvider(ConfigurationServiceProvider::class);
        $provider->boot();

        // Values should remain (env defaults as specified in config files)
        $this->assertEquals(env('APP_NAME'), config('app.name'));
        $this->assertEquals(env('EASYPAY_PAYMENT_METHODS', '[]'), config('easypay.payment_methods'));
    }
}
