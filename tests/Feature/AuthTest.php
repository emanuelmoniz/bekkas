<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
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

        // Ensure DB-driven translations exist for auth-related UI used in this test
        \App\Models\StaticTranslation::create(['key' => 'auth.login', 'locale' => 'en-UK', 'value' => 'Log in']);
        \App\Models\StaticTranslation::create(['key' => 'auth.login', 'locale' => 'pt-PT', 'value' => 'Entrar']);
        \App\Models\StaticTranslation::create(['key' => 'auth.register', 'locale' => 'en-UK', 'value' => 'Register']);
        \App\Models\StaticTranslation::create(['key' => 'auth.register', 'locale' => 'pt-PT', 'value' => 'Registar']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password', 'locale' => 'en-UK', 'value' => 'Forgot your password? No problem.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password', 'locale' => 'pt-PT', 'value' => 'Esqueceu-se da sua palavra-passe? Sem problema.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password_desc', 'locale' => 'en-UK', 'value' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password_desc', 'locale' => 'pt-PT', 'value' => 'Esqueceu-se da sua palavra-passe? Sem problema. Indique-nos o seu endereço de email e enviaremos um link de redefinição que lhe permitirá escolher uma nova.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.reset_password_button', 'locale' => 'en-UK', 'value' => 'Reset Password']);
        \App\Models\StaticTranslation::create(['key' => 'auth.reset_password_button', 'locale' => 'pt-PT', 'value' => 'Redefinir Palavra-passe']);

        // sanity: ensure DB rows persisted
        $this->assertDatabaseHas('static_translations', ['key' => 'auth.login', 'locale' => 'en-UK']);
        $this->assertDatabaseHas('static_translations', ['key' => 'auth.login', 'locale' => 'pt-PT']);
        $this->assertDatabaseHas('static_translations', ['key' => 'auth.forgot_password', 'locale' => 'en-UK']);
        $this->assertDatabaseHas('static_translations', ['key' => 'auth.forgot_password', 'locale' => 'pt-PT']);
        $this->assertDatabaseHas('static_translations', ['key' => 'auth.forgot_password_desc', 'locale' => 'en-UK']);
        $this->assertDatabaseHas('static_translations', ['key' => 'auth.forgot_password_desc', 'locale' => 'pt-PT']);

        // Active site locale is English — set config so controller honours it and clear DB-driven translations cache
        config(['app.locale' => 'en-UK']);
        \Illuminate\Support\Facades\Cache::forget('static_translations_all');

        // Login page should use active site locale (English) — not the stored user language (Portuguese)
        $resp = $this->get(route('login'));
        $resp->assertSee('Log in')->assertDontSee('Entrar');

        // Register page should use active site locale
        $this->get(route('register'))->assertSee('Register')->assertDontSee('Registar');

        // ensure t() resolves DB translation as expected
        $this->assertEquals('Forgot your password? No problem.', t('auth.forgot_password'));

        // Forgot-password page should use active site locale (description)
        $this->get(route('password.request'))->assertSee('Forgot your password? No problem.')->assertDontSee('Esqueceu-se da sua palavra-passe? Sem problema.');

        // Password reset form (guest access) should use active site locale (check reset button)
        $this->get(route('password.reset', ['token' => 'dummy']))->assertSee('Reset Password')->assertDontSee('Redefinir Palavra-passe');
    }

    public function test_guest_forms_render_in_pt_when_site_locale_is_pt()
    {
        // Active site locale is Portuguese via session (guest)
        // Ensure DB-driven translations exist for the keys used by the auth views
        \App\Models\StaticTranslation::create(['key' => 'auth.login', 'locale' => 'en-UK', 'value' => 'Log in']);
        \App\Models\StaticTranslation::create(['key' => 'auth.login', 'locale' => 'pt-PT', 'value' => 'Entrar']);
        \App\Models\StaticTranslation::create(['key' => 'auth.register', 'locale' => 'en-UK', 'value' => 'Register']);
        \App\Models\StaticTranslation::create(['key' => 'auth.register', 'locale' => 'pt-PT', 'value' => 'Registar']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password', 'locale' => 'en-UK', 'value' => 'Forgot your password? No problem.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password', 'locale' => 'pt-PT', 'value' => 'Esqueceu-se da sua palavra-passe? Sem problema.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password_desc', 'locale' => 'en-UK', 'value' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.forgot_password_desc', 'locale' => 'pt-PT', 'value' => 'Esqueceu-se da sua palavra-passe? Sem problema. Indique-nos o seu endereço de email e enviaremos um link de redefinição que lhe permitirá escolher uma nova.']);
        \App\Models\StaticTranslation::create(['key' => 'auth.reset_password_button', 'locale' => 'en-UK', 'value' => 'Reset Password']);
        \App\Models\StaticTranslation::create(['key' => 'auth.reset_password_button', 'locale' => 'pt-PT', 'value' => 'Redefinir Palavra-passe']);

        \Illuminate\Support\Facades\Cache::forget('static_translations_all');

        $resp = $this->withSession(['locale' => 'pt-PT'])
            ->get(route('login'));
        $resp->assertSee('Entrar')->assertDontSee('Log in');

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

    public function test_password_reset_notification_locale_prefers_logged_in_user_language()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'locale-auth@example.com',
            'language' => 'en-UK',
        ]);

        config(['app.locale' => 'pt-PT']);
        session(['locale' => 'pt-PT']);

        $this->actingAs($user);
        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo(
            $user,
            \App\Notifications\ResetPasswordNotification::class,
            function ($notification) {
                return $notification->locale === 'en-UK';
            }
        );
    }

    public function test_password_reset_notification_locale_uses_session_when_guest()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'locale-guest@example.com',
            'language' => 'pt-PT',
        ]);

        config(['app.locale' => 'pt-PT']);
        session(['locale' => 'en-UK']);

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo(
            $user,
            \App\Notifications\ResetPasswordNotification::class,
            function ($notification) {
                return $notification->locale === 'en-UK';
            }
        );
    }

    public function test_password_reset_notification_locale_falls_back_to_app_default()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'locale-fallback@example.com',
            'language' => 'en-UK',
        ]);

        config(['app.locale' => 'pt-PT']);
        session()->forget('locale');

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo(
            $user,
            \App\Notifications\ResetPasswordNotification::class,
            function ($notification) {
                return $notification->locale === 'pt-PT';
            }
        );
    }
}
