<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ตั้งค่าทั่วไป | Good Friend Shop Admin</title>
    <link rel="stylesheet" href="{{ asset('css/line-seed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-shell.css') }}">
    <style>
        :root { --bg: #050b0a; --panel: rgba(8, 17, 15, 0.94); --line: rgba(255, 255, 255, 0.1); --green: #66edbd; --muted: rgba(255, 255, 255, 0.62); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.18), transparent 38rem), var(--bg); color: #fff; font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; }
        main { width: min(980px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .card { border: 1px solid var(--line); border-radius: 30px; padding: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 22px; font-size: clamp(28px, 5vw, 40px); }
        h2 { margin: 28px 0 0; font-size: 20px; }
        label { display: grid; gap: 8px; margin-top: 16px; color: rgba(255, 255, 255, 0.84); font-size: 14px; font-weight: 900; }
        input, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: 0 16px; background: rgba(255, 255, 255, 0.04); color: #fff; font: inherit; outline: none; }
        input { height: 48px; }
        textarea { min-height: 130px; padding-top: 14px; resize: vertical; }
        input:focus, textarea:focus { border-color: rgba(102, 237, 189, 0.62); box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 14px; }
        .notice { margin: 0 0 18px; border: 1px solid rgba(102, 237, 189, 0.2); border-radius: 18px; padding: 14px 16px; background: rgba(102, 237, 189, 0.09); color: #bbf7d0; font-weight: 800; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; border: 0; border-radius: 16px; padding: 0 18px; background: var(--green); color: #05140f; font: inherit; font-weight: 900; cursor: pointer; }
        .error { color: #fca5a5; font-size: 13px; }
        .hint { color: var(--muted); font-size: 13px; font-weight: 700; }
        @media (max-width: 700px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        <section class="card">
            <p class="kicker">Website settings</p>
            <h1>ตั้งค่าทั่วไป</h1>
            <p class="hint">แก้ข้อมูลร้านที่แสดงในหน้าแรก เช่น ชื่อเว็บ, footer, LINE, อีเมล, เบอร์โทร และ Facebook</p>

            <form method="POST" action="{{ route('admin.site-settings.update') }}">
                @csrf
                @method('PUT')

                @php
                    $groupedSettings = $settings->groupBy('group');
                    $groupLabels = ['general' => 'ข้อมูลร้าน', 'contact' => 'ช่องทางติดต่อ'];
                @endphp

                @foreach ($groupedSettings as $group => $items)
                    <h2>{{ $groupLabels[$group] ?? $group }}</h2>
                    <div class="grid">
                        @foreach ($items as $setting)
                            <label @if ($setting->type === 'textarea') style="grid-column: 1 / -1;" @endif>
                                {{ $setting->label }}
                                @if ($setting->type === 'textarea')
                                    <textarea name="settings[{{ $setting->key }}]">{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
                                @else
                                    <input name="settings[{{ $setting->key }}]" value="{{ old("settings.{$setting->key}", $setting->value) }}">
                                @endif
                                @error("settings.{$setting->key}") <span class="error">{{ $message }}</span> @enderror
                            </label>
                        @endforeach
                    </div>
                @endforeach

                <div style="margin-top: 22px;">
                    <button class="button" type="submit">บันทึกตั้งค่า</button>
                </div>
            </form>
        </section>
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
