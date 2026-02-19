<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class SocialLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_link_google_account()
    {
        $user = User::factory()->create(['email' => 'me@example.com']);

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'g-link-1';
            public $name = 'Link User';
            public $email = 'me@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $this->actingAs($user)->get(route('login.provider.callback', 'google') . '?code=dummy')
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'g-link-1']);
    }

    public function test_link_fails_if_provider_already_linked_to_other_user()
    {
        $user1 = User::factory()->create(['email' => 'one@example.com']);
        $user2 = User::factory()->create(['email' => 'two@example.com']);

        SocialAccount::create(['user_id' => $user2->id, 'provider' => 'google', 'provider_id' => 'g-dup']);

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'g-dup';
            public $name = 'Dup';
            public $email = 'two@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $resp = $this->actingAs($user1)->get(route('login.provider.callback', 'google') . '?code=dummy');
        $resp->assertRedirect(route('profile.edit'));
        $this->assertTrue(session()->has('errors'));
        $errors = session('errors')->get('social');
        $this->assertNotEmpty($errors);
        $this->assertEquals(t('profile.provider_already_linked'), $errors[0]);

        // ensure existing link unchanged and no link created for user1
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user2->id, 'provider' => 'google', 'provider_id' => 'g-dup']);
        $this->assertDatabaseMissing('social_accounts', ['user_id' => $user1->id, 'provider_id' => 'g-dup']);
    }

    public function test_authenticated_user_can_unlink_provider()
    {
        $user = User::factory()->create();
        $sa = SocialAccount::create(['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'g-unlink']);

        $this->actingAs($user)->delete(route('profile.social.unlink', 'google'))
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseMissing('social_accounts', ['id' => $sa->id]);
    }

    public function test_authenticated_user_can_link_microsoft_account()
    {
        $user = User::factory()->create(['email' => 'me-ms@example.com']);

        Socialite::shouldReceive('driver')->with('microsoft')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'ms-link-1';
            public $name = 'MS Link User';
            public $email = 'me-ms@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $this->actingAs($user)->get(route('login.provider.callback', 'microsoft') . '?code=dummy')
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'microsoft', 'provider_id' => 'ms-link-1']);
    }

    public function test_link_fails_if_provider_already_linked_to_other_user_microsoft()
    {
        $user1 = User::factory()->create(['email' => 'one-ms@example.com']);
        $user2 = User::factory()->create(['email' => 'two-ms@example.com']);

        SocialAccount::create(['user_id' => $user2->id, 'provider' => 'microsoft', 'provider_id' => 'ms-dup']);

        Socialite::shouldReceive('driver')->with('microsoft')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'ms-dup';
            public $name = 'Dup MS';
            public $email = 'two-ms@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $resp = $this->actingAs($user1)->get(route('login.provider.callback', 'microsoft') . '?code=dummy');
        $resp->assertRedirect(route('profile.edit'));
        $this->assertTrue(session()->has('errors'));
        $errors = session('errors')->get('social');
        $this->assertNotEmpty($errors);
        $this->assertEquals(t('profile.provider_already_linked'), $errors[0]);

        // ensure existing link unchanged and no link created for user1
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user2->id, 'provider' => 'microsoft', 'provider_id' => 'ms-dup']);
        $this->assertDatabaseMissing('social_accounts', ['user_id' => $user1->id, 'provider_id' => 'ms-dup']);
    }
}
