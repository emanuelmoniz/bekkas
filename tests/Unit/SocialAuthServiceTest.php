<?php

namespace Tests\Unit;

use App\Exceptions\SocialAuthException;
use App\Services\SocialAuthService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_user_when_no_existing_user()
    {
        $providerUser = new class {
            public $id = 'p-1';
            public $name = 'New Social';
            public $email = 'new.social@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        $svc = new SocialAuthService();
        $user = $svc->findOrCreateUserFromProvider('google', $providerUser);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'new.social@example.com']);
        $this->assertNotNull($user->email_verified_at, 'Social-created user must be email-verified');
        $this->assertDatabaseHas('social_accounts', ['provider' => 'google', 'provider_id' => 'p-1']);
    }

    public function test_creates_user_when_no_existing_user_for_microsoft()
    {
        $providerUser = new class {
            public $id = 'ms-p-1';
            public $name = 'New MS Social';
            public $email = 'new.ms.social@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        $svc = new SocialAuthService();
        $user = $svc->findOrCreateUserFromProvider('microsoft', $providerUser);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'new.ms.social@example.com']);
        $this->assertNotNull($user->email_verified_at, 'Social-created MS user must be email-verified');
        $this->assertDatabaseHas('social_accounts', ['provider' => 'microsoft', 'provider_id' => 'ms-p-1']);
    }

    public function test_throws_when_existing_user_unverified()
    {
        $existing = User::factory()->unverified()->create(['email' => 'blocked@example.com']);

        $providerUser = new class {
            public $id = 'p-2';
            public $name = 'Blocked';
            public $email = 'blocked@example.com';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getAvatar() { return null; }
        };

        $this->expectException(SocialAuthException::class);
        $this->expectExceptionCode(SocialAuthException::UNVERIFIED_EMAIL);

        $svc = new SocialAuthService();
        $svc->findOrCreateUserFromProvider('google', $providerUser);
    }

    public function test_link_provider_to_user_creates_social_account()
    {
        $user = User::factory()->create(['email' => 'link@example.com']);

        $providerUser = new class {
            public $id = 'p-link-1';
            public $name = 'Linker';
            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return null; }
            public function getAvatar() { return null; }
        };

        $svc = new SocialAuthService();
        $svc->linkProviderToUser($user, 'google', $providerUser);

        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'p-link-1']);
    }

    public function test_link_throws_if_provider_taken_by_other_user()
    {
        $user1 = User::factory()->create(['email' => 'one@example.com']);
        $user2 = User::factory()->create(['email' => 'two@example.com']);

        \App\Models\SocialAccount::create(['user_id' => $user2->id, 'provider' => 'google', 'provider_id' => 'p-taken']);

        $providerUser = new class {
            public $id = 'p-taken';
            public function getId() { return $this->id; }
            public function getName() { return 'Taken'; }
            public function getEmail() { return null; }
            public function getAvatar() { return null; }
        };

        $this->expectException(SocialAuthException::class);
        $this->expectExceptionCode(SocialAuthException::PROVIDER_ALREADY_LINKED);

        $svc = new SocialAuthService();
        $svc->linkProviderToUser($user1, 'google', $providerUser);
    }

    public function test_unlink_provider_removes_record()
    {
        $user = User::factory()->create();
        $sa = \App\Models\SocialAccount::create(['user_id' => $user->id, 'provider' => 'google', 'provider_id' => 'p-unlink']);

        $svc = new SocialAuthService();
        $svc->unlinkProviderFromUser($user, 'google');

        $this->assertDatabaseMissing('social_accounts', ['id' => $sa->id]);
    }
}
