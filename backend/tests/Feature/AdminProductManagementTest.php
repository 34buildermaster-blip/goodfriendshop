<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GamePackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_game(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Delta Force',
            'slug' => 'delta-force',
            'publisher' => 'Garena',
            'description' => 'Delta Force Garena top-up product',
            'image_file' => $this->fakePngUpload('delta-force.png'),
            'status' => Game::STATUS_ACTIVE,
            'sort_order' => 30,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('games', [
            'name' => 'Delta Force',
            'slug' => 'delta-force',
            'status' => Game::STATUS_ACTIVE,
        ]);
        $game = Game::where('slug', 'delta-force')->firstOrFail();
        Storage::disk('public')->assertExists($game->image_path);
    }

    public function test_admin_can_create_package_for_game(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $game = Game::create([
            'name' => 'Mobile Legends',
            'slug' => 'mobile-legends',
            'status' => Game::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.products.packages.store', $game), [
            'name' => '257 Diamonds',
            'sku' => 'MLBB-257-DIAMONDS',
            'description' => 'Top-up diamonds',
            'price' => 199,
            'cost' => 175,
            'currency' => 'THB',
            'required_fields' => ['uid', 'server_id'],
            'status' => GamePackage::STATUS_ACTIVE,
            'sort_order' => 10,
        ]);

        $response->assertRedirect(route('admin.packages.index'));
        $this->assertDatabaseHas('game_packages', [
            'game_id' => $game->id,
            'name' => '257 Diamonds',
            'sku' => 'MLBB-257-DIAMONDS',
            'price' => 199,
            'status' => GamePackage::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_manage_packages_from_package_menu(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $game = Game::create([
            'name' => 'PUBG Mobile',
            'slug' => 'pubg-mobile',
            'status' => Game::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.packages.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.packages.create'))
            ->assertOk();

        $response = $this->actingAs($admin)->post(route('admin.packages.store'), [
            'game_id' => $game->id,
            'name' => 'UC 325',
            'sku' => 'PUBG-UC-325',
            'description' => 'PUBG UC top-up package',
            'price' => 199,
            'cost' => 180,
            'currency' => 'THB',
            'required_fields' => ['uid'],
            'status' => GamePackage::STATUS_ACTIVE,
            'sort_order' => 20,
        ]);

        $response->assertRedirect(route('admin.packages.index'));
        $this->assertDatabaseHas('game_packages', [
            'game_id' => $game->id,
            'name' => 'UC 325',
            'sku' => 'PUBG-UC-325',
            'status' => GamePackage::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.packages.index'))
            ->assertOk()
            ->assertSee('PUBG Mobile')
            ->assertSee('UC 325')
            ->assertSee('PUBG-UC-325');
    }

    public function test_customer_cannot_manage_products(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_update_package(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $game = Game::create([
            'name' => 'RoV Mobile',
            'slug' => 'rov-mobile',
            'status' => Game::STATUS_ACTIVE,
        ]);
        $package = $game->packages()->create([
            'name' => '370 คูปอง',
            'sku' => 'ROV-370-COUPON',
            'price' => 299,
            'currency' => 'THB',
            'status' => GamePackage::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.packages.update', $package), [
            'name' => '370 คูปอง',
            'sku' => 'ROV-370-COUPON',
            'description' => 'Updated package',
            'price' => 289,
            'cost' => 260,
            'currency' => 'THB',
            'required_fields' => ['uid'],
            'status' => GamePackage::STATUS_ACTIVE,
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.packages.index'));
        $this->assertDatabaseHas('game_packages', [
            'id' => $package->id,
            'price' => 289,
            'cost' => 260,
            'status' => GamePackage::STATUS_ACTIVE,
            'sort_order' => 5,
        ]);
    }

    private function fakePngUpload(string $name, int $width = 512, int $height = 512): UploadedFile
    {
        $chunk = function (string $type, string $data): string {
            return pack('N', strlen($data)).$type.$data.pack('N', crc32($type.$data));
        };

        $raw = str_repeat("\0".str_repeat("\x66\xed\xbd", $width), $height);
        $png = "\x89PNG\r\n\x1a\n"
            .$chunk('IHDR', pack('NNC5', $width, $height, 8, 2, 0, 0, 0))
            .$chunk('IDAT', gzcompress($raw, 9))
            .$chunk('IEND', '');

        return UploadedFile::fake()->createWithContent($name, $png);
    }
}
