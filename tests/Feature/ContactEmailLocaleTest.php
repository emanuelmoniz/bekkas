<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactEmailLocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure reCAPTCHA is disabled in tests (same as other contact tests)
        config(['services.recaptcha.secret_key' => null]);
    }

    public function test_contact_admin_email_is_always_english()
    {
        Mail::fake();

        // Simulate site in Portuguese
        app()->setLocale('pt-PT');

        $resp = $this->post(route('contact.store'), [
            'name' => 'Rui',
            'email' => 'rui@gmail.com',
            'message' => 'Olá',
        ]);

        $resp->assertRedirect();

        Mail::assertQueued(\App\Mail\ContactMessage::class, function ($mail) {
            return ($mail->locale === 'en-UK' || $mail->locale === 'en');
        });
    }

    public function test_contact_confirmation_respects_authenticated_user_language()
    {
        Mail::fake();

        $user = User::factory()->create(['language' => 'en-UK', 'email' => 'testuser@gmail.com']);
        $this->actingAs($user);

        $resp = $this->post(route('contact.store'), [
            'name' => $user->name,
            'email' => $user->email,
            'message' => 'Hi',
        ]);

        $resp->assertRedirect();

        Mail::assertQueued(\App\Mail\ContactConfirmation::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && ($mail->locale === 'en-UK' || $mail->locale === 'en');
        });
    }
}
