<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการแอพพรีเมียม | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root { --bg: #050b0a; --panel: rgba(8, 17, 15, 0.94); --line: rgba(255, 255, 255, 0.1); --green: #66edbd; --muted: rgba(255, 255, 255, 0.62); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.16), transparent 38rem), var(--bg); color: #fff; font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; }
        main { width: min(1180px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        a, button { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .topbar { margin-bottom: 20px; }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 0; font-size: clamp(28px, 5vw, 42px); line-height: 1.16; }
        .notice { margin: 0 0 18px; border: 1px solid rgba(102, 237, 189, 0.2); border-radius: 18px; padding: 14px 16px; background: rgba(102, 237, 189, 0.09); color: #bbf7d0; font-weight: 800; }
        .empty, .app-card { border: 1px solid var(--line); border-radius: 28px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .empty { padding: 28px; color: var(--muted); text-align: center; }
        .app-list { display: grid; gap: 16px; }
        .app-card { display: grid; grid-template-columns: 74px minmax(0, 1fr) auto; gap: 16px; align-items: start; padding: 18px; }
        .app-thumb { width: 74px; aspect-ratio: 1; border: 1px solid var(--line); border-radius: 18px; object-fit: cover; background: rgba(255, 255, 255, 0.055); }
        .app-title { margin: 0; font-size: 23px; }
        .meta { margin: 7px 0 0; color: var(--muted); line-height: 1.7; }
        .price { color: #fff; font-weight: 900; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
        .button { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; border: 0; border-radius: 14px; padding: 0 16px; background: var(--green); color: #05140f; font-weight: 900; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255, 255, 255, 0.055); color: #fff; }
        .button.danger { border: 1px solid rgba(248, 113, 113, 0.28); background: rgba(248, 113, 113, 0.1); color: #fecaca; }
        .badge { display: inline-flex; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 900; }
        .badge.active { border-color: rgba(110, 231, 183, 0.24); background: rgba(110, 231, 183, 0.12); color: #bbf7d0; }
        .badge.draft { border-color: rgba(252, 211, 77, 0.24); background: rgba(252, 211, 77, 0.12); color: #fde68a; }
        .badge.inactive { background: rgba(255, 255, 255, 0.06); color: rgba(255, 255, 255, 0.72); }
        .badge.stock { border-color: rgba(96, 165, 250, 0.24); background: rgba(96, 165, 250, 0.12); color: #bfdbfe; }
        .inline-form { display: inline; }
        .pagination { margin-top: 18px; color: var(--muted); }
        @media (max-width: 760px) { .app-card { grid-template-columns: 1fr; } .actions { justify-content: stretch; } .button { flex: 1; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <p class="kicker">Premium apps</p>
            <h1>จัดการแอพพรีเมียม</h1>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        @if ($apps->isEmpty())
            <section class="empty">ยังไม่มีแอพพรีเมียมในระบบ เลือกเมนูเพิ่มแอพจากแถบด้านซ้ายเพื่อเริ่มสร้างรายการแรก</section>
        @else
            <section class="app-list">
                @foreach ($apps as $app)
                    <article class="app-card">
                        @if ($app->imageUrl())
                            <img class="app-thumb" src="{{ $app->imageUrl() }}" alt="{{ $app->name }}">
                        @else
                            <div class="app-thumb" aria-hidden="true"></div>
                        @endif
                        <div>
                            <h2 class="app-title">{{ $app->name }}</h2>
                            <p class="meta">
                                Slug: {{ $app->slug }}
                                @if ($app->provider) · ผู้ให้บริการ: {{ $app->provider }} @endif
                                · <span class="price">{{ number_format((float) $app->price, 2) }} {{ $app->currency }}</span>
                                @if ($app->duration_days) · {{ $app->duration_days }} วัน @endif
                                · {{ \App\Models\PremiumApp::deliveryTypeLabels()[$app->delivery_type] ?? $app->delivery_type }}
                            </p>
                            @if ($app->description)
                                <p class="meta">{{ $app->description }}</p>
                            @endif
                        </div>
                        <div class="actions">
                            <span class="badge stock">{{ \App\Models\PremiumApp::stockStatusLabels()[$app->stock_status] ?? $app->stock_status }}</span>
                            <span class="badge {{ $app->status }}">{{ $statusLabels[$app->status] ?? $app->status }}</span>
                            <a class="button secondary" href="{{ route('admin.premium-apps.edit', $app) }}">แก้ไข</a>
                            <form class="inline-form" method="POST" action="{{ route('admin.premium-apps.destroy', $app) }}" onsubmit="return confirm('ลบแอพพรีเมียมนี้?')">
                                @csrf
                                @method('DELETE')
                                <button class="button danger" type="submit">ลบ</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </section>
            <div class="pagination">{{ $apps->links() }}</div>
        @endif
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
