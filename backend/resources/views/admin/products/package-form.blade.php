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
            background:
                radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.18), transparent 38rem),
                var(--bg);
            color: #fff;
            font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif;
        }

        main {
            width: min(820px, 100%);
            margin: 0 auto;
            padding: 28px 20px 56px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 24px;
            background: var(--panel);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22);
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
            margin: 8px 0 6px;
            font-size: clamp(28px, 5vw, 40px);
        }

        .lead {
            margin: 0 0 20px;
            color: var(--muted);
        }

        label {
            display: grid;
            gap: 8px;
            margin-top: 16px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 14px;
            font-weight: 900;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font: inherit;
            outline: none;
        }

        input,
        select {
            height: 48px;
        }

        textarea {
            min-height: 110px;
            padding-top: 14px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: rgba(102, 237, 189, 0.62);
            box-shadow: 0 0 0 4px rgba(102, 237, 189, 0.1);
        }

        option {
            color: #111827;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 14px;
        }

        .check-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.035);
        }

        .check input {
            width: 18px;
            height: 18px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .button {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 16px;
            padding: 0 18px;
            background: var(--green);
            color: #05140f;
            font: inherit;
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.055);
            color: #fff;
        }

        .error {
            color: #fca5a5;
            font-size: 13px;
        }

        @media (max-width: 640px) {
            .grid,
            .check-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    @php
        $selectedFields = old('required_fields', $package->required_fields ?? []);
    @endphp

    <main>
        <section class="card">
            <p class="kicker">Package @if ($game) · {{ $game->name }} @endif</p>
            <h1>{{ $title }}</h1>
            <p class="lead">ตั้งราคา แพ็กเกจ และข้อมูลที่ลูกค้าต้องกรอกตอนสั่งซื้อ</p>

            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                @if (! $game && isset($games))
                    <label>
                        เกม
                        <select name="game_id" required autofocus>
                            <option value="">เลือกเกมสำหรับแพ็กเกจนี้</option>
                            @foreach ($games as $gameOption)
                                <option value="{{ $gameOption->id }}" @selected((int) old('game_id') === $gameOption->id)>{{ $gameOption->name }}</option>
                            @endforeach
                        </select>
                        @error('game_id') <span class="error">{{ $message }}</span> @enderror
                    </label>
                @endif

                <label>
                    ชื่อแพ็กเกจ
                    <input name="name" value="{{ old('name', $package->name) }}" required @if ($game || ! isset($games)) autofocus @endif>
                    @error('name') <span class="error">{{ $message }}</span> @enderror
                </label>

                <div class="grid">
                    <label>
                        SKU
                        <input name="sku" value="{{ old('sku', $package->sku) }}" placeholder="MLBB-257-DIAMONDS">
                        @error('sku') <span class="error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        สกุลเงิน
                        <input name="currency" maxlength="3" value="{{ old('currency', $package->currency ?? 'THB') }}" required>
                        @error('currency') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="grid">
                    <label>
                        ราคาขาย
                        <input name="price" type="number" min="0" step="0.01" value="{{ old('price', $package->price) }}" required>
                        @error('price') <span class="error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        ต้นทุน
                        <input name="cost" type="number" min="0" step="0.01" value="{{ old('cost', $package->cost) }}">
                        @error('cost') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label>
                    รายละเอียดแพ็กเกจ
                    <textarea name="description">{{ old('description', $package->description) }}</textarea>
                    @error('description') <span class="error">{{ $message }}</span> @enderror
                </label>

                <label>
                    ข้อมูลที่ลูกค้าต้องกรอก
                </label>
                <div class="check-grid">
                    @foreach ($requiredFieldLabels as $value => $label)
                        <label class="check">
                            <input name="required_fields[]" type="checkbox" value="{{ $value }}" @checked(in_array($value, $selectedFields, true))>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('required_fields') <span class="error">{{ $message }}</span> @enderror

                <div class="grid">
                    <label>
                        สถานะ
                        <select name="status" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $package->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="error">{{ $message }}</span> @enderror
                    </label>

                    <label>
                        ลำดับแสดงผล
                        <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $package->sort_order ?? 0) }}">
                        @error('sort_order') <span class="error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="actions">
                    <button class="button" type="submit">บันทึก</button>
                    <a class="button secondary" href="{{ route('admin.packages.index') }}">กลับ</a>
                </div>
            </form>
        </section>
    </main>
    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
