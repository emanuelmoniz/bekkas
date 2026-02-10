<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_area_redirects_guests_to_login()
    {
        $this->get('/admin')
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_users_are_redirected_to_public_products()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('products.index'));
    }

    public function test_admin_user_can_access_admin_dashboard()
    {
        $user = User::factory()->create();

        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->roles()->attach($role->id);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }
}
