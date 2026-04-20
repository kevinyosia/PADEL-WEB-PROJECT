<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bandeja Padel Arena</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            line-height: 1.6;
        }

        .admin-wrapper {
            min-height: 100vh;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-right: 1px solid #1e293b;
            position: fixed;
            width: 280px;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            padding: 24px 0;
        }

        .sidebar-brand {
            padding: 0 24px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            font-size: 16px;
        }

        .sidebar-brand-icon {
            font-size: 24px;
        }

        .sidebar-menu {
            list-style: none;
            flex: 1;
        }

        .sidebar-menu-item {
            margin: 0;
            padding: 0;
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 13px;
            font-weight: 600;
        }

        .sidebar-menu-link:hover,
        .sidebar-menu-link.active {
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border-right: 3px solid #2563eb;
            padding-left: 21px;
        }

        .sidebar-menu-icon {
            font-size: 16px;
            min-width: 20px;
        }

        /* ─── Main Content ─── */
        .main-content {
            margin-left: 280px;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            height: 70px;
        }

        .topbar-left {
            font-size: 13px;
            color: #64748b;
        }

        .page-content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        /* ─── Toast Notifications ─── */
        .toast-container {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            border-left: 4px solid #10b981;
            color: #166534;
        }

        .toast.error {
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .toast.info {
            border-left: 4px solid #2563eb;
            color: #1e40af;
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ─── Sidebar Logout ─── */
        .sidebar-logout-wrap {
            padding: 0 24px;
            margin-top: auto;
        }

        .sidebar-logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px 14px;
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Figtree', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sidebar-logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.5);
            color: #fecaca;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        {{-- ─── Sidebar ─── --}}
        <div class="sidebar">
            <a href="{{ route('admin.dashboard.courts') }}" class="sidebar-brand">
                <span class="sidebar-brand-icon">🏐</span>
                <span>Bandeja</span>
            </a>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.dashboard.courts') }}" class="sidebar-menu-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                        <span class="sidebar-menu-icon">📊</span>
                        Courts
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.coaches.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}">
                        <span class="sidebar-menu-icon">👥</span>
                        Coaches
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.inventory.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                        <span class="sidebar-menu-icon">📦</span>
                        Inventory
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.pricing.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}">
                        <span class="sidebar-menu-icon">💰</span>
                        Pricing
                    </a>
                </li>
            </ul>

            {{-- Logout Button at Bottom --}}
            <div class="sidebar-logout-wrap">
                <form method="POST" action="{{ route('admin.logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn">🚪 Logout</button>
                </form>
            </div>
        </div>

        {{-- ─── Main Content ─── --}}
        <div class="main-content">
            {{-- Topbar --}}
            <div class="topbar">
                <div class="topbar-left">
                    {{ auth()->user()->name }} • Admin
                </div>
            </div>

            {{-- Page Content --}}
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Toast notification (if session has success/error)
        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if (session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
