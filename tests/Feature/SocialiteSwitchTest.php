<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialiteSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_google_hides_login_button_and_route_is_404()
    {
        config(['services.google.enabled' => false]);

        $this->get(route('login'))
            ->assertDontSeeText(t('auth.continue_with_google'));

        $this->get('/login/google')
            ->assertStatus(404);
    }

    public function test_disabled_microsoft_hides_login_button_and_route_is_404()
    {
        config(['services.microsoft.enabled' => false]);

        $this->get(route('login'))
            ->assertDontSeeText(t('auth.continue_with_microsoft'));

        $this->get('/login/microsoft')
            ->assertStatus(404);
    }

    public function test_profile_link_hidden_when_provider_disabled_but_unlink_still_available_google()
    {
        $user = User::factory()->create();
        config(['services.google.enabled' => false]);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertDontSee('/profile/social/google/link')
            ->assertSeeText(t('profile.provider_disabled'));

        // If user already has a linked account they should still be able to unlink
        SocialAccount::create(['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'g-1']);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertSeeText(t('profile.unlink_account'));

        $this->actingAs($user)->delete(route('profile.social.unlink', 'google'))
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseMissing('social_accounts', ['user_id' => $user->id, 'provider' => 'google']);
    }

    public function test_profile_link_hidden_when_provider_disabled_but_unlink_still_available_microsoft()
    {
        $user = User::factory()->create();
        config(['services.microsoft.enabled' => false]);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertDontSee('/profile/social/microsoft/link')
            ->assertSeeText(t('profile.provider_disabled'));

        SocialAccount::create(['user_id' => $user->id, 'provider' => 'microsoft', 'provider_id' => 'ms-1']);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertSeeText(t('profile.unlink_account'));

        $this->actingAs($user)->delete(route('profile.social.unlink', 'microsoft'))
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseMissing('social_accounts', ['user_id' => $user->id, 'provider' => 'microsoft']);
    }
}
