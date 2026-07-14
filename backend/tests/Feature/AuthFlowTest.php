<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_is_redirected_to_profile(): void
    {
        $response = $this->post('/register', [
            'name' => 'Good Customer',
            'email' => 'customer@goodfriendshop.test',
            'phone' => '0899999999',
            'line_id' => 'goodfriend',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'customer@goodfriendshop.test')->first();

        $response->assertRedirect(route('profile.show'));
        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_CUSTOMER, $user->role);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'member@goodfriendshop.test',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_CUSTOMER,
        ]);

        $response = $this->post('/login', [
            'email' => 'member@goodfriendshop.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Admin Center')
            ->assertSee('ภาพรวมระบบ')
            ->assertSee(route('admin.products.index'), false)
            ->assertSee(route('admin.users.index'), false);
    }
}
