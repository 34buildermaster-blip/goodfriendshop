<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการข่าวสาร/กิจกรรม | Good Friend Shop Admin</title>
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
        .panel, .empty { border: 1px solid var(--line); border-radius: 30px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .panel { padding: 20px; }
        .empty { padding: 28px; color: var(--muted); text-align: center; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 980px; border-collapse: separate; border-spacing: 0 8px; text-align: left; font-size: 14px; }
        th { padding: 8px 12px; color: rgba(255, 255, 255, 0.38); font-size: 12px; font-weight: 900; letter-spacing: 0.12em; text-transform: uppercase; }
        td { padding: 14px 12px; background: rgba(255, 255, 255, 0.035); color: rgba(255, 255, 255, 0.72); vertical-align: middle; }
        td:first-child { border-radius: 16px 0 0 16px; color: #fff; font-weight: 900; }
        td:last-child { border-radius: 0 16px 16px 0; }
        .post-cell { display: grid; grid-template-columns: 76px minmax(0, 1fr); gap: 12px; align-items: center; }
        .post-thumb { width: 76px; aspect-ratio: 16 / 9; border: 1px solid var(--line); border-radius: 14px; object-fit: cover; background: rgba(255, 255, 255, 0.055); }
        .muted { color: var(--muted); font-size: 13px; }
        .badge { display: inline-flex; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 900; }
        .badge.published, .badge.news { border-color: rgba(110, 231, 183, 0.24); background: rgba(110, 231, 183, 0.12); color: #bbf7d0; }
        .badge.draft, .badge.activity { border-color: rgba(252, 211, 77, 0.24); background: rgba(252, 211, 77, 0.12); color: #fde68a; }
        .badge.archived { background: rgba(255, 255, 255, 0.06); color: rgba(255, 255, 255, 0.72); }
        .inline-form { display: inline; }
        .small-link { border: 0; background: transparent; color: var(--green); font: inherit; font-weight: 900; cursor: pointer; }
        .pagination { margin-top: 18px; color: var(--muted); }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <p class="kicker">News & activities</p>
            <h1>จัดการข่าวสาร/กิจกรรม</h1>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        @if ($posts->isEmpty())
            <section class="empty">ยังไม่มีข่าวสารหรือกิจกรรมในระบบ เลือกเมนูเพิ่มข่าว/กิจกรรมจากแถบด้านซ้ายเพื่อเริ่มสร้างโพสต์แรก</section>
        @else
            <section class="panel">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>หัวข้อ</th>
                                <th>ประเภท</th>
                                <th>สถานะ</th>
                                <th>วันเผยแพร่</th>
                                <th>ลำดับ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>
                                        <div class="post-cell">
                                            @if ($post->coverImageUrl())
                                                <img class="post-thumb" src="{{ $post->coverImageUrl() }}" alt="{{ $post->title }}">
                                            @else
                                                <div class="post-thumb" aria-hidden="true"></div>
                                            @endif
                                            <div>
                                                {{ $post->title }}
                                                <div class="muted">Slug: {{ $post->slug }}</div>
                                                @if ($post->excerpt)
                                                    <div class="muted">{{ \Illuminate\Support\Str::limit($post->excerpt, 72) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge {{ $post->type }}">{{ $typeLabels[$post->type] ?? $post->type }}</span></td>
                                    <td><span class="badge {{ $post->status }}">{{ $statusLabels[$post->status] ?? $post->status }}</span></td>
                                    <td>{{ $post->published_at?->format('d/m/Y H:i') ?: '-' }}</td>
                                    <td>{{ $post->sort_order }}</td>
                                    <td>
                                        <a class="small-link" href="{{ route('admin.content-posts.edit', $post) }}">แก้ไข</a>
                                        <form class="inline-form" method="POST" action="{{ route('admin.content-posts.destroy', $post) }}" onsubmit="return confirm('ลบข่าวสาร/กิจกรรมนี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="small-link" type="submit">ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination">{{ $posts->links() }}</div>
            </section>
        @endif
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
