<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

    public function test_google_callback_creates_customer_and_redirects_with_token(): void
    {
        config([
            'services.google.client_id' => 'google-client-id',
            'services.google.client_secret' => 'google-client-secret',
            'services.google.redirect' => 'http://127.0.0.1:8001/auth/google/callback',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'google-access-token',
                'token_type' => 'Bearer',
            ]),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => 'google-user-123',
                'email' => 'google-user@goodfriendshop.test',
                'email_verified' => true,
                'name' => 'Google User',
                'picture' => 'https://example.com/avatar.png',
            ]),
        ]);

        $response = $this
            ->withSession(['oauth_google_state' => 'valid-state'])
            ->get('/auth/google/callback?code=valid-code&state=valid-state');

        $this->assertStringStartsWith(
            'http://127.0.0.1:3001/login/social-callback#payload=',
            $response->headers->get('Location'),
        );
        $this->assertDatabaseHas('users', [
            'email' => 'google-user@goodfriendshop.test',
            'name' => 'Google User',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'google',
            'provider_user_id' => 'google-user-123',
            'email' => 'google-user@goodfriendshop.test',
        ]);
    }

    public function test_google_callback_rejects_invalid_state(): void
    {
        $this
            ->withSession(['oauth_google_state' => 'valid-state'])
            ->get('/auth/google/callback?code=valid-code&state=wrong-state')
            ->assertRedirect('http://127.0.0.1:3001/login?social_error=invalid_state');
    }

    public function test_line_callback_creates_customer_and_redirects_with_token(): void
    {
        config([
            'services.line.client_id' => 'line-channel-id',
            'services.line.client_secret' => 'line-channel-secret',
            'services.line.redirect' => 'http://127.0.0.1:8001/auth/line/callback',
        ]);

        Http::fake([
            'https://api.line.me/oauth2/v2.1/token' => Http::response([
                'access_token' => 'line-access-token',
                'token_type' => 'Bearer',
            ]),
            'https://api.line.me/oauth2/v2.1/userinfo' => Http::response([
                'sub' => 'line-user-123',
                'email' => 'line-user@goodfriendshop.test',
                'name' => 'LINE User',
                'picture' => 'https://example.com/line-avatar.png',
            ]),
        ]);

        $response = $this
            ->withSession(['oauth_line_state' => 'valid-state'])
            ->get('/auth/line/callback?code=valid-code&state=valid-state');

        $this->assertStringStartsWith(
            'http://127.0.0.1:3001/login/social-callback#payload=',
            $response->headers->get('Location'),
        );
        $this->assertDatabaseHas('users', [
            'email' => 'line-user@goodfriendshop.test',
            'name' => 'LINE User',
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'line',
            'provider_user_id' => 'line-user-123',
            'email' => 'line-user@goodfriendshop.test',
        ]);
    }

    public function test_line_callback_rejects_invalid_state(): void
    {
        $this
            ->withSession(['oauth_line_state' => 'valid-state'])
            ->get('/auth/line/callback?code=valid-code&state=wrong-state')
            ->assertRedirect('http://127.0.0.1:3001/login?social_error=invalid_state');
    }
}
