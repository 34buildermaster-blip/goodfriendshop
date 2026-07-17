<?php

namespace Tests\Feature;

use App\Models\ContentPost;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\HeroSlide;
use App\Models\Announcement;
use App\Models\PremiumApp;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_games_api_returns_active_games_with_packages(): void
    {
        $activeGame = Game::create([
            'name' => 'Mobile Legends',
            'slug' => 'mobile-legends',
            'image_path' => '/figma/game-mobile-legends.webp',
            'status' => Game::STATUS_ACTIVE,
            'sort_order' => 10,
        ]);
        $activeGame->packages()->create([
            'name' => '257 Diamonds',
            'sku' => 'MLBB-257-DIAMONDS',
            'price' => 199,
            'currency' => 'THB',
            'status' => GamePackage::STATUS_ACTIVE,
        ]);
        Game::create([
            'name' => 'Draft Game',
            'slug' => 'draft-game',
            'status' => Game::STATUS_DRAFT,
        ]);

        $this->getJson('/api/games')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mobile Legends')
            ->assertJsonPath('data.0.packages.0.name', '257 Diamonds');
    }

    public function test_public_premium_apps_api_returns_active_apps(): void
    {
        PremiumApp::create([
            'name' => 'Netflix Premium 30 วัน',
            'slug' => 'netflix-premium-30-day',
            'provider' => 'Netflix',
            'image_path' => '/figma/premium-netflix.webp',
            'price' => 239,
            'currency' => 'THB',
            'duration_days' => 30,
            'delivery_type' => PremiumApp::DELIVERY_ACCOUNT_TOPUP,
            'customer_required_fields' => ['account_email', 'line_id'],
            'warranty_days' => 7,
            'stock_status' => PremiumApp::STOCK_IN_STOCK,
            'terms' => 'Warranty by shop policy.',
            'status' => PremiumApp::STATUS_ACTIVE,
        ]);
        PremiumApp::create([
            'name' => 'Draft App',
            'slug' => 'draft-app',
            'price' => 1,
            'currency' => 'THB',
            'status' => PremiumApp::STATUS_DRAFT,
        ]);

        $this->getJson('/api/premium-apps')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Netflix Premium 30 วัน')
            ->assertJsonPath('data.0.price', '฿239.00')
            ->assertJsonPath('data.0.delivery_type', PremiumApp::DELIVERY_ACCOUNT_TOPUP)
            ->assertJsonPath('data.0.warranty_days', 7)
            ->assertJsonPath('data.0.customer_required_fields.0', 'account_email');
    }

    public function test_public_content_api_returns_published_posts_and_detail(): void
    {
        ContentPost::create([
            'title' => 'ข่าวเกมล่าสุด',
            'slug' => 'latest-game-news',
            'type' => ContentPost::TYPE_NEWS,
            'excerpt' => 'สรุปข่าวเกมจากร้าน',
            'content' => '<p>รายละเอียดข่าว</p>',
            'cover_image_path' => '/figma/news-main.webp',
            'published_at' => now(),
            'status' => ContentPost::STATUS_PUBLISHED,
        ]);
        ContentPost::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'status' => ContentPost::STATUS_DRAFT,
        ]);

        $this->getJson('/api/content-posts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'latest-game-news');

        $this->getJson('/api/content-posts/latest-game-news')
            ->assertOk()
            ->assertJsonPath('data.content', '<p>รายละเอียดข่าว</p>');
    }

    public function test_public_site_content_api_returns_settings_slides_and_announcements(): void
    {
        SiteSetting::seedDefaults();
        SiteSetting::query()->where('key', 'site_name')->update(['value' => 'Good Friend Shop']);
        HeroSlide::create([
            'eyebrow' => 'SAFE TOPUP',
            'title' => 'เติมเกมปลอดภัย',
            'highlight' => 'มั่นใจได้',
            'quote' => 'เติมไว',
            'image_path' => '/figma/hero.webp',
            'cta_label' => 'เริ่มเติมเกม',
            'cta_url' => '/games',
            'is_active' => true,
        ]);
        Announcement::create([
            'message' => 'ประกาศทดสอบ',
            'is_active' => true,
        ]);

        $this->getJson('/api/site-content')
            ->assertOk()
            ->assertJsonPath('data.settings.site_name', 'Good Friend Shop')
            ->assertJsonPath('data.hero_slides.0.title', 'เติมเกมปลอดภัย')
            ->assertJsonPath('data.announcements.0.message', 'ประกาศทดสอบ');
    }
}
