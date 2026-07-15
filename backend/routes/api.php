<?php

use App\Http\Middleware\AllowFrontendCors;
use App\Models\Announcement;
use App\Models\ContentPost;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\HeroSlide;
use App\Models\Order;
use App\Models\PremiumApp;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

Route::options('/{any}', fn () => response()->noContent())
    ->where('any', '.*')
    ->middleware(AllowFrontendCors::class);

Route::middleware(AllowFrontendCors::class)->group(function () {
    $resolveApiUser = function (Request $request): ?User {
        $token = $request->bearerToken();

        if (blank($token)) {
            return null;
        }

        return User::query()
            ->where('api_token', hash('sha256', $token))
            ->where('status', User::STATUS_ACTIVE)
            ->first();
    };

    $issueToken = function (User $user): string {
        $token = Str::random(80);
        $user->forceFill(['api_token' => hash('sha256', $token)])->save();

        return $token;
    };

    $userPayload = fn (User $user) => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'line_id' => $user->line_id,
        'avatar_url' => $user->avatarUrl(),
    ];

    $orderStatusSteps = function (Order $order): array {
        if ($order->status === Order::STATUS_CANCELLED) {
            return [
                ['key' => Order::STATUS_PENDING, 'label' => 'รับออเดอร์', 'state' => 'done'],
                ['key' => Order::STATUS_CANCELLED, 'label' => 'ยกเลิก', 'state' => 'current'],
            ];
        }

        $steps = [
            Order::STATUS_PENDING => 'รับออเดอร์',
            Order::STATUS_PAID => 'ชำระเงิน',
            Order::STATUS_PROCESSING => 'กำลังดำเนินการ',
            Order::STATUS_COMPLETED => 'สำเร็จ',
        ];
        $currentIndex = array_search($order->status, array_keys($steps), true);

        return collect($steps)
            ->map(function (string $label, string $key) use ($currentIndex, $steps) {
                $index = array_search($key, array_keys($steps), true);

                return [
                    'key' => $key,
                    'label' => $label,
                    'state' => $index < $currentIndex ? 'done' : ($index === $currentIndex ? 'current' : 'upcoming'),
                ];
            })
            ->values()
            ->all();
    };

    $orderNextAction = fn (Order $order) => match ($order->status) {
        Order::STATUS_PENDING => 'รอทีมงานตรวจสอบข้อมูลและยอดชำระ',
        Order::STATUS_PAID => 'รับชำระแล้ว รอทีมงานเริ่มดำเนินการ',
        Order::STATUS_PROCESSING => 'ทีมงานกำลังเติมสินค้าให้บัญชีเกมของคุณ',
        Order::STATUS_COMPLETED => 'รายการสำเร็จแล้ว ตรวจสอบสินค้าในบัญชีเกมได้เลย',
        Order::STATUS_CANCELLED => 'รายการถูกยกเลิก หากมีข้อสงสัยติดต่อทีมงาน',
        default => 'รออัปเดตสถานะจากทีมงาน',
    };

    $orderPayload = fn (Order $order) => [
        'id' => $order->id,
        'order_number' => $order->order_number,
        'customer_name' => $order->customer_name,
        'customer_email' => $order->customer_email,
        'customer_phone' => $order->customer_phone,
        'player_identifier' => $order->player_identifier,
        'server_identifier' => $order->server_identifier,
        'game_name' => $order->game_name,
        'package_name' => $order->package_name,
        'price' => (float) $order->price,
        'currency' => $order->currency,
        'status' => $order->status,
        'status_label' => Order::statusLabels()[$order->status] ?? $order->status,
        'customer_note' => $order->customer_note,
        'support_note' => $order->admin_note,
        'next_action' => $orderNextAction($order),
        'status_steps' => $orderStatusSteps($order),
        'created_at' => $order->created_at?->toIso8601String(),
        'updated_at' => $order->updated_at?->toIso8601String(),
    ];

    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'game-topup-api',
        ]);
    });

    Route::post('/auth/register', function (Request $request) use ($issueToken, $userPayload) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'line_id' => ['nullable', 'string', 'max:80'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_CUSTOMER,
            'status' => User::STATUS_ACTIVE,
        ]);

        return response()->json([
            'data' => [
                'token' => $issueToken($user),
                'user' => $userPayload($user),
            ],
        ], 201);
    });

    Route::post('/auth/login', function (Request $request) use ($issueToken, $userPayload) {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password) || ! $user->isActive()) {
            return response()->json(['message' => 'ข้อมูลเข้าสู่ระบบไม่ถูกต้อง'], 422);
        }

        return response()->json([
            'data' => [
                'token' => $issueToken($user),
                'user' => $userPayload($user),
            ],
        ]);
    });

    Route::get('/auth/me', function (Request $request) use ($resolveApiUser, $userPayload) {
        $user = $resolveApiUser($request);

        abort_unless($user, 401);

        return response()->json(['data' => $userPayload($user)]);
    });

    Route::patch('/auth/me', function (Request $request) use ($resolveApiUser, $userPayload) {
        $user = $resolveApiUser($request);

        abort_unless($user, 401);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'line_id' => ['nullable', 'string', 'max:80'],
        ]);

        $user->update($data);

        return response()->json(['data' => $userPayload($user->refresh())]);
    });

    Route::post('/auth/me/avatar', function (Request $request) use ($resolveApiUser, $userPayload) {
        $user = $resolveApiUser($request);

        abort_unless($user, 401);

        $data = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($user->avatar_path && ! Str::startsWith($user->avatar_path, ['http://', 'https://', '/'])) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $file = $data['avatar'];
        $extension = $file->extension() ?: 'webp';
        $path = $file->storeAs('avatars', "user-{$user->id}-".Str::uuid().".{$extension}", 'public');

        $user->forceFill(['avatar_path' => $path])->save();

        return response()->json(['data' => $userPayload($user->refresh())]);
    });

    Route::post('/auth/logout', function (Request $request) use ($resolveApiUser) {
        $user = $resolveApiUser($request);

        if ($user) {
            $user->forceFill(['api_token' => null])->save();
        }

        return response()->json(['data' => ['ok' => true]]);
    });

    Route::get('/site-content', function () {
        SiteSetting::seedDefaults();
        $now = now();

        return response()->json([
            'data' => [
                'settings' => SiteSetting::values(),
                'hero_slides' => HeroSlide::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->map(fn (HeroSlide $slide) => [
                        'id' => $slide->id,
                        'eyebrow' => $slide->eyebrow,
                        'title' => $slide->title,
                        'highlight' => $slide->highlight,
                        'quote' => $slide->quote,
                        'image' => $slide->imageUrl(),
                        'href' => $slide->cta_url ?: '/',
                        'cta' => $slide->cta_label ?: 'ดูเพิ่มเติม',
                    ])
                    ->values(),
                'announcements' => Announcement::query()
                    ->where('is_active', true)
                    ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                    ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get()
                    ->map(fn (Announcement $announcement) => [
                        'id' => $announcement->id,
                        'message' => $announcement->message,
                    ])
                    ->values(),
            ],
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

    Route::get('/games/{game:slug}', function (Game $game) {
        abort_unless($game->status === Game::STATUS_ACTIVE, 404);

        $game->load(['packages' => fn ($query) => $query
            ->where('status', GamePackage::STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->orderBy('name')]);

        return response()->json([
            'data' => [
                'id' => $game->id,
                'slug' => $game->slug,
                'name' => $game->name,
                'publisher' => $game->publisher,
                'description' => $game->description,
                'image' => $game->imageUrl(),
                'packages' => $game->packages->map(fn (GamePackage $package) => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'sku' => $package->sku,
                    'description' => $package->description,
                    'price' => (float) $package->price,
                    'currency' => $package->currency,
                    'required_fields' => $package->required_fields ?? [],
                ])->values(),
            ],
        ]);
    });

    Route::post('/orders', function (Request $request) use ($resolveApiUser, $orderPayload) {
        $data = $request->validate([
            'game_package_id' => ['nullable', 'required_without:premium_app_id', 'integer', 'exists:game_packages,id'],
            'premium_app_id' => ['nullable', 'required_without:game_package_id', 'string', 'max:180'],
            'customer_name' => ['nullable', 'string', 'max:160'],
            'customer_email' => ['nullable', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'player_identifier' => ['required', 'string', 'max:160'],
            'server_identifier' => ['nullable', 'string', 'max:160'],
            'extra_fields' => ['nullable', 'array'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $package = null;
        $premiumApp = null;

        if (filled($data['game_package_id'] ?? null)) {
            $package = GamePackage::query()
                ->with('game')
                ->whereKey($data['game_package_id'])
                ->where('status', GamePackage::STATUS_ACTIVE)
                ->firstOrFail();

            abort_unless($package->game?->status === Game::STATUS_ACTIVE, 404);
        }

        if (filled($data['premium_app_id'] ?? null)) {
            $premiumApp = PremiumApp::query()
                ->where(fn ($query) => $query
                    ->whereKey($data['premium_app_id'])
                    ->orWhere('slug', $data['premium_app_id']))
                ->where('status', PremiumApp::STATUS_ACTIVE)
                ->firstOrFail();
        }

        $user = $resolveApiUser($request);

        if (! $user && blank($data['customer_name'] ?? null)) {
            return response()->json([
                'message' => 'กรุณาระบุชื่อลูกค้าหรือเข้าสู่ระบบก่อนสั่งซื้อ',
                'errors' => ['customer_name' => ['กรุณาระบุชื่อลูกค้า']],
            ], 422);
        }

        $order = Order::create([
            'user_id' => $user?->id,
            'game_id' => $package?->game_id,
            'game_package_id' => $package?->id,
            'premium_app_id' => $premiumApp?->id,
            'customer_name' => $data['customer_name'] ?? $user?->name,
            'customer_email' => $data['customer_email'] ?? $user?->email,
            'customer_phone' => $data['customer_phone'] ?? $user?->phone,
            'player_identifier' => $data['player_identifier'],
            'server_identifier' => $data['server_identifier'] ?? null,
            'extra_fields' => [
                ...($data['extra_fields'] ?? []),
                ...($premiumApp ? ['premium_app_slug' => $premiumApp->slug] : []),
            ],
            'game_name' => $package?->game->name ?? 'Premium App',
            'package_name' => $package?->name ?? $premiumApp->name,
            'price' => $package?->price ?? $premiumApp->price,
            'currency' => $package?->currency ?? $premiumApp->currency,
            'status' => Order::STATUS_PENDING,
            'customer_note' => $data['customer_note'] ?? null,
        ]);

        return response()->json(['data' => $orderPayload($order)], 201);
    });

    Route::get('/orders/{order:order_number}', function (Order $order) use ($orderPayload) {
        return response()->json(['data' => $orderPayload($order)]);
    });

    Route::get('/my/orders', function (Request $request) use ($resolveApiUser, $orderPayload) {
        $user = $resolveApiUser($request);

        abort_unless($user, 401);

        return response()->json([
            'data' => $user->orders()
                ->latest()
                ->get()
                ->map(fn (Order $order) => $orderPayload($order))
                ->values(),
        ]);
    });

    Route::get('/my/orders/{order:order_number}', function (Request $request, Order $order) use ($resolveApiUser, $orderPayload) {
        $user = $resolveApiUser($request);

        abort_unless($user && (int) $order->user_id === (int) $user->id, 404);

        return response()->json(['data' => $orderPayload($order)]);
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
