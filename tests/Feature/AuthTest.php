<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        $password = 'Abcde.123!';

        // Use the factory so password hashing/casts match application behaviour
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt($password),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials()
    {
        $this->post(route('login'), [
            'email' => 'noone@example.com',
            'password' => 'invalid',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_register_creates_user_and_logs_in()
    {
        // ensure 'client' role exists to avoid missing role lookup side-effects
        Role::firstOrCreate(['name' => 'client']);

        // Avoid external side-effects during registration
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Event::fake();
        config(['services.recaptcha.secret_key' => null]);

        $password = 'MyPass.123!';
        $response = $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'email_confirmation' => 'new@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ]);

        // New behaviour: user must verify email before being able to sign-in.
        $response->assertRedirect(route('verification.sent'));
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);

        // user should NOT be logged in yet
        $this->assertGuest();
    }

    public function test_register_requires_terms_and_privacy_acceptance()
    {
        Role::firstOrCreate(['name' => 'client']);
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Event::fake();
        config(['services.recaptcha.secret_key' => null]);

        $password = 'MyPass.123!';
        $response = $this->post(route('register'), [
            'name' => 'No Accept',
            'email' => 'noaccept@example.com',
            'email_confirmation' => 'noaccept@example.com',
            'password' => $password,
            'password_confirmation' => $password,
            // deliberately omit accept_terms / accept_privacy
        ]);

        $response->assertSessionHasErrors(['accept_terms', 'accept_privacy']);
        $this->assertDatabaseMissing('users', ['email' => 'noaccept@example.com']);
    }

    public function test_guest_forms_use_active_site_locale_not_stored_user_language()
    {
        // Create a user that has a stored language but do not authenticate them.
        $user = \App\Models\User::factory()->create(['language' => 'pt-PT', 'email' => 'locale-user@example.com']);

        // Active site locale is English
        app()->setLocale('en-UK');

        // Login page should use active site locale (English) — not the stored user language (Portuguese)
        $this->get(route('login'))->assertSee('Log in')->assertDontSee('Entrar');

        // Register page should use active site locale
        $this->get(route('register'))->assertSee('Register')->assertDontSee('Registar');

        // Forgot-password page should use active site locale (description)
        $this->get(route('password.request'))->assertSee('Forgot your password? No problem.')->assertDontSee('Esqueceu-se da sua palavra-passe? Sem problema.');

        // Password reset form (guest access) should use active site locale (check reset button)
        $this->get(route('password.reset', ['token' => 'dummy']))->assertSee('Reset Password')->assertDontSee('Redefinir Palavra-passe');
    }

    public function test_guest_forms_render_in_pt_when_site_locale_is_pt()
    {
        // Active site locale is Portuguese via session (guest)
        $this->withSession(['locale' => 'pt-PT'])
            ->get(route('login'))
            ->assertSee('Entrar')
            ->assertDontSee('Log in');

        $this->withSession(['locale' => 'pt-PT'])
            ->get(route('register'))
            ->assertSee('Registar')
            ->assertDontSee('Register');

        $this->withSession(['locale' => 'pt-PT'])
            ->get(route('password.request'))
            ->assertSee('Esqueceu-se da sua palavra-passe? Sem problema.')
            ->assertDontSee('Forgot your password? No problem.');

        $this->withSession(['locale' => 'pt-PT'])
            ->get(route('password.reset', ['token' => 'dummy']))
            ->assertSee('Redefinir Palavra-passe')
            ->assertDontSee('Reset Password');
    }

    public function test_logout_clears_session()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
