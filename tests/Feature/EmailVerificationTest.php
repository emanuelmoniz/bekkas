<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_verification_and_blocks_login()
    {
        // Ensure reCAPTCHA is disabled in tests
        config(['services.recaptcha.secret_key' => null]);
        Notification::fake();

        $password = 'Password1!';

        $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'email_confirmation' => 'jane@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect(route('verification.sent'));

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertEquals(app()->getLocale(), $user->language);

        Notification::assertSentTo($user, VerifyEmail::class);

        // Attempt to login before verification -> blocked with validation error
        $this->post(route('login'), [
            'email' => 'jane@example.com',
            'password' => $password,
        ])->assertSessionHasErrors('email');
    }

    public function test_guest_can_resend_activation_for_unverified_account()
    {
        // Ensure reCAPTCHA is disabled in tests
        config(['services.recaptcha.secret_key' => null]);
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => null, 'email' => 'resend@example.com']);

        $this->post(route('verification.resend.guest'), ['email' => $user->email])
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_uses_active_locale_for_user_language_and_sends_verification()
    {
        // Ensure reCAPTCHA is disabled in tests
        config(['services.recaptcha.secret_key' => null]);
        Notification::fake();

        app()->setLocale('pt-PT');

        $password = 'LocalePass1!';

        $this->post(route('register'), [
            'name' => 'Locale User',
            'email' => 'locale@example.com',
            'email_confirmation' => 'locale@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect(route('verification.sent'));

        $user = User::where('email', 'locale@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('pt-PT', $user->language);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_link_marks_account_verified_and_allows_login()
    {
        // Ensure reCAPTCHA is disabled in tests
        config(['services.recaptcha.secret_key' => null]);
        Notification::fake();

        $password = 'Password1!';

        $this->post(route('register'), [
            'name' => 'Mark Verify',
            'email' => 'mark@example.com',
            'email_confirmation' => 'mark@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect(route('verification.sent'));

        $user = User::where('email', 'mark@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(app()->getLocale(), $user->language);

        $verificationUrl = null;

        Notification::assertSentTo($user, VerifyEmail::class, function ($notification, $channels) use (&$verificationUrl, $user) {
            $mail = $notification->toMail($user);
            $verificationUrl = $mail->actionUrl;
            return true;
        });

        // Simulate visiting the signed verification URL (guest)
        $this->get($verificationUrl)->assertRedirect('/login?verified=1');

        $this->assertNotNull($user->fresh()->email_verified_at);

        // The login page should show a localized "verified" success message in default locale
        $expected = t('auth.verification_verified') ?: 'Your email address has been verified.';
        $this->get('/login?verified=1')->assertSee($expected);

        // When the site locale is Portuguese, request the verification URL while the session locale is pt-PT
        $this->withSession(['locale' => 'pt-PT'])->get($verificationUrl)->assertRedirect('/login?verified=1');

        // The flashed message should now be visible on the next request and translated
        app()->setLocale('pt-PT');
        $this->get('/login?verified=1')->assertSee(t('auth.verification_verified'));

        // Now login should succeed
        $this->post(route('login'), [
            'email' => 'mark@example.com',
            'password' => $password,
        ])->assertSessionDoesntHaveErrors();
    }
}
