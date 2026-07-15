<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GamePackage;
use App\Models\Order;
use App\Models\PremiumApp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_order_from_active_package(): void
    {
        $package = $this->createActivePackage();

        $response = $this->postJson('/api/orders', [
            'game_package_id' => $package->id,
            'customer_name' => 'Somchai',
            'customer_email' => 'somchai@example.com',
            'customer_phone' => '0812345678',
            'player_identifier' => '123456789',
            'server_identifier' => '1001',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.game_name', 'Mobile Legends')
            ->assertJsonPath('data.package_name', '257 Diamonds')
            ->assertJsonPath('data.status', Order::STATUS_PENDING);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Somchai',
            'player_identifier' => '123456789',
            'game_name' => 'Mobile Legends',
            'package_name' => '257 Diamonds',
        ]);
    }

    public function test_guest_can_create_order_from_active_premium_app(): void
    {
        $app = PremiumApp::create([
            'name' => 'Spotify Premium 30 Days',
            'slug' => 'spotify-premium-30-days',
            'description' => 'Premium app package',
            'price' => 89,
            'currency' => 'THB',
            'status' => PremiumApp::STATUS_ACTIVE,
        ]);

        $response = $this->postJson('/api/orders', [
            'premium_app_id' => $app->slug,
            'customer_name' => 'Somchai',
            'customer_email' => 'somchai@example.com',
            'customer_phone' => '0812345678',
            'player_identifier' => 'somchai@example.com',
            'customer_note' => 'Use on mobile',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.game_name', 'Premium App')
            ->assertJsonPath('data.package_name', 'Spotify Premium 30 Days')
            ->assertJsonPath('data.status', Order::STATUS_PENDING);

        $this->assertDatabaseHas('orders', [
            'premium_app_id' => $app->id,
            'customer_name' => 'Somchai',
            'player_identifier' => 'somchai@example.com',
            'game_name' => 'Premium App',
            'package_name' => 'Spotify Premium 30 Days',
        ]);
    }

    public function test_customer_can_register_login_and_see_own_orders(): void
    {
        $package = $this->createActivePackage();

        $register = $this->postJson('/api/auth/register', [
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'phone' => '0899999999',
            'password' => 'password123',
        ])->assertCreated();

        $token = $register->json('data.token');

        $this->postJson('/api/orders', [
            'game_package_id' => $package->id,
            'player_identifier' => '999888777',
            'server_identifier' => '2002',
        ], ['Authorization' => "Bearer {$token}"])->assertCreated();

        $this->getJson('/api/my/orders', ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.customer_name', 'Customer One')
            ->assertJsonPath('data.0.player_identifier', '999888777');

        $login = $this->postJson('/api/auth/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ])->assertOk();

        $this->assertNotEmpty($login->json('data.token'));
    }

    public function test_customer_can_update_profile_and_view_order_detail(): void
    {
        Storage::fake('public');

        $package = $this->createActivePackage();
        $register = $this->postJson('/api/auth/register', [
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'phone' => '0899999999',
            'password' => 'password123',
        ])->assertCreated();
        $token = $register->json('data.token');

        $this->patchJson('/api/auth/me', [
            'name' => 'Customer Updated',
            'email' => 'updated@example.com',
            'phone' => '0811111111',
            'line_id' => 'goodfriendline',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.name', 'Customer Updated')
            ->assertJsonPath('data.line_id', 'goodfriendline');

        $avatarResponse = $this->post('/api/auth/me/avatar', [
            'avatar' => UploadedFile::fake()->createWithContent(
                'avatar.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
            ),
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}",
        ])->assertOk();

        $avatarPath = User::query()->where('email', 'updated@example.com')->value('avatar_path');

        $this->assertNotEmpty($avatarResponse->json('data.avatar_url'));
        $this->assertNotEmpty($avatarPath);
        Storage::disk('public')->assertExists($avatarPath);

        $orderNumber = $this->postJson('/api/orders', [
            'game_package_id' => $package->id,
            'player_identifier' => '999888777',
            'server_identifier' => '2002',
        ], ['Authorization' => "Bearer {$token}"])
            ->assertCreated()
            ->json('data.order_number');

        $this->getJson("/api/my/orders/{$orderNumber}", ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('data.customer_name', 'Customer Updated')
            ->assertJsonPath('data.status_steps.0.label', 'รับออเดอร์')
            ->assertJsonPath('data.next_action', 'รอทีมงานตรวจสอบข้อมูลและยอดชำระ');
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $package = $this->createActivePackage();
        $order = Order::create([
            'game_id' => $package->game_id,
            'game_package_id' => $package->id,
            'customer_name' => 'Somchai',
            'player_identifier' => '123456789',
            'game_name' => 'Mobile Legends',
            'package_name' => '257 Diamonds',
            'price' => 199,
            'currency' => 'THB',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), [
                'status' => Order::STATUS_PROCESSING,
                'admin_note' => 'Payment checked',
            ])
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_PROCESSING,
            'admin_note' => 'Payment checked',
        ]);
    }

    private function createActivePackage(): GamePackage
    {
        $game = Game::create([
            'name' => 'Mobile Legends',
            'slug' => 'mobile-legends',
            'publisher' => 'Moonton',
            'status' => Game::STATUS_ACTIVE,
        ]);

        return GamePackage::create([
            'game_id' => $game->id,
            'name' => '257 Diamonds',
            'sku' => 'MLBB-257-DIAMONDS',
            'price' => 199,
            'currency' => 'THB',
            'required_fields' => ['uid', 'server_id'],
            'status' => GamePackage::STATUS_ACTIVE,
        ]);
    }
}
