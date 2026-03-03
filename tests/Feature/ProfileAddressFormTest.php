<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAddressFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_shows_new_address_fields_and_can_store()
    {
        $user = User::factory()->create();

        $country = Country::firstOrCreate(
            ['iso_alpha2' => 'PT'],
            ['name_pt' => 'Portugal', 'name_en' => 'Portugal', 'country_code' => '351', 'is_active' => true]
        );

        // open profile page and ensure form inputs are present
        $res = $this->actingAs($user)->get(route('profile.edit'));
        $res->assertStatus(200);
        $content = $res->getContent();
        $this->assertStringContainsString('name="title"', $content);
        $this->assertStringContainsString('name="address_line_1"', $content);
        // we expect label markup for the address form and required star
        $this->assertStringContainsString('<label', $content);
        $this->assertStringContainsString('text-status-error', $content);
        // new address form should not be prefilled with any existing address
        $this->assertFalse(str_contains($content, 'value="Work"'), 'New address form unexpectedly prefilled');

        // submit a new address
        $this->actingAs($user)
            ->post(route('addresses.store'), [
                'title' => 'Work',
                'address_line_1' => 'Rua Teste 2',
                'postal_code' => '1234-567',
                'city' => 'Lisbon',
                'country_id' => $country->id,
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', ['title' => 'Work', 'user_id' => $user->id]);
    }
}
