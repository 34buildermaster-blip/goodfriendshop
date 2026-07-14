<?php

namespace Tests\Feature;

use App\Models\ContentPost;
use App\Models\PremiumApp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_premium_app(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.premium-apps.index'))
            ->assertOk();

        $response = $this->actingAs($admin)->post(route('admin.premium-apps.store'), [
            'name' => 'Netflix Premium',
            'slug' => 'netflix-premium',
            'provider' => 'Netflix',
            'description' => 'Premium streaming account package',
            'price' => 159,
            'cost' => 120,
            'currency' => 'THB',
            'duration_days' => 30,
            'status' => PremiumApp::STATUS_ACTIVE,
            'sort_order' => 10,
        ]);

        $response->assertRedirect(route('admin.premium-apps.index'));
        $this->assertDatabaseHas('premium_apps', [
            'name' => 'Netflix Premium',
            'slug' => 'netflix-premium',
            'provider' => 'Netflix',
            'status' => PremiumApp::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_create_content_post(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content-posts.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.content-posts.create'))
            ->assertOk()
            ->assertSee('content-editor')
            ->assertSee('tinymce');

        $response = $this->actingAs($admin)->post(route('admin.content-posts.store'), [
            'title' => 'Top-up promotion',
            'slug' => 'top-up-promotion',
            'type' => ContentPost::TYPE_ACTIVITY,
            'excerpt' => 'Special campaign for top-up customers',
            'content' => 'Campaign details and reward conditions.',
            'published_at' => '2026-07-14T10:00',
            'status' => ContentPost::STATUS_PUBLISHED,
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.content-posts.index'));
        $this->assertDatabaseHas('content_posts', [
            'title' => 'Top-up promotion',
            'slug' => 'top-up-promotion',
            'type' => ContentPost::TYPE_ACTIVITY,
            'status' => ContentPost::STATUS_PUBLISHED,
        ]);
    }

    public function test_customer_cannot_manage_premium_apps_or_content_posts(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('admin.premium-apps.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.content-posts.index'))
            ->assertForbidden();
    }
}
