<?php

namespace Tests\Feature;

use App\Models\Configuration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConfigurationUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_set_tax_enabled_and_it_is_persisted()
    {
        $admin = User::factory()->create();
        $role = \App\Models\Role::firstOrCreate(['name' => 'admin']);
        $admin->roles()->attach($role->id);

        $resp = $this->actingAs($admin)->put(route('admin.configurations.update'), [
            'tax_enabled' => '1',
        ]);

        $resp->assertRedirect();

        $this->assertDatabaseHas('configurations', [
            'tax_enabled' => 1,
        ]);

        $cfg = Configuration::latest()->first();
        $this->assertTrue((bool) $cfg->tax_enabled);
    }
}
