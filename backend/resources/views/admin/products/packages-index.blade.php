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
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
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

        .summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .summary-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.04);
        }

        .summary-card span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .summary-card strong {
            display: block;
            margin-top: 6px;
            font-size: 28px;
            line-height: 1;
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

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border: 1px solid rgba(102, 237, 189, 0.26);
            border-radius: 999px;
            padding: 0 18px;
            background: rgba(102, 237, 189, 0.14);
            color: #d1fae5;
            font-size: 14px;
            font-weight: 900;
        }

        .empty {
            border: 1px solid var(--line);
            border-radius: 30px;
            padding: 28px;
            background: var(--panel);
            color: var(--muted);
            text-align: center;
        }

        .game-stack {
            display: grid;
            gap: 14px;
        }

        .game-group {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 26px;
            background: var(--panel);
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.22);
        }

        .game-group[open] {
            border-color: rgba(102, 237, 189, 0.28);
        }

        .game-summary {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 18px;
            padding: 18px 20px;
            cursor: pointer;
            list-style: none;
        }

        .game-summary::-webkit-details-marker {
            display: none;
        }

        .game-title {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .chevron {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.07);
            color: var(--green);
            transition: transform 0.18s ease;
        }

        .game-group[open] .chevron {
            transform: rotate(90deg);
        }

        .game-title h2 {
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 20px;
        }

        .game-title p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .game-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 0 13px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .package-list {
            display: grid;
            gap: 10px;
            padding: 0 18px 18px;
        }

        .package-row {
            display: grid;
            grid-template-columns: minmax(220px, 1.5fr) minmax(120px, 0.7fr) minmax(130px, 0.7fr) minmax(160px, 1fr) minmax(110px, auto) minmax(90px, auto);
            gap: 12px;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.035);
        }

        .package-row.header {
            border-color: transparent;
            padding: 0 14px 2px;
            background: transparent;
            color: rgba(255, 255, 255, 0.38);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .package-name {
            color: #fff;
            font-weight: 900;
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
            width: fit-content;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
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
            white-space: nowrap;
        }

        .empty-game {
            border: 1px dashed rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 16px;
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 980px) {
            .summary {
                grid-template-columns: 1fr;
            }

            .game-summary {
                grid-template-columns: 1fr;
            }

            .game-actions {
                justify-content: space-between;
            }

            .package-row,
            .package-row.header {
                grid-template-columns: 1fr;
            }

            .package-row.header {
                display: none;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <main>
        <header class="topbar">
            <div>
                <p class="kicker">Admin packages</p>
                <h1>จัดการแพ็กเกจ</h1>
            </div>
            <a class="button" href="{{ route('admin.packages.create') }}">เพิ่มแพ็กเกจ</a>
        </header>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        <section class="summary">
            <div class="summary-card">
                <span>เกมทั้งหมด</span>
                <strong>{{ $games->count() }}</strong>
            </div>
            <div class="summary-card">
                <span>แพ็กเกจทั้งหมด</span>
                <strong>{{ $packageTotal }}</strong>
            </div>
            <div class="summary-card">
                <span>เกมที่มีแพ็กเกจ</span>
                <strong>{{ $games->where('packages_count', '>', 0)->count() }}</strong>
            </div>
        </section>

        @if ($packageTotal === 0)
            <section class="empty">
                ยังไม่มีแพ็กเกจในระบบ เลือกเมนูเพิ่มแพ็กเกจเพื่อเริ่มสร้างรายการแรก
            </section>
        @else
            <section class="game-stack">
                @foreach ($games as $game)
                    @php
                        $packages = $game->packages;
                    @endphp
                    <details class="game-group" @if ($loop->first) open @endif>
                        <summary class="game-summary">
                            <div class="game-title">
                                <span class="chevron">›</span>
                                <div>
                                    <h2>{{ $game->name }}</h2>
                                    <p>
                                        {{ $game->publisher ?: 'ไม่ระบุผู้ให้บริการ' }}
                                        · {{ $game->slug }}
                                    </p>
                                </div>
                            </div>
                            <div class="game-actions">
                                <span class="count-pill">{{ $packages->count() }} แพ็กเกจ</span>
                                <a class="button" href="{{ route('admin.products.packages.create', $game) }}">เพิ่มในเกมนี้</a>
                            </div>
                        </summary>

                        <div class="package-list">
                            @if ($packages->isEmpty())
                                <div class="empty-game">
                                    เกมนี้ยังไม่มีแพ็กเกจ
                                </div>
                            @else
                                <div class="package-row header">
                                    <span>แพ็กเกจ</span>
                                    <span>SKU</span>
                                    <span>ราคา / ต้นทุน</span>
                                    <span>ข้อมูลที่ต้องกรอก</span>
                                    <span>สถานะ</span>
                                    <span>จัดการ</span>
                                </div>

                                @foreach ($packages as $package)
                                    <article class="package-row">
                                        <div>
                                            <div class="package-name">{{ $package->name }}</div>
                                            @if ($package->description)
                                                <div class="muted">{{ \Illuminate\Support\Str::limit($package->description, 92) }}</div>
                                            @endif
                                        </div>
                                        <div>{{ $package->sku ?: '-' }}</div>
                                        <div>
                                            <div class="price">{{ number_format((float) $package->price, 2) }} {{ $package->currency }}</div>
                                            <div class="muted">
                                                ต้นทุน {{ $package->cost !== null ? number_format((float) $package->cost, 2) : '-' }}
                                            </div>
                                        </div>
                                        <div>
                                            @forelse ($package->required_fields ?? [] as $field)
                                                {{ $requiredFieldLabels[$field] ?? $field }}@if (! $loop->last), @endif
                                            @empty
                                                -
                                            @endforelse
                                        </div>
                                        <div>
                                            <span class="badge {{ $package->status }}">
                                                {{ $statusLabels[$package->status] ?? $package->status }}
                                            </span>
                                        </div>
                                        <div>
                                            <a class="small-link" href="{{ route('admin.packages.edit', $package) }}">แก้ไข</a>
                                            <form class="inline-form" method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('ลบแพ็กเกจนี้?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="small-link" type="submit">ลบ</button>
                                            </form>
                                        </div>
                                    </article>
                                @endforeach
                            @endif
                        </div>
                    </details>
                @endforeach
            </section>
        @endif
    </main>

    <script src="{{ asset('js/admin-shell.js') }}"></script>
</body>
</html>
