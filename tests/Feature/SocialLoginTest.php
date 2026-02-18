<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
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

        $this->followingRedirects()->get(route('login.provider.callback', 'google') . '?code=dummy')
            ->assertOk()
            ->assertSeeText(''); // page body not important here

        $this->assertDatabaseHas('users', ['email' => 'social@example.com']);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'google', 'provider_id' => 'g-12345']);

        $this->assertAuthenticated();
        $this->assertEquals('social@example.com', auth()->user()->email);
        $this->assertNotNull(auth()->user()->email_verified_at, 'Social-created user must be email-verified');
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

        $resp = $this->get(route('login.provider.callback', 'google') . '?code=dummy');

        $resp->assertRedirect(route('login'));
        $resp->assertSessionHas('unverified_email', 'unverified@example.com');
        $this->assertGuest();
    }

    public function test_socialite_exception_shows_generic_error_on_login()
    {
        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();
        Socialite::shouldReceive('user')->andThrow(new \Exception('oauth error'));
        // Expect we log the exception for diagnostics
        // (do not mock Log here so we can see full error in test logs)
        $resp = $this->get(route('login.provider.callback', 'google') . '?code=dummy');
        $resp->assertRedirect(route('login'));

        // login page should show social error message
        $this->get(route('login'))->assertSeeText(t('auth.social_failed'));
    }

    public function test_callback_with_error_param_logs_and_redirects()
    {
        // incoming callback contains an error (provider->callback)
        Log::shouldReceive('debug')->once();
        Log::shouldReceive('warning')->once();

        $resp = $this->get(route('login.provider.callback', 'google') . '?error=access_denied&error_description=consent_required');

        $resp->assertRedirect(route('login'));
        $this->get(route('login'))->assertSeeText(t('auth.social_failed'));
    }

    public function test_callback_missing_code_logs_and_redirects()
    {
        // callback with no code (and no explicit error) — should be detected early
        Log::shouldReceive('debug')->once();
        Log::shouldReceive('warning')->once();

        $resp = $this->get(route('login.provider.callback', 'google') . '?state=foo');

        $resp->assertRedirect(route('login'));
        $this->get(route('login'))->assertSeeText(t('auth.social_failed'));
    }

    public function test_callback_with_empty_code_logs_and_redirects()
    {
        // callback where the 'code' parameter is present but empty — treat as missing
        Log::shouldReceive('debug')->once();
        Log::shouldReceive('warning')->once();

        $resp = $this->get(route('login.provider.callback', 'google') . '?code=');

        $resp->assertRedirect(route('login'));
        $this->get(route('login'))->assertSeeText(t('auth.social_failed'));
    }

    /* --------------------------- Microsoft --------------------------- */

    public function test_microsoft_redirect_route_exists()
    {
        $resp = $this->get(route('login.provider', 'microsoft'));
        $resp->assertStatus(302);
    }

    public function test_microsoft_callback_creates_user_and_links_social_account()
    {
        Socialite::shouldReceive('driver')->with('microsoft')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'ms-12345';
            public $name = 'Microsoft User';
            public $email = 'msocial@example.com';
            public $avatar = 'http://avatar-ms';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return $this->avatar; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $this->followingRedirects()->get(route('login.provider.callback', 'microsoft') . '?code=dummy')
            ->assertOk();

        $this->assertDatabaseHas('users', ['email' => 'msocial@example.com']);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'microsoft', 'provider_id' => 'ms-12345']);

        $this->assertAuthenticated();
        $this->assertEquals('msocial@example.com', auth()->user()->email);
        $this->assertNotNull(auth()->user()->email_verified_at, 'Social-created MS user must be email-verified');
    }

    public function test_microsoft_data_uri_avatar_is_saved_to_public_disk_and_db()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        Socialite::shouldReceive('driver')->with('microsoft')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        // tiny 1x1 PNG data URI
        $dataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAn8B9QJ3jQAAAABJRU5ErkJggg==';

        $providerUser = new class($dataUri) {
            public $id = 'ms-12345';
            public $name = 'Microsoft Avatar';
            public $email = 'msavatar@example.com';
            public $avatar;
            public function __construct($avatar) { $this->avatar = $avatar; }
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return $this->avatar; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $this->followingRedirects()->get(route('login.provider.callback', 'microsoft') . '?code=dummy')
            ->assertOk();

        $this->assertDatabaseHas('users', ['email' => 'msavatar@example.com']);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'microsoft', 'provider_id' => 'ms-12345']);

        $account = \App\Models\SocialAccount::where('provider','microsoft')->where('provider_id','ms-12345')->first();
        $this->assertNotNull($account->avatar);
        $this->assertStringStartsWith('/storage/avatars/social/microsoft_ms-12345_', $account->avatar);

        $diskPath = substr($account->avatar, strlen('/storage/'));
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($diskPath);
    }

    public function test_microsoft_callback_blocks_existing_unverified_account()
    {
        $user = User::factory()->unverified()->create(['email' => 'unverified-ms@example.com']);

        Socialite::shouldReceive('driver')->with('microsoft')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();

        $providerUser = new class {
            public $id = 'ms-9999';
            public $name = 'Unverified MS';
            public $email = 'unverified-ms@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        Socialite::shouldReceive('user')->andReturn($providerUser);

        $resp = $this->get(route('login.provider.callback', 'microsoft') . '?code=dummy');

        $resp->assertRedirect(route('login'));
        $resp->assertSessionHas('unverified_email', 'unverified-ms@example.com');
        $this->assertGuest();
    }

    public function test_microsoft_callback_with_empty_code_logs_and_redirects()
    {
        Log::shouldReceive('debug')->once();
        Log::shouldReceive('warning')->once();

        $resp = $this->get(route('login.provider.callback', 'microsoft') . '?code=');

        $resp->assertRedirect(route('login'));
        $this->get(route('login'))->assertSeeText(t('auth.social_failed'));
    }
}
