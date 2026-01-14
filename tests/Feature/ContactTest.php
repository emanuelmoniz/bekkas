<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not available');
        }

        parent::setUp();

        // Ensure reCAPTCHA validation is disabled for tests even if secrets exist
        config(['services.recaptcha.secret_key' => null]);
    }

    public function test_contact_form_sends_emails()
    {
        Mail::fake();

        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello',
            // reCAPTCHA skipped in tests when secret not set
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Two messages should be sent: one to the admin and one to the sender
        Mail::assertSent(\App\Mail\ContactMessage::class, 1);
        Mail::assertSent(\App\Mail\ContactConfirmation::class, 1);
    }
}
