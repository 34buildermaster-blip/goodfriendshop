<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root { --bg: #050b0a; --panel: rgba(8, 17, 15, 0.94); --line: rgba(255, 255, 255, 0.1); --green: #66edbd; --muted: rgba(255, 255, 255, 0.62); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.18), transparent 38rem), var(--bg); color: #fff; font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; }
        main { width: min(820px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .card { border: 1px solid var(--line); border-radius: 30px; padding: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 22px; font-size: clamp(28px, 5vw, 40px); }
        label { display: grid; gap: 8px; margin-top: 16px; color: rgba(255, 255, 255, 0.84); font-size: 14px; font-weight: 900; }
        input, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: 0 16px; background: rgba(255, 255, 255, 0.04); color: #fff; font: inherit; outline: none; }
        input { height: 48px; }
        input[type="checkbox"] { width: 18px; height: 18px; }
        textarea { min-height: 140px; padding-top: 14px; resize: vertical; }
        input:focus, textarea:focus { border-color: rgba(102, 237, 189, 0.62); box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 14px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; border: 0; border-radius: 16px; padding: 0 18px; background: var(--green); color: #05140f; font: inherit; font-weight: 900; text-decoration: none; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255, 255, 255, 0.055); color: #fff; }
        .error { color: #fca5a5; font-size: 13px; }
        .hint { color: var(--muted); font-size: 13px; font-weight: 700; }
        .toggle { display: flex; align-items: center; gap: 10px; margin-top: 18px; font-weight: 900; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    <main>
        <section class="card">
            <p class="kicker">Announcement</p>
            <h1>{{ $title }}</h1>
            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST') @method($method) @endif
                <label>
                    ข้อความประกาศ
                    <textarea name="message" required autofocus>{{ old('message', $announcement->message) }}</textarea>
                    <span class="hint">ข้อความนี้จะแสดงเป็นข้อความเลื่อนตรงประกาศหน้าแรก</span>
                    @error('message') <span class="error">{{ $message }}</span> @enderror
                </label>
                <div class="grid">
                    <label>ลำดับ <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $announcement->sort_order ?? 0) }}">@error('sort_order') <span class="error">{{ $message }}</span> @enderror</label>
                    <label>เริ่มแสดง <input name="starts_at" type="datetime-local" value="{{ old('starts_at', optional($announcement->starts_at)->format('Y-m-d\TH:i')) }}">@error('starts_at') <span class="error">{{ $message }}</span> @enderror</label>
                </div>
                <label>หยุดแสดง <input name="ends_at" type="datetime-local" value="{{ old('ends_at', optional($announcement->ends_at)->format('Y-m-d\TH:i')) }}">@error('ends_at') <span class="error">{{ $message }}</span> @enderror</label>
                <label class="toggle"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $announcement->is_active))> เปิดใช้ประกาศนี้</label>
                <div class="actions">
                    <button class="button" type="submit">บันทึก</button>
                    <a class="button secondary" href="{{ route('admin.announcements.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
