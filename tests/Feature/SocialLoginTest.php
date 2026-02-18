<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_route_exists()
    {
        $resp = $this->get(route('login.provider', 'google'));
        $resp->assertStatus(302);
    }

    public function test_google_callback_creates_user_and_links_social_account()
    {
        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'g-12345';
            public $name = 'Google User';
            public $email = 'social@example.com';
            public $avatar = 'http://avatar';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return $this->avatar; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $this->followingRedirects()->get(route('login.provider.callback', 'google'))
            ->assertOk()
            ->assertSeeText(''); // page body not important here

        $this->assertDatabaseHas('users', ['email' => 'social@example.com']);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'google', 'provider_id' => 'g-12345']);

        $this->assertAuthenticated();
        $this->assertEquals('social@example.com', auth()->user()->email);
    }

    public function test_google_callback_blocks_existing_unverified_account()
    {
        $user = User::factory()->unverified()->create(['email' => 'unverified@example.com']);

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'g-9999';
            public $name = 'Unverified';
            public $email = 'unverified@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $resp = $this->get(route('login.provider.callback', 'google'));

        $resp->assertRedirect(route('login'));
        $resp->assertSessionHas('unverified_email', 'unverified@example.com');
        $this->assertGuest();
    }
}
