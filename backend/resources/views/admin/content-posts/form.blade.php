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
        main { width: min(920px, 100%); margin: 0 auto; padding: 28px 20px 56px; }
        .card { border: 1px solid var(--line); border-radius: 30px; padding: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22); }
        .kicker { margin: 0; color: rgba(102, 237, 189, 0.78); font-size: 12px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase; }
        h1 { margin: 8px 0 22px; font-size: clamp(28px, 5vw, 40px); }
        h2 { margin: 28px 0 0; font-size: 20px; }
        label, .editor-field { display: grid; gap: 8px; margin-top: 16px; color: rgba(255, 255, 255, 0.84); font-size: 14px; font-weight: 900; }
        input, select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 16px; padding: 0 16px; background: rgba(255, 255, 255, 0.04); color: #fff; font: inherit; outline: none; }
        input, select { height: 48px; }
        textarea { min-height: 150px; padding-top: 14px; resize: vertical; }
        input:focus, select:focus, textarea:focus { border-color: rgba(102, 237, 189, 0.62); box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1); }
        option { color: #111827; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 14px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; border: 0; border-radius: 16px; padding: 0 18px; background: var(--green); color: #05140f; font: inherit; font-weight: 900; text-decoration: none; cursor: pointer; }
        .button.secondary { border: 1px solid var(--line); background: rgba(255, 255, 255, 0.055); color: #fff; }
        .error { color: #fca5a5; font-size: 13px; }
        .hint { color: var(--muted); font-size: 13px; font-weight: 700; }
        .tox.tox-tinymce { border: 1px solid var(--line); border-radius: 16px; overflow: hidden; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <section class="card">
            <p class="kicker">News & activity post</p>
            <h1>{{ $title }}</h1>

            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <label>
                    หัวข้อ
                    <input name="title" value="{{ old('title', $post->title) }}" required autofocus>
                    @error('title') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        Slug
                        <input name="slug" value="{{ old('slug', $post->slug) }}" placeholder="เว้นว่างให้ระบบสร้างให้">
                        @error('slug') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        ประเภท
                        <select name="type" required>
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $post->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <h2>รูปภาพ</h2>
                <label>
                    รูปปก
                    <input name="cover_image_file" type="file" accept=".webp,.png,.jpg,.jpeg,image/webp,image/png,image/jpeg">
                    <span class="hint">แนะนำ 1200x630px รองรับ .webp, .png, .jpg, .jpeg ไม่เกิน 3MB</span>
                    @if ($post->coverImageUrl())
                        <span class="hint">รูปปัจจุบัน: {{ $post->cover_image_path }}</span>
                    @endif
                    @error('cover_image_file') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    Path รูปปก
                    <input name="cover_image_path" value="{{ old('cover_image_path', $post->cover_image_path) }}" placeholder="/figma/news-cover.webp">
                    @error('cover_image_path') <span class="error">{{ $message }}</span> @enderror
                </label>

                <h2>SEO</h2>
                <div class="grid">
                    <label>
                        SEO Title
                        <input name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="180" placeholder="เว้นว่างเพื่อใช้หัวข้อบทความ">
                        @error('meta_title') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        SEO Description
                        <input name="meta_description" value="{{ old('meta_description', $post->meta_description) }}" maxlength="500" placeholder="คำอธิบายสำหรับ Google และ Social share">
                        @error('meta_description') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label>
                    OG Image
                    <input name="og_image_file" type="file" accept=".webp,.png,.jpg,.jpeg,image/webp,image/png,image/jpeg">
                    <span class="hint">แนะนำ 1200x630px ถ้าไม่ใส่จะใช้รูปปก</span>
                    @if ($post->ogImageUrl())
                        <span class="hint">รูป Social ปัจจุบัน: {{ $post->og_image_path ?: $post->cover_image_path }}</span>
                    @endif
                    @error('og_image_file') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    OG Image Path
                    <input name="og_image_path" value="{{ old('og_image_path', $post->og_image_path) }}" placeholder="/figma/news-share.webp">
                    @error('og_image_path') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    คำโปรย
                    <textarea name="excerpt" style="min-height: 96px">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label class="editor-field">
                    เนื้อหา
                    <textarea id="content-editor" name="content">{{ old('content', $post->content) }}</textarea>
                    <span class="hint">ใช้ H2/H3, ลิงก์, ตาราง และ bullet เพื่อจัดบทความให้อ่านง่ายและพร้อมทำ SEO</span>
                    @error('content') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        วันเผยแพร่
                        <input name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                        @error('published_at') <span class="error">{{ $message }}</span> @enderror
                    </label>
                    <label>
                        สถานะ
                        <select name="status" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $post->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label>
                    ลำดับแสดงผล
                    <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $post->sort_order ?? 0) }}">
                    @error('sort_order') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="actions">
                    <button class="button" type="submit">บันทึก</button>
                    <a class="button secondary" href="{{ route('admin.content-posts.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (! window.tinymce) {
                return;
            }

            tinymce.init({
                selector: '#content-editor',
                license_key: 'gpl',
                base_url: 'https://cdn.jsdelivr.net/npm/tinymce@7',
                suffix: '.min',
                height: 460,
                menubar: false,
                promotion: false,
                branding: false,
                skin: 'oxide-dark',
                content_css: 'dark',
                plugins: 'lists link image table code preview wordcount searchreplace autoresize',
                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | removeformat code preview',
                block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4',
                convert_urls: false,
                link_default_target: '_blank',
                images_file_types: 'jpeg,jpg,png,webp,gif',
                automatic_uploads: false,
                content_style: 'body { font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif; font-size: 16px; line-height: 1.8; } h2, h3, h4 { line-height: 1.25; } a { color: #66edbd; }',
                setup: function (editor) {
                    editor.on('change keyup undo redo', function () {
                        editor.save();
                    });
                },
            });

            var form = document.querySelector('form');

            if (form) {
                form.addEventListener('submit', function () {
                    tinymce.triggerSave();
                });
            }
        });
    </script>
</body>
</html>
