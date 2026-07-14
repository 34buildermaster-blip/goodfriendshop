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
        input, select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: 0 16px; background: rgba(255, 255, 255, 0.04); color: #fff; font: inherit; outline: none; }
        input, select { height: 48px; }
        textarea { min-height: 120px; padding-top: 14px; resize: vertical; }
        input:focus, select:focus, textarea:focus { border-color: rgba(102, 237, 189, 0.62); box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1); }
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
            <p class="kicker">Premium app</p>
            <h1>{{ $title }}</h1>

            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <label>
                    ชื่อแอพ
                    <input name="name" value="{{ old('name', $app->name) }}" required autofocus>
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        Slug
                        <input name="slug" value="{{ old('slug', $app->slug) }}" placeholder="เว้นว่างให้ระบบสร้างให้">
                        @error('slug') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        ผู้ให้บริการ
                        <input name="provider" value="{{ old('provider', $app->provider) }}" placeholder="Netflix, Spotify, Canva">
                        @error('provider') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label>
                    รูปแอพ
                    <input name="image_file" type="file" accept=".webp,.png,.jpg,.jpeg,image/webp,image/png,image/jpeg">
                    <span class="hint">แนะนำ 512x512px แบบสี่เหลี่ยมจัตุรัส รับขนาด 400x400 ถึง 2000x2000px รองรับ .webp, .png, .jpg, .jpeg ขนาดไฟล์ไม่เกิน 2MB</span>
                    @if ($app->imageUrl())
                        <span class="hint">รูปปัจจุบัน: {{ $app->image_path }}</span>
                    @endif
                    @error('image_file') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    Path รูปแอพ
                    <input name="image_path" value="{{ old('image_path', $app->image_path) }}" placeholder="/figma/app-netflix.webp">
                    <span class="hint">ใช้กรณีมีรูปจากระบบเดิมหรือ CDN ถ้าอัปโหลดไฟล์ใหม่ ระบบจะใช้ไฟล์ที่อัปโหลดแทน</span>
                    @error('image_path') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        ราคาขาย
                        <input name="price" type="number" min="0" step="0.01" value="{{ old('price', $app->price) }}" required>
                        @error('price') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        ต้นทุน
                        <input name="cost" type="number" min="0" step="0.01" value="{{ old('cost', $app->cost) }}">
                        @error('cost') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="grid">
                    <label>
                        สกุลเงิน
                        <input name="currency" maxlength="3" value="{{ old('currency', $app->currency ?? 'THB') }}" required>
                        @error('currency') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        อายุใช้งาน (วัน)
                        <input name="duration_days" type="number" min="1" value="{{ old('duration_days', $app->duration_days) }}" placeholder="30">
                        @error('duration_days') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label>
                    รายละเอียด
                    <textarea name="description">{{ old('description', $app->description) }}</textarea>
                    @error('description') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        สถานะ
                        <select name="status" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $app->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        ลำดับแสดงผล
                        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $app->sort_order ?? 0) }}">
                        @error('sort_order') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="actions">
                    <button class="button" type="submit">บันทึก</button>
                    <a class="button secondary" href="{{ route('admin.premium-apps.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
