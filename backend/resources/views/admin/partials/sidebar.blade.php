@php
    $productsActive = request()->routeIs('admin.products.index', 'admin.products.create', 'admin.products.edit');
    $packagesActive = request()->routeIs('admin.packages.*', 'admin.products.packages.*');
    $premiumAppsActive = request()->routeIs('admin.premium-apps.*');
    $contentPostsActive = request()->routeIs('admin.content-posts.*');
    $siteSettingsActive = request()->routeIs('admin.site-settings.*', 'admin.hero-slides.*', 'admin.announcements.*');
    $ordersActive = request()->routeIs('admin.orders.*');
    $usersActive = request()->routeIs('admin.users.*');
@endphp

<aside class="admin-sidebar" aria-label="Admin navigation">
    <div class="admin-sidebar-brand">
        <a class="admin-brand-lockup" href="{{ route('admin.dashboard') }}" aria-label="Good Friend Admin">
            <span class="admin-brand-mark">GF</span>
            <span class="admin-brand-copy">
                <span>Good Friend</span>
                <strong>Admin Center</strong>
            </span>
        </a>
        <button class="admin-sidebar-toggle" type="button" data-admin-sidebar-toggle aria-label="ย่อเมนู" aria-expanded="true">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M15.5 5 8.5 12l7 7" />
            </svg>
        </button>
    </div>

    <nav class="admin-sidebar-nav">
        <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif title="แดชบอร์ด">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 13h6V4H4v9Zm0 7h6v-5H4v5Zm10 0h6v-9h-6v9Zm0-11h6V4h-6v5Z" />
            </svg>
            <span class="admin-nav-label">แดชบอร์ด</span>
        </a>

        <details class="admin-nav-group" @if ($ordersActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $ordersActive ? 'is-active' : '' }}" title="ออเดอร์">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 4h10a2 2 0 0 1 2 2v15l-3-1.7-3 1.7-3-1.7L7 21V6a2 2 0 0 1 2-2Zm2 2v11.6l1-.6 3 1.7 3-1.7 1 .6V6H9Zm2 3h4v2h-4V9Zm0 4h5v2h-5v-2Z" />
                </svg>
                <span class="admin-nav-label">ออเดอร์</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.orders.index') ? 'is-active' : '' }}" href="{{ route('admin.orders.index') }}">รายการออเดอร์</a>
            </div>
        </details>

        <details class="admin-nav-group" @if ($productsActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $productsActive ? 'is-active' : '' }}" title="เกม">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 7.5 12 3l7 4.5v9L12 21l-7-4.5v-9Zm2 1.1v6.8l5 3.2 5-3.2V8.6l-5 3.2-5-3.2Zm1.4-1.2L12 9.7l3.6-2.3L12 5.1 8.4 7.4Z" />
                </svg>
                <span class="admin-nav-label">เกม</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.products.index') ? 'is-active' : '' }}" href="{{ route('admin.products.index') }}">รายการเกม</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.products.create') ? 'is-active' : '' }}" href="{{ route('admin.products.create') }}">เพิ่มเกม</a>
            </div>
        </details>

        <details class="admin-nav-group" @if ($packagesActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $packagesActive ? 'is-active' : '' }}" title="แพ็กเกจ">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a3 3 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a3 3 0 0 0 0-4V7Zm8-1v12m-5-9h4m2 0h4m-4 4h4m-10 0h4" />
                </svg>
                <span class="admin-nav-label">แพ็กเกจ</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.packages.index') ? 'is-active' : '' }}" href="{{ route('admin.packages.index') }}">รายการแพ็กเกจ</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.packages.create') ? 'is-active' : '' }}" href="{{ route('admin.packages.create') }}">เพิ่มแพ็กเกจ</a>
            </div>
        </details>

        <details class="admin-nav-group" @if ($premiumAppsActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $premiumAppsActive ? 'is-active' : '' }}" title="แอพพรีเมียม">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3 5 6v6c0 4.1 2.9 7.9 7 9 4.1-1.1 7-4.9 7-9V6l-7-3Zm0 2.2L17 7v5c0 3-2 5.8-5 6.8C9 17.8 7 15 7 12V7l5-1.8Zm-1 4.3h2v3h3v2h-3v3h-2v-3H8v-2h3v-3Z" />
                </svg>
                <span class="admin-nav-label">แอพพรีเมียม</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.premium-apps.index') ? 'is-active' : '' }}" href="{{ route('admin.premium-apps.index') }}">รายการแอพ</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.premium-apps.create') ? 'is-active' : '' }}" href="{{ route('admin.premium-apps.create') }}">เพิ่มแอพ</a>
            </div>
        </details>

        <details class="admin-nav-group" @if ($contentPostsActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $contentPostsActive ? 'is-active' : '' }}" title="ข่าวสาร/กิจกรรม">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 2v12h14V6H5Zm2 2h5v4H7V8Zm7 0h3v2h-3V8Zm0 4h3v2h-3v-2ZM7 14h10v2H7v-2Z" />
                </svg>
                <span class="admin-nav-label">ข่าวสาร/กิจกรรม</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.content-posts.index') ? 'is-active' : '' }}" href="{{ route('admin.content-posts.index') }}">รายการทั้งหมด</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.content-posts.create') ? 'is-active' : '' }}" href="{{ route('admin.content-posts.create') }}">เพิ่มข่าว/กิจกรรม</a>
            </div>
        </details>

        <details class="admin-nav-group" @if ($siteSettingsActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $siteSettingsActive ? 'is-active' : '' }}" title="ตั้งค่าเว็บไซต์">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2a2 2 0 0 1 2 2v1.1a7.8 7.8 0 0 1 1.8.75l.78-.78a2 2 0 0 1 2.83 0l1.52 1.52a2 2 0 0 1 0 2.83l-.78.78c.32.57.57 1.17.75 1.8H22a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1.1a7.8 7.8 0 0 1-.75 1.8l.78.78a2 2 0 0 1 0 2.83l-1.52 1.52a2 2 0 0 1-2.83 0l-.78-.78a7.8 7.8 0 0 1-1.8.75V22a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-1.1a7.8 7.8 0 0 1-1.8-.75l-.78.78a2 2 0 0 1-2.83 0L1.07 19.4a2 2 0 0 1 0-2.83l.78-.78A7.8 7.8 0 0 1 1.1 14H0a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h1.1c.18-.63.43-1.23.75-1.8l-.78-.78a2 2 0 0 1 0-2.83l1.52-1.52a2 2 0 0 1 2.83 0l.78.78A7.8 7.8 0 0 1 8 1.1V0a2 2 0 0 1 2-2h2Zm-1 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" transform="translate(2 2) scale(.83)" />
                </svg>
                <span class="admin-nav-label">ตั้งค่าเว็บไซต์</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.site-settings.*') ? 'is-active' : '' }}" href="{{ route('admin.site-settings.edit') }}">ตั้งค่าทั่วไป</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.hero-slides.*') ? 'is-active' : '' }}" href="{{ route('admin.hero-slides.index') }}">สไลด์หน้าแรก</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.announcements.*') ? 'is-active' : '' }}" href="{{ route('admin.announcements.index') }}">ประกาศ</a>
            </div>
        </details>

        <details class="admin-nav-group" @if ($usersActive) open @endif>
            <summary class="admin-nav-link admin-nav-summary {{ $usersActive ? 'is-active' : '' }}" title="สมาชิก">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0ZM4 21a8 8 0 0 1 16 0H4ZM19 8h3v2h-3v3h-2v-3h-3V8h3V5h2v3Z" />
                </svg>
                <span class="admin-nav-label">สมาชิก</span>
                <svg class="admin-nav-chevron" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8.5 9.5 12 13l3.5-3.5 1.5 1.5-5 5-5-5 1.5-1.5Z" />
                </svg>
            </summary>
            <div class="admin-subnav">
                <a class="admin-subnav-link {{ request()->routeIs('admin.users.index') ? 'is-active' : '' }}" href="{{ route('admin.users.index') }}">รายการสมาชิก</a>
                <a class="admin-subnav-link {{ request()->routeIs('admin.users.create') ? 'is-active' : '' }}" href="{{ route('admin.users.create') }}">เพิ่มสมาชิก</a>
            </div>
        </details>
    </nav>

    <form class="admin-sidebar-logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="admin-nav-link" type="submit" title="ออกจากระบบ">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M10 17v-2h4V9h-4V7l-5 5 5 5Zm3 4h6a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-6v2h6v14h-6v2Z" />
            </svg>
            <span class="admin-nav-label">ออกจากระบบ</span>
        </button>
    </form>
</aside>
