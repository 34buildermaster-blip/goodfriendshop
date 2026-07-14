<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_member_list(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create([
            'name' => 'Good Customer',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('จัดการสมาชิก')
            ->assertSee('Good Customer');
    }

    public function test_admin_can_create_member(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Member',
            'email' => 'new-member@goodfriendshop.test',
            'phone' => '0800000000',
            'line_id' => 'newmember',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'new-member@goodfriendshop.test',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_update_member_and_suspend_login(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create([
            'email' => 'member@goodfriendshop.test',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $member), [
            'name' => 'Suspended Member',
            'email' => 'member@goodfriendshop.test',
            'phone' => '0811111111',
            'line_id' => 'suspended',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_SUSPENDED,
            'password' => null,
            'password_confirmation' => null,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Suspended Member',
            'status' => User::STATUS_SUSPENDED,
        ]);

        auth()->logout();

        $this->post('/login', [
            'email' => 'member@goodfriendshop.test',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_customer_cannot_manage_members(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertStatus(422);
    }
}
