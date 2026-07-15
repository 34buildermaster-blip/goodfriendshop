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
        main { width: min(860px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .card { border: 1px solid var(--line); border-radius: 30px; padding: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 22px; font-size: clamp(28px, 5vw, 40px); }
        label { display: grid; gap: 8px; margin-top: 16px; color: rgba(255, 255, 255, 0.84); font-size: 14px; font-weight: 900; }
        input, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: 0 16px; background: rgba(255, 255, 255, 0.04); color: #fff; font: inherit; outline: none; }
        input { height: 48px; }
        input[type="checkbox"] { width: 18px; height: 18px; }
        textarea { min-height: 120px; padding-top: 14px; resize: vertical; }
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
            <p class="kicker">Homepage slide</p>
            <h1>{{ $title }}</h1>
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @if ($method !== 'POST') @method($method) @endif
                <div class="grid">
                    <label>Eyebrow <input name="eyebrow" value="{{ old('eyebrow', $slide->eyebrow) }}" placeholder="SAFE TOPUP">@error('eyebrow') <span class="error">{{ $message }}</span> @enderror</label>
                    <label>ลำดับ <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $slide->sort_order ?? 0) }}">@error('sort_order') <span class="error">{{ $message }}</span> @enderror</label>
                </div>
                <label>หัวข้อ <input name="title" value="{{ old('title', $slide->title) }}" required autofocus>@error('title') <span class="error">{{ $message }}</span> @enderror</label>
                <label>ข้อความไฮไลต์ <input name="highlight" value="{{ old('highlight', $slide->highlight) }}">@error('highlight') <span class="error">{{ $message }}</span> @enderror</label>
                <label>คำโปรย <textarea name="quote">{{ old('quote', $slide->quote) }}</textarea>@error('quote') <span class="error">{{ $message }}</span> @enderror</label>
                <div class="grid">
                    <label>ข้อความปุ่ม <input name="cta_label" value="{{ old('cta_label', $slide->cta_label) }}" placeholder="เริ่มเติมเกม">@error('cta_label') <span class="error">{{ $message }}</span> @enderror</label>
                    <label>ลิงก์ปุ่ม <input name="cta_url" value="{{ old('cta_url', $slide->cta_url) }}" placeholder="/games">@error('cta_url') <span class="error">{{ $message }}</span> @enderror</label>
                </div>
                <label>
                    อัปโหลดรูปสไลด์
                    <input name="image_file" type="file" accept=".webp,.png,.jpg,.jpeg,image/webp,image/png,image/jpeg">
                    <span class="hint">แนะนำ 1600x900px หรือกว้างกว่า 900px รองรับ .webp, .png, .jpg, .jpeg ไม่เกิน 4MB</span>
                    @if ($slide->imageUrl()) <span class="hint">รูปปัจจุบัน: {{ $slide->image_path }}</span> @endif
                    @error('image_file') <span class="error">{{ $message }}</span> @enderror
                </label>
                <label>Path รูป <input name="image_path" value="{{ old('image_path', $slide->image_path) }}" placeholder="/figma/hero.webp">@error('image_path') <span class="error">{{ $message }}</span> @enderror</label>
                <label class="toggle"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $slide->is_active))> เปิดใช้สไลด์นี้</label>
                <div class="actions">
                    <button class="button" type="submit">บันทึก</button>
                    <a class="button secondary" href="{{ route('admin.hero-slides.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
