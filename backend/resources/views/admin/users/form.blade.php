<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Good Friend Shop Admin</title>
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
            background: radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.16), transparent 38rem), var(--bg);
            color: #fff;
            font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif;
        }
        main { width: min(760px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .card { border: 1px solid var(--line); border-radius: 30px; padding: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 22px; font-size: clamp(28px, 5vw, 40px); }
        label { display: grid; gap: 8px; margin-top: 16px; color: rgba(255, 255, 255, 0.84); font-size: 14px; font-weight: 900; }
        input, select {
            width: 100%;
            height: 48px;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font: inherit;
            outline: none;
        }
        input:focus, select:focus { border-color: rgba(102, 237, 189, 0.62); box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1); }
        option { color: #111827; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 14px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; border: 0; border-radius: 16px; padding: 0 18px; background: var(--green); color: #05140f; font: inherit; font-weight: 900; text-decoration: none; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255, 255, 255, 0.055); color: #fff; }
        .error { color: #fca5a5; font-size: 13px; }
        .hint { color: var(--muted); font-size: 13px; font-weight: 700; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <section class="card">
            <p class="kicker">Member account</p>
            <h1>{{ $title }}</h1>

            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <label>
                    ชื่อ
                    <input name="name" value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    อีเมล
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        เบอร์โทร
                        <input name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        LINE ID
                        <input name="line_id" value="{{ old('line_id', $user->line_id) }}">
                        @error('line_id') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="grid">
                    <label>
                        บทบาท
                        <select name="role" required>
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        สถานะ
                        <select name="status" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $user->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="grid">
                    <label>
                        รหัสผ่าน
                        <input name="password" type="password" @required($method === 'POST')>
                        @if ($method !== 'POST')
                            <span class="hint">เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยนรหัสผ่าน</span>
                        @endif
                        @error('password') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        ยืนยันรหัสผ่าน
                        <input name="password_confirmation" type="password" @required($method === 'POST')>
                    </label>
                </div>

                <div class="actions">
                    <button class="button" type="submit">บันทึก</button>
                    <a class="button secondary" href="{{ route('admin.users.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
