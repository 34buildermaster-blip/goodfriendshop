<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@goodfriendshop.test')],
            [
                'name' => env('ADMIN_NAME', 'Good Friend Admin'),
                'phone' => env('ADMIN_PHONE'),
                'line_id' => env('ADMIN_LINE_ID'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ],
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_ACTIVE,
            ],
        );

        $mobileLegends = Game::updateOrCreate(
            ['slug' => 'mobile-legends'],
            [
                'name' => 'Mobile Legends',
                'publisher' => 'Moonton',
                'description' => 'แพ็กเกจเติม Diamonds สำหรับ Mobile Legends',
                'image_path' => '/figma/game-mobile-legends.webp',
                'status' => Game::STATUS_ACTIVE,
                'sort_order' => 10,
            ],
        );

        $rov = Game::updateOrCreate(
            ['slug' => 'rov-mobile'],
            [
                'name' => 'RoV Mobile',
                'publisher' => 'Garena',
                'description' => 'แพ็กเกจคูปอง RoV สำหรับลูกค้าหน้าร้าน',
                'image_path' => '/figma/game-rov.webp',
                'status' => Game::STATUS_ACTIVE,
                'sort_order' => 20,
            ],
        );

        $mobileLegends->packages()->updateOrCreate(
            ['sku' => 'MLBB-257-DIAMONDS'],
            [
                'name' => '257 Diamonds',
                'description' => 'เติม Diamonds เข้าบัญชีลูกค้า',
                'price' => 199,
                'cost' => 175,
                'currency' => 'THB',
                'required_fields' => ['uid', 'server_id'],
                'status' => 'active',
                'sort_order' => 10,
            ],
        );

        $mobileLegends->packages()->updateOrCreate(
            ['sku' => 'MLBB-514-DIAMONDS'],
            [
                'name' => '514 Diamonds',
                'description' => 'แพ็กเกจยอดนิยมสำหรับลูกค้าประจำ',
                'price' => 389,
                'cost' => 350,
                'currency' => 'THB',
                'required_fields' => ['uid', 'server_id'],
                'status' => 'active',
                'sort_order' => 20,
            ],
        );

        $rov->packages()->updateOrCreate(
            ['sku' => 'ROV-370-COUPON'],
            [
                'name' => '370 คูปอง',
                'description' => 'เติมคูปอง RoV เข้าบัญชีลูกค้า',
                'price' => 299,
                'cost' => 270,
                'currency' => 'THB',
                'required_fields' => ['uid'],
                'status' => 'active',
                'sort_order' => 10,
            ],
        );
    }
}
