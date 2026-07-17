<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Good Friend Shop Admin</title>
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
                radial-gradient(circle at 100% 100%, rgba(190, 242, 100, 0.08), transparent 30rem),
                var(--bg);
            color: #fff;
            font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif;
        }

        a, button { font: inherit; }
        a { color: inherit; text-decoration: none; }

        .shell {
            display: flex;
            width: min(1380px, 100%);
            min-height: 100vh;
            margin: 0 auto;
        }

        .sidebar {
            width: 280px;
            padding: 24px;
            border-right: 1px solid rgba(102, 237, 189, 0.12);
            background: rgba(7, 17, 15, 0.88);
        }

        .brand-kicker {
            margin: 0;
            color: rgba(102, 237, 189, 0.78);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .brand-title {
            margin: 8px 0 28px;
            font-size: 26px;
            line-height: 1.15;
        }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav-item {
            display: flex;
            min-height: 48px;
            align-items: center;
            border: 1px solid transparent;
            border-radius: 18px;
            padding: 0 14px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 800;
        }

        .nav-item.active {
            border-color: rgba(102, 237, 189, 0.35);
            background: rgba(102, 237, 189, 0.14);
            color: #eafff6;
        }

        a.nav-item:hover {
            border-color: rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.045);
            color: #fff;
        }

        .content {
            min-width: 0;
            flex: 1;
            padding: 24px 28px 44px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .eyebrow {
            margin: 0;
            color: rgba(102, 237, 189, 0.76);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .page-title {
            margin: 6px 0 0;
            font-size: clamp(28px, 4vw, 38px);
            line-height: 1.12;
        }

        .logout {
            min-height: 44px;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0 18px;
            background: rgba(255, 255, 255, 0.055);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }

        .sales-metrics {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .metric-card,
        .panel {
            border: 1px solid var(--line);
            border-radius: 22px;
            background: var(--panel);
            box-shadow: 0 18px 52px rgba(0, 0, 0, 0.18);
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            min-height: 116px;
            padding: 16px;
        }

        .metric-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: var(--accent, var(--green));
        }

        .metric-card::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 100% 0%, var(--accent-soft, rgba(102, 237, 189, 0.12)), transparent 58%);
        }

        .metric-card > * {
            position: relative;
            z-index: 1;
        }

        .metric-card.tone-green,
        .metric-card.tone-emerald {
            --accent: #34d399;
            --accent-soft: rgba(52, 211, 153, 0.18);
        }

        .metric-card.tone-cyan {
            --accent: #22d3ee;
            --accent-soft: rgba(34, 211, 238, 0.16);
        }

        .metric-card.tone-teal {
            --accent: #2dd4bf;
            --accent-soft: rgba(45, 212, 191, 0.16);
        }

        .metric-card.tone-lime {
            --accent: #a3e635;
            --accent-soft: rgba(163, 230, 53, 0.15);
        }

        .metric-card.tone-sky {
            --accent: #38bdf8;
            --accent-soft: rgba(56, 189, 248, 0.16);
        }

        .metric-card.tone-violet {
            --accent: #a78bfa;
            --accent-soft: rgba(167, 139, 250, 0.16);
        }

        .metric-card.tone-gold {
            --accent: #fbbf24;
            --accent-soft: rgba(251, 191, 36, 0.17);
        }

        .metric-card.tone-rose {
            --accent: #fb7185;
            --accent-soft: rgba(251, 113, 133, 0.16);
        }

        .metric-card.tone-pink {
            --accent: #f472b6;
            --accent-soft: rgba(244, 114, 182, 0.15);
        }

        .metric-label {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .metric-value {
            margin: 16px 0 0;
            font-size: 27px;
            font-weight: 900;
        }

        .metric-note {
            margin: 4px 0 0;
            color: color-mix(in srgb, var(--accent, #bbf7d0) 62%, white);
            font-size: 12px;
            font-weight: 800;
        }

        .sales-card {
            border-color: color-mix(in srgb, var(--accent, #66edbd) 34%, transparent);
            background:
                linear-gradient(135deg, var(--accent-soft, rgba(102, 237, 189, 0.14)), rgba(255, 255, 255, 0.025)),
                var(--panel);
        }

        .sales-card .metric-value {
            color: color-mix(in srgb, var(--accent, #bbf7d0) 70%, white);
            font-size: clamp(24px, 2.5vw, 31px);
        }

        .main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
            gap: 14px;
        }

        .panel {
            padding: 18px;
        }

        .panel-kicker {
            margin: 0;
            color: rgba(102, 237, 189, 0.75);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .panel-title {
            margin: 5px 0 7px;
            font-size: 20px;
        }

        .panel-text {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .button {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            margin-top: 14px;
            border-radius: 13px;
            padding: 0 14px;
            background: var(--green);
            color: #05140f;
            font-size: 13px;
            font-weight: 900;
        }

        .list {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .list-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 11px 13px;
            background: rgba(255, 255, 255, 0.035);
        }

        .list-row strong {
            display: block;
            font-size: 14px;
        }

        .list-row span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .empty {
            margin-top: 18px;
            color: var(--muted);
        }

        @media (max-width: 1100px) {
            .shell { display: block; }
            .sidebar {
                width: 100%;
                border-right: 0;
                border-bottom: 1px solid rgba(102, 237, 189, 0.12);
            }
            .nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sales-metrics { grid-template-columns: 1fr; }
            .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .sidebar, .content { padding: 20px 16px; }
            .topbar { display: grid; }
            .metrics { grid-template-columns: 1fr; }
            .nav { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main class="shell">
        <section class="content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Admin dashboard</p>
                    <h2 class="page-title">ภาพรวมระบบ</h2>
                </div>
            </header>

            <section class="metrics" aria-label="Overview">
                @foreach ($metrics as $metric)
                    <article class="metric-card tone-{{ $metric['tone'] ?? 'green' }}">
                        <p class="metric-label">{{ $metric['label'] }}</p>
                        <p class="metric-value">{{ number_format($metric['value']) }}</p>
                        <p class="metric-note">{{ $metric['note'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="metrics sales-metrics" aria-label="Sales summary">
                @php($salesTones = ['emerald', 'sky', 'violet'])
                @foreach ($salesMetrics as $metric)
                    <article class="metric-card sales-card tone-{{ $salesTones[$loop->index] ?? 'emerald' }}">
                        <p class="metric-label">{{ $metric['label'] }}</p>
                        <p class="metric-value">{{ $metric['value'] }}</p>
                        <p class="metric-note">{{ $metric['note'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="metrics" aria-label="Reports">
                @php($reportTones = ['gold', 'rose', 'cyan', 'pink'])
                @foreach ($reportMetrics as $metric)
                    <article class="metric-card tone-{{ $reportTones[$loop->index] ?? 'teal' }}">
                        <p class="metric-label">{{ $metric['label'] }}</p>
                        <p class="metric-value">{{ $metric['value'] }}</p>
                        <p class="metric-note">{{ $metric['note'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="main-grid">
                <article class="panel">
                    <p class="panel-kicker">Current function</p>
                    <h3 class="panel-title">ฟังก์ชันที่เปิดใช้งาน</h3>
                    <p class="panel-text">
                        ตอนนี้หลังบ้านเปิดใช้เฉพาะส่วนที่ทำเสร็จแล้ว: สินค้าและสมาชิก
                        ส่วนออเดอร์กับชำระเงินจะค่อยๆเพิ่มเมื่อโครงพร้อม
                    </p>
                    <a class="button" href="{{ route('admin.products.index') }}">ไปจัดการสินค้า</a>
                    <a class="button" href="{{ route('admin.users.index') }}">ไปจัดการสมาชิก</a>
                    <a class="button" href="{{ route('admin.orders.index') }}">ไปจัดการออเดอร์</a>
                </article>

                <aside class="panel">
                    <p class="panel-kicker">Top products</p>
                    <h3 class="panel-title">สินค้าขายดีเดือนนี้</h3>
                    @if ($topProducts->isEmpty())
                        <p class="empty">ยังไม่มีออเดอร์ที่ชำระแล้วในเดือนนี้</p>
                    @else
                        <div class="list">
                            @foreach ($topProducts as $product)
                                <div class="list-row">
                                    <div>
                                        <strong>{{ $product->package_name }}</strong>
                                        <span>{{ number_format($product->order_count) }} ออเดอร์</span>
                                    </div>
                                    <span>THB {{ number_format((float) $product->total_sales, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </section>

            <section class="main-grid" style="margin-top: 20px;">
                <article class="panel">
                    <p class="panel-kicker">Recent orders</p>
                    <h3 class="panel-title">ออเดอร์ล่าสุด</h3>
                    @if ($recentOrders->isEmpty())
                        <p class="empty">ยังไม่มีออเดอร์เข้ามา</p>
                    @else
                        <div class="list">
                            @foreach ($recentOrders as $order)
                                <a class="list-row" href="{{ route('admin.orders.edit', $order) }}">
                                    <div>
                                        <strong>{{ $order->order_number }} · {{ $order->package_name }}</strong>
                                        <span>{{ $order->customer_name ?: '-' }} · {{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <span>{{ $order->currency }} {{ number_format((float) $order->price, 2) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </article>

                <aside class="panel">
                    <p class="panel-kicker">Recent games</p>
                    <h3 class="panel-title">เกมล่าสุด</h3>
                    @if ($recentGames->isEmpty())
                        <p class="empty">ยังไม่มีเกมในระบบ</p>
                    @else
                        <div class="list">
                            @foreach ($recentGames as $game)
                                <div class="list-row">
                                    <div>
                                        <strong>{{ $game->name }}</strong>
                                        <span>{{ $game->packages_count }} แพ็กเกจ</span>
                                    </div>
                                    <span>{{ $game->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </section>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
