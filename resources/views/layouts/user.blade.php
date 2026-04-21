<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bandeja Padel Arena')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-deep:   #2D4A1E;
            --green-mid:    #3A5C28;
            --green-light:  #4A7035;
            --green-accent: #5C8A42;
            --cream-bg:     #EDE8D8;
            --cream-card:   #F5F1E6;
            --cream-white:  #FAFAF5;
            --text-dark:    #1A1A0F;
            --text-muted:   #6B6B5A;
            --sidebar-w:    220px;
            --gold:         #C8922A;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Figtree', sans-serif; background: var(--cream-bg); color: var(--text-dark); }
        .serif { font-family: 'DM Serif Display', serif; }

        /* ══════════ SIDEBAR ══════════ */
        #user-sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, var(--green-deep) 0%, var(--green-mid) 60%, var(--green-light) 100%);
            display: flex; flex-direction: column;
            z-index: 100; overflow: hidden;
        }

        /* Subtle texture overlay */
        #user-sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative; z-index: 1;
        }
        .logo-box {
            display: flex; align-items: center; gap: 10px;
        }
        .logo-icon {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .logo-text { font-family: 'DM Serif Display', serif; color: #fff; font-size: 15px; line-height: 1.25; }
        .logo-sub  { color: rgba(255,255,255,0.4); font-size: 9px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; margin-top: 2px; }

        .sidebar-nav {
            flex: 1; padding: 20px 14px;
            display: flex; flex-direction: column; gap: 2px;
            position: relative; z-index: 1;
            overflow-y: auto;
        }

        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 14px; border-radius: 10px;
            color: rgba(255,255,255,0.65); font-size: 15px; font-weight: 600;
            text-decoration: none; transition: all 0.18s ease;
            position: relative;
        }
        .nav-link:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            box-shadow: inset 3px 0 0 rgba(255,255,255,0.5);
        }
        .nav-link .nav-icon {
            width: 20px; text-align: center; font-size: 16px; flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 16px 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            position: relative; z-index: 1;
        }
        .user-chip {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            background: rgba(255,255,255,0.07);
            margin-bottom: 8px;
        }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .user-name  { color: #fff; font-size: 12px; font-weight: 700; }
        .user-email { color: rgba(255,255,255,0.4); font-size: 10px; }

        /* ══════════ MAIN ══════════ */
        #user-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }

        /* Toast */
        #toast-wrap {
            position: fixed; top: 20px; right: 20px;
            z-index: 9999; display: flex; flex-direction: column; gap: 8px;
        }
        .toast {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px; border-radius: 12px;
            background: #fff; font-size: 13px; font-weight: 600;
            min-width: 260px; max-width: 340px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.12);
            border-left: 3px solid transparent;
            animation: toastIn .3s ease;
        }
        .toast-success { border-left-color: #4A7035; color: #2D4A1E; }
        .toast-error   { border-left-color: #C0392B; color: #7B1A14; }
        .toast-icon    { font-size: 16px; }
        .toast-close   { margin-left: auto; background: none; border: none; cursor: pointer; color: #aaa; font-size: 18px; line-height: 1; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }
        @keyframes toastOut { to { opacity: 0; transform: translateX(20px); } }
        .toast.out { animation: toastOut .25s ease forwards; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══ SIDEBAR ══ --}}
<aside id="user-sidebar">
    <div class="sidebar-logo">
        <div class="logo-box">
            <div class="logo-icon">🏸</div>
            <div>
                <div class="logo-text">Bandeja<br>Padel Arena.</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('courts.index') }}"
           class="nav-link {{ request()->routeIs('courts.*') ? 'active' : '' }}">
            <span class="nav-icon">🏟</span> Courts
        </a>
        <a href="{{ route('coaches.index') }}"
           class="nav-link {{ request()->routeIs('coaches.*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span> Coaches
        </a>
        <a href="{{ route('proshop.index') }}"
           class="nav-link {{ request()->routeIs('proshop.*') ? 'active' : '' }}">
            <span class="nav-icon">🛍</span> Pro Shops
        </a>
        <a href="{{ route('membership.index') }}"
           class="nav-link {{ request()->routeIs('membership.*') ? 'active' : '' }}">
            <span class="nav-icon">⭐</span> Membership
        </a>
        <a href="{{ route('reviews.index') }}"
           class="nav-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}">
            <span class="nav-icon">💬</span> Reviews
        </a>

        <div style="flex:1"></div>

        <a href="{{ route('settings.index') }}"
           class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <span class="nav-icon">⚙️</span> Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="user-email">{{ auth()->user()->email ?? '' }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- ══ MAIN ══ --}}
<div id="user-main">
    @yield('content')
</div>

{{-- Toast --}}
<div id="toast-wrap"></div>

@if(session('status') || session('success'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    showToast('success', @json(session('status') ?? session('success')));
});
</script>
@endif
@if(session('error') || $errors->any())
<script>
document.addEventListener('DOMContentLoaded', () => {
    showToast('error', @json(session('error') ?? $errors->first() ?? 'Terjadi kesalahan.'));
});
</script>
@endif

<script>
function showToast(type, msg) {
    const wrap = document.getElementById('toast-wrap');
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.innerHTML = `<span class="toast-icon">${type==='success'?'✓':'✕'}</span><span>${msg}</span><button class="toast-close" onclick="dismissToast(this.parentElement)">×</button>`;
    wrap.appendChild(t);
    setTimeout(() => dismissToast(t), 4500);
}
function dismissToast(el) {
    if (!el?.parentElement) return;
    el.classList.add('out');
    setTimeout(() => el.remove(), 260);
}
</script>
@stack('scripts')
</body>
</html>
