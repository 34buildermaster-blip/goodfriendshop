<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $order->order_number }} | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root { --bg: #050b0a; --panel: rgba(8, 17, 15, 0.94); --line: rgba(255,255,255,.1); --green: #66edbd; --muted: rgba(255,255,255,.62); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 50% -10%, rgba(56,189,148,.18), transparent 38rem), var(--bg); color: #fff; font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; }
        main { width: min(920px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .card { border: 1px solid var(--line); border-radius: 30px; padding: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0,0,0,.22); }
        .kicker { margin: 0; color: rgba(102,237,189,.78); font-size: 12px; font-weight: 800; letter-spacing: .2em; text-transform: uppercase; }
        h1 { margin: 8px 0 6px; font-size: clamp(28px, 5vw, 40px); }
        .lead, .muted { color: var(--muted); line-height: 1.7; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin: 22px 0; }
        .info { border: 1px solid var(--line); border-radius: 18px; padding: 16px; background: rgba(255,255,255,.04); }
        .info span { display: block; color: rgba(255,255,255,.42); font-size: 12px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .info strong { display: block; margin-top: 6px; color: #fff; font-size: 16px; }
        label { display: grid; gap: 8px; margin-top: 16px; color: rgba(255,255,255,.84); font-size: 14px; font-weight: 900; }
        select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; background: rgba(255,255,255,.04); color: #fff; font: inherit; outline: none; }
        select { height: 48px; padding: 0 16px; }
        textarea { min-height: 130px; padding: 14px 16px; resize: vertical; }
        option { color: #111827; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; border: 0; border-radius: 16px; padding: 0 18px; background: var(--green); color: #05140f; font: inherit; font-weight: 900; text-decoration: none; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255,255,255,.055); color: #fff; }
        .error { color: #fca5a5; font-size: 13px; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <section class="card">
            <p class="kicker">Order detail</p>
            <h1>{{ $order->order_number }}</h1>
            <p class="lead">แก้สถานะออเดอร์และจดโน้ตให้ทีมหลังบ้านติดตามงานได้</p>

            <div class="grid">
                <div class="info"><span>ลูกค้า</span><strong>{{ $order->customer_name ?: '-' }}</strong><p class="muted">{{ $order->customer_email ?: '-' }}<br>{{ $order->customer_phone ?: '-' }}</p></div>
                <div class="info"><span>สินค้า</span><strong>{{ $order->game_name }}</strong><p class="muted">{{ $order->package_name }}</p></div>
                <div class="info"><span>ข้อมูลเกม</span><strong>UID: {{ $order->player_identifier }}</strong><p class="muted">Server: {{ $order->server_identifier ?: '-' }}</p></div>
                <div class="info"><span>ยอดชำระ</span><strong>{{ $order->currency }} {{ number_format((float) $order->price, 2) }}</strong><p class="muted">{{ $order->created_at->format('d/m/Y H:i') }}</p></div>
            </div>

            @if ($order->customer_note)
                <div class="info">
                    <span>หมายเหตุจากลูกค้า</span>
                    <p class="muted">{{ $order->customer_note }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                @csrf
                @method('PUT')

                <label>
                    สถานะ
                    <select name="status" required>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    โน้ตหลังบ้าน
                    <textarea name="admin_note">{{ old('admin_note', $order->admin_note) }}</textarea>
                    @error('admin_note') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="actions">
                    <button class="button" type="submit">บันทึกออเดอร์</button>
                    <a class="button secondary" href="{{ route('admin.orders.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
