<?php

use App\Http\Middleware\AllowFrontendCors;
use App\Models\ContentPost;
use App\Models\Game;
use App\Models\PremiumApp;
use Illuminate\Support\Facades\Route;

Route::options('/{any}', fn () => response()->noContent())
    ->where('any', '.*')
    ->middleware(AllowFrontendCors::class);

Route::middleware(AllowFrontendCors::class)->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'game-topup-api',
        ]);
    });

    Route::get('/games', function () {
        $games = Game::query()
            ->with(['packages' => fn ($query) => $query
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')])
            ->where('status', Game::STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Game $game, int $index) => [
                'id' => $game->id,
                'slug' => $game->slug,
                'name' => $game->name,
                'publisher' => $game->publisher,
                'description' => $game->description,
                'image' => $game->imageUrl(),
                'featured' => $index === 0,
                'packages' => $game->packages->map(fn ($package) => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'sku' => $package->sku,
                    'description' => $package->description,
                    'price' => (float) $package->price,
                    'currency' => $package->currency,
                    'required_fields' => $package->required_fields ?? [],
                ])->values(),
            ])
            ->values();

        return response()->json(['data' => $games]);
    });

    Route::get('/premium-apps', function () {
        $apps = PremiumApp::query()
            ->where('status', PremiumApp::STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (PremiumApp $app) => [
                'id' => $app->slug,
                'slug' => $app->slug,
                'title' => $app->name,
                'provider' => $app->provider,
                'description' => $app->description,
                'image' => $app->imageUrl(),
                'price' => '฿'.number_format((float) $app->price, 2),
                'duration' => $app->duration_days ? "ใช้งาน {$app->duration_days} วัน" : 'ตามรายละเอียดสินค้า',
                'warranty' => 'มีเคลมตามเงื่อนไขร้าน',
                'platform' => $app->provider ?: 'มือถือ / เว็บ',
                'details' => array_values(array_filter([
                    $app->description,
                    $app->duration_days ? "ระยะเวลาใช้งาน {$app->duration_days} วัน" : null,
                    'รับข้อมูลหลังชำระเงินสำเร็จ',
                ])),
            ])
            ->values();

        return response()->json(['data' => $apps]);
    });

    Route::get('/content-posts', function () {
        $posts = ContentPost::query()
            ->where('status', ContentPost::STATUS_PUBLISHED)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (ContentPost $post, int $index) => [
                'slug' => $post->slug,
                'title' => $post->title,
                'image' => $post->coverImageUrl(),
                'date' => $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y'),
                'category' => $post->type === ContentPost::TYPE_ACTIVITY ? 'Activity' : 'News',
                'type' => $post->type,
                'featured' => $index === 0,
                'excerpt' => $post->excerpt ?: str($post->content)->stripTags()->limit(160)->toString(),
            ])
            ->values();

        return response()->json(['data' => $posts]);
    });

    Route::get('/content-posts/{contentPost:slug}', function (ContentPost $contentPost) {
        abort_unless($contentPost->status === ContentPost::STATUS_PUBLISHED, 404);

        return response()->json([
            'data' => [
                'slug' => $contentPost->slug,
                'title' => $contentPost->title,
                'image' => $contentPost->coverImageUrl(),
                'date' => $contentPost->published_at?->format('F j, Y') ?? $contentPost->created_at->format('F j, Y'),
                'category' => $contentPost->type === ContentPost::TYPE_ACTIVITY ? 'Activity' : 'News',
                'type' => $contentPost->type,
                'excerpt' => $contentPost->excerpt ?: str($contentPost->content)->stripTags()->limit(160)->toString(),
                'content' => $contentPost->content,
            ],
        ]);
    });

    Route::get('/payment-methods', function () {
        return response()->json([
            'data' => [
                ['id' => 'promptpay', 'name' => 'PromptPay', 'status' => 'active'],
                ['id' => 'truemoney', 'name' => 'TrueMoney', 'status' => 'draft'],
                ['id' => 'bank-transfer', 'name' => 'Bank Transfer', 'status' => 'draft'],
            ],
        ]);
    });
});
