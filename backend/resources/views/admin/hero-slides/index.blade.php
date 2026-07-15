<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สไลด์หน้าแรก | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root { --bg: #050b0a; --panel: rgba(8, 17, 15, 0.94); --line: rgba(255, 255, 255, 0.1); --green: #66edbd; --muted: rgba(255, 255, 255, 0.62); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.16), transparent 38rem), var(--bg); color: #fff; font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; }
        main { width: min(1180px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        a, button { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .topbar { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 0; font-size: clamp(28px, 5vw, 42px); line-height: 1.16; }
        .notice { margin: 0 0 18px; border: 1px solid rgba(102, 237, 189, 0.2); border-radius: 18px; padding: 14px 16px; background: rgba(102, 237, 189, 0.09); color: #bbf7d0; font-weight: 800; }
        .empty, .slide-card { border: 1px solid var(--line); border-radius: 28px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .empty { padding: 28px; color: var(--muted); text-align: center; }
        .slide-list { display: grid; gap: 16px; }
        .slide-card { display: grid; grid-template-columns: 180px minmax(0, 1fr) auto; gap: 16px; align-items: start; padding: 18px; }
        .slide-thumb { width: 180px; aspect-ratio: 16 / 9; border: 1px solid var(--line); border-radius: 18px; object-fit: cover; background: rgba(255, 255, 255, 0.055); }
        .slide-title { margin: 0; font-size: 23px; }
        .meta { margin: 7px 0 0; color: var(--muted); line-height: 1.7; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
        .button { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; border: 0; border-radius: 14px; padding: 0 16px; background: var(--green); color: #05140f; font-weight: 900; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255, 255, 255, 0.055); color: #fff; }
        .button.danger { border: 1px solid rgba(248, 113, 113, 0.28); background: rgba(248, 113, 113, 0.1); color: #fecaca; }
        .badge { display: inline-flex; border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 900; background: rgba(255, 255, 255, 0.06); color: rgba(255, 255, 255, 0.72); }
        .badge.active { background: rgba(110, 231, 183, 0.12); color: #bbf7d0; }
        .inline-form { display: inline; }
        .pagination { margin-top: 18px; color: var(--muted); }
        @media (max-width: 860px) { .slide-card { grid-template-columns: 1fr; } .slide-thumb { width: 100%; } .actions { justify-content: stretch; } .button { flex: 1; } .topbar { align-items: stretch; flex-direction: column; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    <main>
        <header class="topbar">
            <div>
                <p class="kicker">Homepage slides</p>
                <h1>สไลด์หน้าแรก</h1>
            </div>
            <a class="button" href="{{ route('admin.hero-slides.create') }}">เพิ่มสไลด์</a>
        </header>
        @if (session('status')) <p class="notice">{{ session('status') }}</p> @endif
        @if ($slides->isEmpty())
            <section class="empty">ยังไม่มีสไลด์หน้าแรก</section>
        @else
            <section class="slide-list">
                @foreach ($slides as $slide)
                    <article class="slide-card">
                        @if ($slide->imageUrl())
                            <img class="slide-thumb" src="{{ $slide->imageUrl() }}" alt="{{ $slide->title }}">
                        @else
                            <div class="slide-thumb" aria-hidden="true"></div>
                        @endif
                        <div>
                            <h2 class="slide-title">{{ $slide->title }}</h2>
                            <p class="meta">{{ $slide->eyebrow }} · {{ $slide->highlight }} · ปุ่ม: {{ $slide->cta_label ?: '-' }}</p>
                            @if ($slide->quote)<p class="meta">{{ $slide->quote }}</p>@endif
                        </div>
                        <div class="actions">
                            <span class="badge {{ $slide->is_active ? 'active' : '' }}">{{ $slide->is_active ? 'เปิดใช้' : 'ปิด' }}</span>
                            <a class="button secondary" href="{{ route('admin.hero-slides.edit', $slide) }}">แก้ไข</a>
                            <form class="inline-form" method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" onsubmit="return confirm('ลบสไลด์นี้?')">
                                @csrf
                                @method('DELETE')
                                <button class="button danger" type="submit">ลบ</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </section>
            <div class="pagination">{{ $slides->links() }}</div>
        @endif
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
