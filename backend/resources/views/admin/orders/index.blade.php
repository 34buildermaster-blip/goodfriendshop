<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการออเดอร์ | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root { --bg: #050b0a; --panel: rgba(8, 17, 15, 0.94); --line: rgba(255,255,255,.1); --green: #66edbd; --muted: rgba(255,255,255,.62); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 50% -10%, rgba(56,189,148,.18), transparent 38rem), var(--bg); color: #fff; font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; }
        main { width: min(1180px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .topbar { display: flex; align-items: end; justify-content: space-between; gap: 18px; margin-bottom: 20px; }
        .kicker { margin: 0; color: rgba(102,237,189,.78); font-size: 12px; font-weight: 800; letter-spacing: .2em; text-transform: uppercase; }
        h1 { margin: 8px 0 0; font-size: clamp(28px, 5vw, 42px); line-height: 1.16; }
        a, button, input, select { font: inherit; }
        a { color: inherit; text-decoration: none; }
        .notice { margin: 0 0 18px; border: 1px solid rgba(102,237,189,.2); border-radius: 18px; padding: 14px 16px; background: rgba(102,237,189,.09); color: #bbf7d0; font-weight: 800; }
        .filters { display: grid; grid-template-columns: minmax(0, 1fr) 190px auto; gap: 10px; margin-bottom: 18px; }
        input, select { height: 46px; border: 1px solid var(--line); border-radius: 14px; padding: 0 14px; background: rgba(255,255,255,.045); color: #fff; outline: none; }
        option { color: #111827; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; border: 0; border-radius: 14px; padding: 0 18px; background: var(--green); color: #05140f; font-weight: 900; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255,255,255,.055); color: #fff; }
        .table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 24px; background: var(--panel); }
        table { width: 100%; min-width: 920px; border-collapse: collapse; text-align: left; }
        th, td { padding: 16px; border-bottom: 1px solid rgba(255,255,255,.07); }
        th { color: rgba(255,255,255,.42); font-size: 12px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; }
        td { color: rgba(255,255,255,.74); vertical-align: top; }
        tr:last-child td { border-bottom: 0; }
        .strong { color: #fff; font-weight: 900; }
        .muted { color: var(--muted); font-size: 13px; line-height: 1.55; }
        .badge { display: inline-flex; border-radius: 999px; padding: 5px 12px; background: rgba(102,237,189,.12); color: #bbf7d0; font-size: 12px; font-weight: 900; }
        .empty { border: 1px solid var(--line); border-radius: 24px; padding: 28px; background: var(--panel); color: var(--muted); text-align: center; }
        .pagination { margin-top: 18px; color: var(--muted); }
        @media (max-width: 760px) { .topbar, .filters { display: grid; grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <div>
                <p class="kicker">Admin orders</p>
                <h1>จัดการออเดอร์</h1>
            </div>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        <form class="filters" method="GET" action="{{ route('admin.orders.index') }}">
            <input name="search" value="{{ request('search') }}" placeholder="ค้นหาเลขออเดอร์ ชื่อลูกค้า เกม หรือ UID">
            <select name="status">
                <option value="">ทุกสถานะ</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="button" type="submit">ค้นหา</button>
        </form>

        @if ($orders->isEmpty())
            <section class="empty">ยังไม่มีออเดอร์เข้ามา</section>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ออเดอร์</th>
                            <th>ลูกค้า</th>
                            <th>สินค้า</th>
                            <th>ข้อมูลเกม</th>
                            <th>ยอด</th>
                            <th>สถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <div class="strong">{{ $order->order_number }}</div>
                                    <div class="muted">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="strong">{{ $order->customer_name ?: '-' }}</div>
                                    <div class="muted">{{ $order->customer_email ?: '-' }}</div>
                                    <div class="muted">{{ $order->customer_phone ?: '-' }}</div>
                                </td>
                                <td>
                                    <div class="strong">{{ $order->game_name }}</div>
                                    <div class="muted">{{ $order->package_name }}</div>
                                </td>
                                <td>
                                    <div class="strong">UID: {{ $order->player_identifier }}</div>
                                    @if ($order->server_identifier)
                                        <div class="muted">Server: {{ $order->server_identifier }}</div>
                                    @endif
                                </td>
                                <td class="strong">{{ $order->currency }} {{ number_format((float) $order->price, 2) }}</td>
                                <td><span class="badge">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
                                <td><a class="button secondary" href="{{ route('admin.orders.edit', $order) }}">จัดการ</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $orders->links() }}</div>
        @endif
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
