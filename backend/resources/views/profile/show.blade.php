<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>โปรไฟล์ | Good Friend Shop</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <style>
        :root {
            --bg: #050b0a;
            --panel: rgba(8, 17, 15, 0.92);
            --line: rgba(255, 255, 255, 0.1);
            --green: #66edbd;
            --muted: rgba(255, 255, 255, 0.62);
        }

        * {
            box-sizing: border-box;
        }

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
            width: min(960px, 100%);
            margin: 0 auto;
            padding: 32px 20px;
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

        .logout {
            height: 44px;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0 18px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 26px;
            background: var(--panel);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.24);
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .field {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.035);
        }

        .field span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .field strong {
            display: block;
            margin-top: 8px;
            font-size: 18px;
        }

        .admin-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 46px;
            margin-top: 20px;
            border-radius: 16px;
            padding: 0 20px;
            background: var(--green);
            color: #05140f;
            font-weight: 900;
            text-decoration: none;
        }

        @media (max-width: 700px) {
            .topbar {
                display: grid;
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main>
        <header class="topbar">
            <div>
                <p class="kicker">Good Friend Shop</p>
                <h1>โปรไฟล์ของฉัน</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout" type="submit">ออกจากระบบ</button>
            </form>
        </header>

        <section class="card">
            <p class="kicker">Account information</p>
            <div class="profile-grid">
                <div class="field">
                    <span>ชื่อผู้ใช้</span>
                    <strong>{{ $user->name }}</strong>
                </div>
                <div class="field">
                    <span>อีเมล</span>
                    <strong>{{ $user->email }}</strong>
                </div>
                <div class="field">
                    <span>เบอร์โทร</span>
                    <strong>{{ $user->phone ?: '-' }}</strong>
                </div>
                <div class="field">
                    <span>LINE ID</span>
                    <strong>{{ $user->line_id ?: '-' }}</strong>
                </div>
                <div class="field">
                    <span>ประเภทบัญชี</span>
                    <strong>{{ $user->isAdmin() ? 'ผู้ดูแลระบบ' : 'ลูกค้า' }}</strong>
                </div>
                <div class="field">
                    <span>สมัครเมื่อ</span>
                    <strong>{{ $user->created_at?->format('d/m/Y H:i') }}</strong>
                </div>
            </div>

            @if ($user->isAdmin())
                <a class="admin-link" href="{{ route('admin.dashboard') }}">ไปหน้า Admin</a>
            @endif
        </section>
    </main>
</body>
</html>
