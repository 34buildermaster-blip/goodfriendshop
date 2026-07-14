<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสมาชิก | Good Friend Shop Admin</title>
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
                radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.16), transparent 38rem),
                var(--bg);
            color: #fff;
            font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif;
        }
        main { width: min(1180px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        a, button { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 20px; }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 0; font-size: clamp(28px, 5vw, 42px); line-height: 1.16; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; }
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
        .button.secondary { border: 1px solid var(--line); background: rgba(255, 255, 255, 0.055); color: #fff; }
        .button.danger { border: 1px solid rgba(248, 113, 113, 0.28); background: rgba(248, 113, 113, 0.1); color: #fecaca; }
        .notice { margin: 0 0 18px; border: 1px solid rgba(102, 237, 189, 0.2); border-radius: 18px; padding: 14px 16px; background: rgba(102, 237, 189, 0.09); color: #bbf7d0; font-weight: 800; }
        .panel { border: 1px solid var(--line); border-radius: 30px; padding: 20px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 860px; border-collapse: separate; border-spacing: 0 8px; text-align: left; font-size: 14px; }
        th { padding: 8px 12px; color: rgba(255, 255, 255, 0.38); font-size: 12px; font-weight: 900; letter-spacing: 0.12em; text-transform: uppercase; }
        td { padding: 14px 12px; background: rgba(255, 255, 255, 0.035); color: rgba(255, 255, 255, 0.72); vertical-align: middle; }
        td:first-child { border-radius: 16px 0 0 16px; color: #fff; font-weight: 900; }
        td:last-child { border-radius: 0 16px 16px 0; }
        .muted { color: var(--muted); font-size: 13px; }
        .badge { display: inline-flex; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 900; }
        .badge.admin, .badge.active { border-color: rgba(110, 231, 183, 0.24); background: rgba(110, 231, 183, 0.12); color: #bbf7d0; }
        .badge.customer { border-color: rgba(103, 232, 249, 0.24); background: rgba(103, 232, 249, 0.12); color: #cffafe; }
        .badge.suspended { border-color: rgba(248, 113, 113, 0.28); background: rgba(248, 113, 113, 0.1); color: #fecaca; }
        .inline-form { display: inline; }
        .small-link { border: 0; background: transparent; color: var(--green); font: inherit; font-weight: 900; cursor: pointer; }
        .pagination { margin-top: 18px; color: var(--muted); }
        @media (max-width: 760px) {
            .topbar { display: grid; }
            .actions, .button { width: 100%; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <div>
                <p class="kicker">Admin users</p>
                <h1>จัดการสมาชิก</h1>
            </div>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        <section class="panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>สมาชิก</th>
                            <th>ติดต่อ</th>
                            <th>บทบาท</th>
                            <th>สถานะ</th>
                            <th>สมัครเมื่อ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    {{ $user->name }}
                                    <div class="muted">{{ $user->email }}</div>
                                </td>
                                <td>
                                    {{ $user->phone ?: '-' }}
                                    <div class="muted">LINE: {{ $user->line_id ?: '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $user->role }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->status }}">{{ $statusLabels[$user->status] ?? $user->status }}</span>
                                </td>
                                <td>{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a class="small-link" href="{{ route('admin.users.edit', $user) }}">แก้ไข</a>
                                    @if (! $user->is(auth()->user()))
                                        <form class="inline-form" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('ลบสมาชิกนี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="small-link" type="submit">ลบ</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $users->links() }}</div>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
