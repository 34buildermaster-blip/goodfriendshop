<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\HeroSlide;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSiteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_general_site_settings(): void
    {
        SiteSetting::seedDefaults();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.site-settings.edit'))
            ->assertOk()
            ->assertSee('Website settings')
            ->assertSee('logo_file');

        $response = $this->actingAs($admin)->put(route('admin.site-settings.update'), [
            'settings' => [
                'site_name' => 'Good Friend Test',
            ],
        ]);

        $response->assertRedirect(route('admin.site-settings.edit'));
        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_name',
            'value' => 'Good Friend Test',
        ]);
        $this->assertDatabaseMissing('site_settings', ['key' => 'logo_text']);
    }

    public function test_admin_can_manage_hero_slides_and_announcements(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.hero-slides.index'))
            ->assertOk()
            ->assertSee('Homepage slides');

        $this->actingAs($admin)
            ->post(route('admin.hero-slides.store'), [
                'eyebrow' => 'SAFE',
                'title' => 'Hero title',
                'highlight' => 'Hero highlight',
                'quote' => 'Hero quote',
                'image_path' => '/figma/hero.webp',
                'cta_label' => 'Start',
                'cta_url' => '/games',
                'is_active' => 1,
                'sort_order' => 10,
            ])
            ->assertRedirect(route('admin.hero-slides.index'));

        $this->assertDatabaseHas('hero_slides', [
            'title' => 'Hero title',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.announcements.index'))
            ->assertOk()
            ->assertSee('Announcements');

        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), [
                'message' => 'ประกาศหน้าแรก',
                'is_active' => 1,
                'sort_order' => 10,
            ])
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'message' => 'ประกาศหน้าแรก',
            'is_active' => true,
        ]);
    }

    public function test_customer_cannot_access_site_management(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('admin.site-settings.edit'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.hero-slides.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.announcements.index'))
            ->assertForbidden();
    }
}
