<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการแพ็กเกจ | Good Friend Shop Admin</title>
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
                radial-gradient(circle at 50% -10%, rgba(56, 189, 148, 0.16), transparent 38rem),
                radial-gradient(circle at 100% 100%, rgba(190, 242, 100, 0.08), transparent 30rem),
                var(--bg);
            color: #fff;
            font-family: "LINE Seed Sans TH", "Leelawadee UI", Tahoma, Arial, sans-serif;
        }

        main {
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 28px 20px 56px;
        }

        a,
        button {
            font: inherit;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar {
            margin-bottom: 20px;
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
            margin: 8px 0 0;
            font-size: clamp(28px, 5vw, 42px);
            line-height: 1.16;
        }

        .notice {
            margin: 0 0 18px;
            border: 1px solid rgba(102, 237, 189, 0.2);
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(102, 237, 189, 0.09);
            color: #bbf7d0;
            font-weight: 800;
        }

        .panel,
        .empty {
            border: 1px solid var(--line);
            border-radius: 30px;
            background: var(--panel);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22);
        }

        .panel {
            padding: 20px;
        }

        .empty {
            padding: 28px;
            color: var(--muted);
            text-align: center;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 940px;
            border-collapse: separate;
            border-spacing: 0 8px;
            text-align: left;
            font-size: 14px;
        }

        th {
            padding: 8px 12px;
            color: rgba(255, 255, 255, 0.38);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        td {
            padding: 14px 12px;
            background: rgba(255, 255, 255, 0.035);
            color: rgba(255, 255, 255, 0.72);
            vertical-align: middle;
        }

        td:first-child {
            border-radius: 16px 0 0 16px;
            color: #fff;
            font-weight: 900;
        }

        td:last-child {
            border-radius: 0 16px 16px 0;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
        }

        .price {
            color: #fff;
            font-weight: 900;
        }

        .badge {
            display: inline-flex;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 900;
        }

        .badge.active {
            border-color: rgba(110, 231, 183, 0.24);
            background: rgba(110, 231, 183, 0.12);
            color: #bbf7d0;
        }

        .badge.draft {
            border-color: rgba(252, 211, 77, 0.24);
            background: rgba(252, 211, 77, 0.12);
            color: #fde68a;
        }

        .badge.inactive {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.72);
        }

        .inline-form {
            display: inline;
        }

        .small-link {
            border: 0;
            background: transparent;
            color: var(--green);
            font: inherit;
            font-weight: 900;
            cursor: pointer;
        }

        .pagination {
            margin-top: 18px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <p class="kicker">Admin packages</p>
            <h1>จัดการแพ็กเกจ</h1>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        @if ($packages->isEmpty())
            <section class="empty">
                ยังไม่มีแพ็กเกจในระบบ เลือกเมนูเพิ่มแพ็กเกจจากแถบด้านซ้ายเพื่อเริ่มสร้างรายการแรก
            </section>
        @else
            <section class="panel">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>แพ็กเกจ</th>
                                <th>เกม</th>
                                <th>SKU</th>
                                <th>ราคา</th>
                                <th>ต้นทุน</th>
                                <th>ข้อมูลที่ต้องกรอก</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>
                                        {{ $package->name }}
                                        @if ($package->description)
                                            <div class="muted">{{ \Illuminate\Support\Str::limit($package->description, 72) }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $package->game?->name ?: '-' }}</td>
                                    <td>{{ $package->sku ?: '-' }}</td>
                                    <td class="price">{{ number_format((float) $package->price, 2) }} {{ $package->currency }}</td>
                                    <td>{{ $package->cost !== null ? number_format((float) $package->cost, 2) : '-' }}</td>
                                    <td>
                                        @forelse ($package->required_fields ?? [] as $field)
                                            {{ $requiredFieldLabels[$field] ?? $field }}@if (! $loop->last), @endif
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td>
                                        <span class="badge {{ $package->status }}">
                                            {{ $statusLabels[$package->status] ?? $package->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a class="small-link" href="{{ route('admin.packages.edit', $package) }}">แก้ไข</a>
                                        <form class="inline-form" method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('ลบแพ็กเกจนี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="small-link" type="submit">ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination">{{ $packages->links() }}</div>
            </section>
        @endif
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
