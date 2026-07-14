<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สมัครสมาชิก | Good Friend Shop</title>
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
            display: grid;
            min-height: 100vh;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(620px, 100%);
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 28px;
            background: var(--panel);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.28);
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
            margin: 8px 0 8px;
            font-size: 32px;
            line-height: 1.18;
        }

        .lead {
            margin: 0 0 24px;
            color: var(--muted);
            line-height: 1.7;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 14px;
        }

        label {
            display: grid;
            gap: 8px;
            margin-top: 16px;
            color: rgba(255, 255, 255, 0.82);
            font-size: 14px;
            font-weight: 800;
        }

        input {
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

        input:focus {
            border-color: rgba(102, 237, 189, 0.62);
            box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1);
        }

        button {
            width: 100%;
            height: 50px;
            margin-top: 22px;
            border: 0;
            border-radius: 18px;
            background: var(--green);
            color: #05140f;
            font: inherit;
            font-weight: 900;
            cursor: pointer;
        }

        a {
            color: var(--green);
            font-weight: 800;
            text-decoration: none;
        }

        .error {
            margin-top: 8px;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 700;
        }

        .footnote {
            margin: 20px 0 0;
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 640px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main>
        <section class="card">
            <p class="kicker">Good Friend Shop</p>
            <h1>สมัครสมาชิก</h1>
            <p class="lead">สร้างบัญชีสำหรับเก็บโปรไฟล์ ดูประวัติคำสั่งซื้อ และเชื่อมข้อมูลเติมเกมในอนาคต</p>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <label>
                    ชื่อผู้ใช้
                    <input name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </label>

                <label>
                    อีเมล
                    <input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </label>

                <div class="grid">
                    <label>
                        เบอร์โทร
                        <input name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel">
                        @error('phone')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>
                        LINE ID
                        <input name="line_id" type="text" value="{{ old('line_id') }}">
                        @error('line_id')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="grid">
                    <label>
                        รหัสผ่าน
                        <input name="password" type="password" autocomplete="new-password" required>
                        @error('password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>
                        ยืนยันรหัสผ่าน
                        <input name="password_confirmation" type="password" autocomplete="new-password" required>
                    </label>
                </div>

                <button type="submit">สมัครสมาชิก</button>
            </form>

            <p class="footnote">มีบัญชีแล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
        </section>
    </main>
</body>
</html>
