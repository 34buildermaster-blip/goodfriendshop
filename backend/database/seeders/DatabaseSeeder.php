<?php

namespace Database\Seeders;

use App\Models\ContentPost;
use App\Models\Game;
use App\Models\PremiumApp;
use App\Models\User;
use Carbon\CarbonImmutable;
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
        $this->seedUsers();
        $this->seedGames();
        $this->seedPremiumApps();
        $this->seedContentPosts();
    }

    private function seedUsers(): void
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
    }

    private function seedGames(): void
    {
        $games = [
            [
                'slug' => 'mobile-legends',
                'name' => 'Mobile Legends',
                'publisher' => 'Moonton',
                'description' => 'แพ็กเกจเติม Diamonds สำหรับ Mobile Legends จัดส่งไวและปลอดภัย',
                'image_path' => '/figma/game-mobile-legends.webp',
                'sort_order' => 10,
                'packages' => [
                    ['sku' => 'MLBB-257-DIAMONDS', 'name' => '257 Diamonds', 'price' => 199, 'cost' => 175, 'required_fields' => ['uid', 'server_id'], 'sort_order' => 10],
                    ['sku' => 'MLBB-514-DIAMONDS', 'name' => '514 Diamonds', 'price' => 389, 'cost' => 350, 'required_fields' => ['uid', 'server_id'], 'sort_order' => 20],
                ],
            ],
            [
                'slug' => 'pubg-mobile-thai',
                'name' => 'PUBG Mobile (Thai)',
                'publisher' => 'Level Infinite',
                'description' => 'เติม UC PUBG Mobile โซนไทยสำหรับซื้อไอเทมและ Royale Pass',
                'image_path' => '/figma/game-pubg.webp',
                'sort_order' => 20,
                'packages' => [
                    ['sku' => 'PUBG-325-UC', 'name' => '325 UC', 'price' => 199, 'cost' => 175, 'required_fields' => ['player_id'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'rov-mobile',
                'name' => 'RoV Mobile',
                'publisher' => 'Garena',
                'description' => 'แพ็กเกจคูปอง RoV สำหรับลูกค้าหน้าร้าน',
                'image_path' => '/figma/game-rov.webp',
                'sort_order' => 30,
                'packages' => [
                    ['sku' => 'ROV-370-COUPON', 'name' => '370 คูปอง', 'price' => 299, 'cost' => 270, 'required_fields' => ['uid'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'delta-force-garena',
                'name' => 'Delta Force (Garena)',
                'publisher' => 'Garena',
                'description' => 'เติมเครดิตและแพ็กพิเศษสำหรับ Delta Force เวอร์ชัน Garena',
                'image_path' => '/figma/game-delta-garena.webp',
                'sort_order' => 40,
                'packages' => [
                    ['sku' => 'DFG-STARTER', 'name' => 'Starter Pack', 'price' => 159, 'cost' => 135, 'required_fields' => ['uid'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'free-fire',
                'name' => 'Free Fire',
                'publisher' => 'Garena',
                'description' => 'เติมเพชร Free Fire ยอดนิยมสำหรับกิจกรรมและไอเทมในเกม',
                'image_path' => '/figma/game-free-fire.webp',
                'sort_order' => 50,
                'packages' => [
                    ['sku' => 'FF-310-DIAMONDS', 'name' => '310 Diamonds', 'price' => 199, 'cost' => 175, 'required_fields' => ['player_id'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'delta-force-steam',
                'name' => 'Delta Force (Steam)',
                'publisher' => 'Steam',
                'description' => 'แพ็กเติม Delta Force สำหรับผู้เล่นเวอร์ชัน Steam',
                'image_path' => '/figma/game-delta-steam.webp',
                'sort_order' => 60,
                'packages' => [
                    ['sku' => 'DFS-STARTER', 'name' => 'Steam Starter Pack', 'price' => 159, 'cost' => 135, 'required_fields' => ['steam_id'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'league-of-legends',
                'name' => 'League of Legends',
                'publisher' => 'Riot Games',
                'description' => 'เติม RP สำหรับ League of Legends เพื่อซื้อสกินและไอเทมในเกม',
                'image_path' => '/figma/game-lol.webp',
                'sort_order' => 70,
                'packages' => [
                    ['sku' => 'LOL-575-RP', 'name' => '575 RP', 'price' => 149, 'cost' => 130, 'required_fields' => ['riot_id'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'identity-v',
                'name' => 'Identity V',
                'publisher' => 'NetEase',
                'description' => 'เติม Echoes สำหรับ Identity V พร้อมบริการช่วยเช็กรายการ',
                'image_path' => '/figma/game-identity-v.webp',
                'sort_order' => 80,
                'packages' => [
                    ['sku' => 'IDV-185-ECHOES', 'name' => '185 Echoes', 'price' => 149, 'cost' => 130, 'required_fields' => ['player_id'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'fc-mobile',
                'name' => 'FC Mobile (FIFA Mobile)',
                'publisher' => 'EA Sports',
                'description' => 'แพ็กเติม FC Points สำหรับ FC Mobile',
                'image_path' => '/figma/game-fc-mobile.webp',
                'sort_order' => 90,
                'packages' => [
                    ['sku' => 'FCM-500-POINTS', 'name' => '500 FC Points', 'price' => 199, 'cost' => 175, 'required_fields' => ['player_id'], 'sort_order' => 10],
                ],
            ],
            [
                'slug' => 'mu-archangel',
                'name' => 'MU Archangel',
                'publisher' => 'Webzen',
                'description' => 'แพ็กเติมสำหรับ MU Archangel พร้อมรายการแนะนำจากร้าน',
                'image_path' => '/figma/game-mu.webp',
                'sort_order' => 100,
                'packages' => [
                    ['sku' => 'MUA-STARTER', 'name' => 'Starter Pack', 'price' => 199, 'cost' => 175, 'required_fields' => ['uid', 'server'], 'sort_order' => 10],
                ],
            ],
        ];

        foreach ($games as $gameData) {
            $packages = $gameData['packages'];
            unset($gameData['packages']);

            $game = Game::updateOrCreate(
                ['slug' => $gameData['slug']],
                [
                    ...$gameData,
                    'status' => Game::STATUS_ACTIVE,
                ],
            );

            foreach ($packages as $packageData) {
                $game->packages()->updateOrCreate(
                    ['sku' => $packageData['sku']],
                    [
                        ...$packageData,
                        'description' => "แพ็ก {$packageData['name']} สำหรับ {$game->name}",
                        'currency' => 'THB',
                        'status' => 'active',
                    ],
                );
            }
        }
    }

    private function seedPremiumApps(): void
    {
        $apps = [
            ['slug' => 'nf-1-day', 'name' => 'แอคนอกมีเคลม NF 1 วัน (มือถือ)', 'provider' => 'Netflix', 'price' => 20, 'duration_days' => 1, 'sort_order' => 10],
            ['slug' => 'nf-3-day', 'name' => 'แอคนอกมีเคลม NF 3 วัน (มือถือ)', 'provider' => 'Netflix', 'price' => 59, 'duration_days' => 3, 'sort_order' => 20],
            ['slug' => 'nf-7-day', 'name' => 'แอคนอกมีเคลม NF 7 วัน (มือถือ)', 'provider' => 'Netflix', 'price' => 99, 'duration_days' => 7, 'sort_order' => 30],
            ['slug' => 'nf-30-day', 'name' => 'แอคนอกมีเคลม NF 30 วัน (มือถือ)', 'provider' => 'Netflix', 'price' => 239, 'duration_days' => 30, 'sort_order' => 40],
            ['slug' => 'spotify-30-day', 'name' => 'Spotify Premium 30 วัน', 'provider' => 'Spotify', 'price' => 89, 'duration_days' => 30, 'sort_order' => 50],
            ['slug' => 'youtube-30-day', 'name' => 'YouTube Premium 30 วัน', 'provider' => 'YouTube', 'price' => 129, 'duration_days' => 30, 'sort_order' => 60],
        ];

        foreach ($apps as $appData) {
            PremiumApp::updateOrCreate(
                ['slug' => $appData['slug']],
                [
                    ...$appData,
                    'description' => "{$appData['name']} พร้อมรับข้อมูลหลังชำระเงินและมีทีมช่วยตรวจสอบตามเงื่อนไขร้าน",
                    'image_path' => '/figma/premium-netflix.webp',
                    'cost' => null,
                    'currency' => 'THB',
                    'status' => PremiumApp::STATUS_ACTIVE,
                ],
            );
        }
    }

    private function seedContentPosts(): void
    {
        $posts = [
            [
                'slug' => 'bytedance-moonton',
                'title' => 'ByteDance บุกตลาด MOBA ซื้อกิจการ Moonton เจ้าของเกม Mobile Legends',
                'type' => ContentPost::TYPE_NEWS,
                'excerpt' => 'ByteDance ได้เข้าซื้อ Moonton สตูดิโอเกมมือถือรายใหญ่ที่สร้างความทะเยอทะยานในอุตสาหกรรมเกม',
                'cover_image_path' => '/figma/news-main.webp',
                'published_at' => CarbonImmutable::parse('2021-03-23 09:00:00'),
                'sort_order' => 10,
            ],
            [
                'slug' => 'rov-talon-awc',
                'title' => 'เรื่องเล่าเช้านี้รายการข่าวชื่อดังหยิบนำข่าว RoV ทีม Dtac Talon คว้าแชมป์โลก AWC 2021',
                'type' => ContentPost::TYPE_ACTIVITY,
                'excerpt' => 'ข่าวทีมอีสปอร์ตไทยสร้างชื่อบนเวทีระดับโลก และกลายเป็นกระแสในวงการเกม',
                'cover_image_path' => '/figma/news-champions.webp',
                'published_at' => CarbonImmutable::parse('2021-07-21 09:00:00'),
                'sort_order' => 20,
            ],
            [
                'slug' => 'delta-force-acl',
                'title' => '[เปิดรับสมัคร] การแข่งขัน Garena Delta Force Road to ACL 2025',
                'type' => ContentPost::TYPE_ACTIVITY,
                'excerpt' => 'เปิดรับสมัครนักแข่ง Delta Force เพื่อค้นหาทีมเข้าสู่เส้นทางการแข่งขันใหญ่',
                'cover_image_path' => '/figma/news-delta.webp',
                'published_at' => CarbonImmutable::parse('2025-05-12 09:00:00'),
                'sort_order' => 30,
            ],
        ];

        foreach ($posts as $postData) {
            ContentPost::updateOrCreate(
                ['slug' => $postData['slug']],
                [
                    ...$postData,
                    'content' => $this->articleContent($postData['title'], $postData['excerpt']),
                    'status' => ContentPost::STATUS_PUBLISHED,
                ],
            );
        }
    }

    private function articleContent(string $title, string $excerpt): string
    {
        return <<<HTML
<p>{$excerpt}</p>
<p>{$title} เป็นหนึ่งในหัวข้อที่ทีม Good Friend Shop เลือกมาอัปเดตให้ลูกค้าติดตาม เพราะเกี่ยวข้องกับกระแสเกม กิจกรรม หรือบริการที่ผู้เล่นให้ความสนใจในช่วงนี้</p>
<p>ลูกค้าสามารถใช้หน้านี้เพื่อติดตามข่าวสาร โปรโมชั่น และกิจกรรมจากร้าน ก่อนเลือกเติมเกม ซื้อแพ็กแอพพรีเมียม หรือรอโปรโมชันรอบใหม่จากทีมงาน</p>
HTML;
    }
}
