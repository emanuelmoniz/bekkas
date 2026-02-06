<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        $password = 'Abcde.123!';
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make($password),
            'is_active' => true,
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
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);

        $user = User::where('email', 'new@example.com')->first();
        $this->assertAuthenticatedAs($user);
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
