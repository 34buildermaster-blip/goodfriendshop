<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการเกม | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root {
            --bg: #050b0a;
            --panel: rgba(8, 17, 15, 0.94);
            --line: rgba(255, 255, 255, 0.1);
            --green: #66edbd;
            --muted: rgba(255, 255, 255, 0.62);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.18), transparent 38rem),
                radial-gradient(circle at 100% 100%, rgba(190, 242, 100, 0.1), transparent 30rem),
                var(--bg);
            color: #fff;
            font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif;
        }

        main {
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 28px 20px 56px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 20px;
        }

        .kicker {
            margin: 0;
            color: rgba(102, 237, 189, 0.78);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0 0;
            font-size: clamp(28px, 5vw, 42px);
            line-height: 1.16;
        }

        a,
        button {
            font: inherit;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            border: 0;
            border-radius: 16px;
            padding: 0 18px;
            background: var(--green);
            color: #05140f;
            font-weight: 900;
            cursor: pointer;
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.055);
            color: #fff;
        }

        .button.danger {
            border: 1px solid rgba(248, 113, 113, 0.28);
            background: rgba(248, 113, 113, 0.1);
            color: #fecaca;
        }

        .notice {
            margin: 0 0 18px;
            border: 1px solid rgba(102, 237, 189, 0.2);
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(102, 237, 189, 0.09);
            color: #bbf7d0;
            font-weight: 800;
        }

        .empty {
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 28px;
            background: var(--panel);
            color: var(--muted);
            text-align: center;
        }

        .game-list {
            display: grid;
            gap: 18px;
        }

        .game-card {
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 20px;
            background: var(--panel);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22);
        }

        .game-head {
            display: grid;
            grid-template-columns: 74px minmax(0, 1fr) auto;
            gap: 16px;
            align-items: start;
        }

        .game-thumb {
            width: 74px;
            aspect-ratio: 1;
            border: 1px solid var(--line);
            border-radius: 18px;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.055);
        }

        .game-title {
            margin: 0;
            font-size: 24px;
        }

        .meta {
            margin: 7px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .badge {
            display: inline-flex;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 900;
        }

        .badge.active {
            border-color: rgba(110, 231, 183, 0.24);
            background: rgba(110, 231, 183, 0.12);
            color: #bbf7d0;
        }

        .badge.draft {
            border-color: rgba(252, 211, 77, 0.24);
            background: rgba(252, 211, 77, 0.12);
            color: #fde68a;
        }

        .badge.inactive {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.72);
        }

        .package-table {
            width: 100%;
            min-width: 760px;
            margin-top: 18px;
            border-collapse: separate;
            border-spacing: 0 8px;
            text-align: left;
            font-size: 14px;
        }

        th {
            padding: 8px 12px;
            color: rgba(255, 255, 255, 0.38);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        td {
            padding: 14px 12px;
            background: rgba(255, 255, 255, 0.035);
            color: rgba(255, 255, 255, 0.7);
        }

        td:first-child { border-radius: 16px 0 0 16px; }
        td:last-child { border-radius: 0 16px 16px 0; }

        .price {
            color: #fff;
            font-weight: 900;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .inline-form {
            display: inline;
        }

        .small-link {
            color: var(--green);
            font-weight: 900;
        }

        @media (max-width: 760px) {
            .topbar,
            .game-head {
                grid-template-columns: 1fr;
            }

            .topbar {
                display: grid;
            }

            .actions {
                width: 100%;
            }

            .button {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <div>
                <p class="kicker">Admin games</p>
                <h1>จัดการเกม</h1>
            </div>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        @if ($games->isEmpty())
            <section class="empty">
                ยังไม่มีเกมในระบบ เริ่มจากเพิ่มเกมแรกก่อน แล้วค่อยเพิ่มแพ็กเกจของเกมนั้น
            </section>
        @else
            <section class="game-list">
                @foreach ($games as $game)
                    <article class="game-card">
                        <div class="game-head">
                            @if ($game->imageUrl())
                                <img class="game-thumb" src="{{ $game->imageUrl() }}" alt="{{ $game->name }}">
                            @else
                                <div class="game-thumb" aria-hidden="true"></div>
                            @endif
                            <div>
                                <h2 class="game-title">{{ $game->name }}</h2>
                                <p class="meta">
                                    Slug: {{ $game->slug }}
                                    @if ($game->publisher)
                                        · ผู้ให้บริการ: {{ $game->publisher }}
                                    @endif
                                    · แพ็กเกจ {{ $game->packages_count }} รายการ
                                </p>
                                @if ($game->description)
                                    <p class="meta">{{ $game->description }}</p>
                                @endif
                            </div>
                            <div class="actions">
                                <span class="badge {{ $game->status }}">
                                    {{ $statusLabels[$game->status] ?? $game->status }}
                                </span>
                                <a class="button secondary" href="{{ route('admin.products.edit', $game) }}">แก้ไขเกม</a>
                                <a class="button" href="{{ route('admin.products.packages.create', $game) }}">เพิ่มแพ็กเกจ</a>
                                <form class="inline-form" method="POST" action="{{ route('admin.products.destroy', $game) }}" onsubmit="return confirm('ลบเกมนี้และแพ็กเกจทั้งหมด?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button danger" type="submit">ลบเกม</button>
                                </form>
                            </div>
                        </div>

                        <p class="meta">
                            แพ็กเกจ {{ $game->packages_count }} รายการ
                            · <a class="small-link" href="{{ route('admin.packages.index') }}">ไปหน้าจัดการแพ็กเกจ</a>
                        </p>
                    </article>
                @endforeach
            </section>
        @endif
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
