<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Models\Configuration;
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
        Mail::assertQueued(\App\Mail\ContactMessage::class, 1);
        Mail::assertQueued(\App\Mail\ContactConfirmation::class, 1);
    }

    public function test_contact_form_respects_send_mails_switch()
    {
        Mail::fake();

        // Disable emails via DB configuration and re-run provider so runtime config is updated
        Configuration::create(['send_mails_enabled' => false]);
        $this->app->getProvider(\App\Providers\ConfigurationServiceProvider::class)->boot();

        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Calls remain queued (we removed per-call guards) but the global mailer is switched
        $this->assertFalse(config('mail.enabled'));
        $this->assertEquals('disabled', config('mail.default'));

        Mail::assertQueued(\App\Mail\ContactMessage::class, 1);
        Mail::assertQueued(\App\Mail\ContactConfirmation::class, 1);
    }

    public function test_contact_form_rejects_invalid_email()
    {
        Mail::fake();

        $response = $this->post(route('contact.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email-without-tld@gmail',
            'message' => 'Hello',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHas('error');

        // Ensure no emails were queued on validation failure
        Mail::assertNothingQueued();
    }
}
