<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard | ' . config('app.name', 'Laravel'))</title>

    <!-- Google Fonts: Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Anti-flicker: Restore sidebar collapse state before render -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('thl_sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')

    <style id="thl-menu-theme">
        /* -------------------------------------------------------------
         * TÂN HÒA LỢI - PHẦN MỀM CHỞ HÀNG: MENU NAVIGATION THEME
         * 2 Tông màu chủ đạo: Trắng #ffffff & Xanh #193a77
         * Font-family: "Roboto", sans-serif
         * ----------------------------------------------------------- */
        body,
        .dashboard-body,
        .dashboard-sidebar,
        .dashboard-nav,
        .dashboard-nav-link,
        .dashboard-header,
        .dashboard-title,
        .dashboard-breadcrumb,
        .dashboard-user-button,
        .dashboard-user-name,
        .dashboard-user-menu {
            font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        }

        /* SIDEBAR CONTAINER */
        .dashboard-sidebar {
            display: flex;
            flex: 0 0 260px;
            width: 260px;
            min-height: 100vh;
            flex-direction: column;
            padding: 1.75rem 1.15rem 1.25rem;
            color: #ffffff !important;
            background: linear-gradient(180deg, #193a77 0%, #122852 100%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 2px 0 12px rgba(25, 58, 119, 0.12);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), flex 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            will-change: width;
            position: relative;
        }

        .dashboard-main {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            will-change: width;
        }

        /* HEADER ROW (BRAND & TOGGLE BUTTON) */
        .sidebar-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin: 0 0.15rem 1.75rem;
            transition: all 0.3s ease;
        }

        /* BRAND & LOGO */
        .dashboard-sidebar-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin: 0;
            padding: 0.45rem 0.6rem;
            color: #ffffff !important;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            transition: all 0.2s ease;
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .dashboard-sidebar-brand:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .thl-sidebar-logo-emblem {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        }

        .thl-sidebar-brand-text {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.2;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
        }

        .thl-sidebar-brand-name {
            font-size: 0.96rem;
            font-weight: 900;
            color: #ffffff !important;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .thl-sidebar-brand-sub {
            font-size: 0.62rem;
            font-weight: 700;
            color: #bed6fd !important;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* SIDEBAR COLLAPSE TOGGLE BUTTON (<< & >>) */
        .sidebar-collapse-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .sidebar-collapse-toggle-btn:hover {
            background: #ffffff;
            color: #193a77;
            border-color: #ffffff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
            transform: scale(1.05);
        }

        .sidebar-collapse-toggle-btn:active {
            transform: scale(0.96);
        }

        .sidebar-collapse-toggle-btn .toggle-icon-collapse {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-collapse-toggle-btn .toggle-icon-expand {
            display: none;
            align-items: center;
            justify-content: center;
        }

        /* SECTION LABELS */
        .sidebar-section-label {
            margin: 0.85rem 0.5rem 0.45rem;
            color: #8fb6f5 !important;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .sidebar-section-label-bottom {
            margin-top: 1.5rem;
        }

        /* NAV LINKS */
        .dashboard-nav {
            display: grid;
            gap: 0.35rem;
        }

        .dashboard-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.62rem 0.85rem;
            color: #d8e5fc !important;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.18s ease;
            position: relative;
            background: transparent;
        }

        .dashboard-nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.14) !important;
            transform: translateX(3px);
        }

        .dashboard-nav-link.active {
            color: #193a77 !important;
            background: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18) !important;
            transform: none;
        }

        /* ICONS */
        .dashboard-nav-icon {
            display: grid;
            place-items: center;
            width: 1.9rem;
            height: 1.9rem;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 6px;
            flex-shrink: 0;
            transition: all 0.18s ease;
        }

        .dashboard-nav-link:hover .dashboard-nav-icon {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .dashboard-nav-link.active .dashboard-nav-icon {
            background: #193a77 !important;
            color: #ffffff !important;
        }

        /* BOTTOM QUOTE / STATUS CARD */
        .sidebar-quote {
            margin-top: auto;
            padding: 0.85rem 1rem;
            color: #dbe7fb;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 10px;
            font-size: 0.78rem;
            line-height: 1.45;
        }

        .sidebar-quote-status {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }

        .status-live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
            display: inline-block;
        }

        .sidebar-quote p {
            margin: 0;
            font-size: 0.75rem;
            line-height: 1.4;
            color: #c0d4f5;
        }

        /* HEADER AREA (SYNCHRONIZED WITH WHITE & BLUE #193a77) */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem clamp(1.25rem, 4vw, 4rem);
            background: #ffffff;
            border-bottom: 1px solid #e1e9f4;
            box-shadow: 0 1px 3px rgba(25, 58, 119, 0.04);
        }

        .dashboard-breadcrumb {
            margin: 0 0 0.25rem;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .dashboard-title {
            margin: 0;
            font-size: 1.45rem;
            font-weight: 800;
            color: #193a77;
            letter-spacing: -0.02em;
        }

        .dashboard-date {
            color: #193a77;
            background: #eaf1fc;
            border: 1px solid #d5e2f3;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .dashboard-user-button {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.3rem 0.6rem;
            color: #193a77;
            background: #ffffff;
            border: 1px solid #d5e2f3;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.15s ease;
        }

        .dashboard-user-button:hover {
            background: #eaf1fc;
            border-color: #193a77;
            color: #193a77;
        }

        .dashboard-avatar {
            display: grid;
            place-items: center;
            width: 2rem;
            height: 2rem;
            color: #ffffff !important;
            background: #193a77 !important;
            border-radius: 50%;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .dashboard-user-menu {
            min-width: 220px;
            padding: 0.75rem;
            border: 1px solid #dbe5f2;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(25, 58, 119, 0.12);
        }

        .dashboard-user-menu .dropdown-item {
            border-radius: 6px;
            padding: 0.45rem 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.15s ease;
        }

        .dashboard-user-menu .dropdown-item:hover {
            background: #eaf1fc;
            color: #193a77;
        }

        /* MOBILE RESPONSIVE ADAPTATION */
        @media (max-width: 767.98px) {
            .dashboard-shell {
                display: block;
            }

            .dashboard-sidebar {
                min-height: auto;
                width: 100%;
                flex: none;
                padding: 1rem;
            }

            .dashboard-sidebar-brand {
                margin: 0 0 0.85rem;
            }

            .sidebar-section-label,
            .sidebar-section-label-bottom,
            .sidebar-quote {
                display: none;
            }

            .dashboard-nav {
                display: flex;
                gap: 0.4rem;
                overflow-x: auto;
                padding-bottom: 0.4rem;
                scrollbar-width: thin;
            }

            .dashboard-nav-link {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 0.5rem 0.8rem;
                font-size: 0.82rem;
            }

            .dashboard-nav-link.active {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
            }

            .dashboard-main {
                width: 100% !important;
            }
        }

        /* ORDER MENU TOGGLE BUTTON (WHITE & BLUE #193a77) */
        .order-menu-toggle-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            padding: 0.45rem 0.95rem !important;
            color: #193a77 !important;
            background: #ffffff !important;
            border: 1px solid #c8d9f1 !important;
            border-radius: 8px !important;
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 6px rgba(25, 58, 119, 0.08) !important;
        }

        .order-menu-toggle-btn:hover {
            background: #193a77 !important;
            color: #ffffff !important;
            border-color: #193a77 !important;
            box-shadow: 0 4px 12px rgba(25, 58, 119, 0.2) !important;
        }
        /* COLLAPSED SIDEBAR RULES (DESKTOP) */
        @media (min-width: 768px) {
            html.sidebar-collapsed .dashboard-sidebar,
            body.sidebar-collapsed .dashboard-sidebar {
                flex: 0 0 74px !important;
                width: 74px !important;
                padding: 1.25rem 0.55rem 1.25rem !important;
                overflow-x: hidden !important;
            }

            html.sidebar-collapsed .dashboard-main,
            body.sidebar-collapsed .dashboard-main {
                width: calc(100% - 74px) !important;
            }

            html.sidebar-collapsed .sidebar-header-row,
            body.sidebar-collapsed .sidebar-header-row {
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.65rem !important;
                margin: 0 0 1.25rem !important;
            }

            html.sidebar-collapsed .dashboard-sidebar-brand,
            body.sidebar-collapsed .dashboard-sidebar-brand {
                margin: 0 !important;
                padding: 0.3rem !important;
                justify-content: center !important;
                width: 44px !important;
                height: 44px !important;
                flex: none !important;
            }

            html.sidebar-collapsed .thl-sidebar-brand-text,
            body.sidebar-collapsed .thl-sidebar-brand-text,
            html.sidebar-collapsed .dashboard-nav-label,
            body.sidebar-collapsed .dashboard-nav-label,
            html.sidebar-collapsed .sidebar-section-label,
            body.sidebar-collapsed .sidebar-section-label,
            html.sidebar-collapsed .sidebar-quote,
            body.sidebar-collapsed .sidebar-quote {
                display: none !important;
            }

            html.sidebar-collapsed .dashboard-nav-link,
            body.sidebar-collapsed .dashboard-nav-link {
                justify-content: center !important;
                padding: 0.65rem 0 !important;
                gap: 0 !important;
            }

            html.sidebar-collapsed .dashboard-nav-link:hover,
            body.sidebar-collapsed .dashboard-nav-link:hover {
                transform: translateY(-2px) !important;
            }

            html.sidebar-collapsed .dashboard-nav-icon,
            body.sidebar-collapsed .dashboard-nav-icon {
                margin: 0 !important;
            }

            html.sidebar-collapsed .sidebar-collapse-toggle-btn,
            body.sidebar-collapsed .sidebar-collapse-toggle-btn {
                width: 36px !important;
                height: 32px !important;
            }

            html.sidebar-collapsed .sidebar-collapse-toggle-btn .toggle-icon-collapse,
            body.sidebar-collapsed .sidebar-collapse-toggle-btn .toggle-icon-collapse {
                display: none !important;
            }

            html.sidebar-collapsed .sidebar-collapse-toggle-btn .toggle-icon-expand,
            body.sidebar-collapsed .sidebar-collapse-toggle-btn .toggle-icon-expand {
                display: inline-flex !important;
            }
        }

        .orders-full-layout .sidebar-collapse-toggle-btn {
            display: none !important;
        }

        @media (max-width: 767.98px) {
            .sidebar-collapse-toggle-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body class="dashboard-body {{ request()->routeIs('orders.*') ? 'orders-full-layout' : '' }}">
    <div class="dashboard-shell {{ request()->routeIs('orders.*') ? 'orders-full-shell' : '' }}">
        @include('partials.dashboard.sidebar')
        @if (request()->routeIs('orders.*'))
            <div class="orders-sidebar-backdrop" id="ordersSidebarBackdrop" onclick="window.toggleOrdersSidebar(false)"></div>
        @endif

        <div class="dashboard-main {{ request()->routeIs('orders.*') ? 'orders-full-main' : '' }}">
            @unless (request()->routeIs('orders.*'))
                @include('partials.dashboard.header')
            @endunless

            <main class="dashboard-content {{ request()->routeIs('orders.*') ? 'orders-full-content' : '' }}">
                @yield('content')
            </main>

            @unless (request()->routeIs('orders.*'))
                @include('partials.dashboard.footer')
            @endunless
        </div>
    </div>

    <!-- Script điều khiển thu gọn / mở rộng menu Sidebar -->
    <script>
        (function() {
            function initSidebarToggle() {
                var btn = document.getElementById('sidebarCollapseToggleBtn');
                if (!btn) return;

                function setSidebarState(collapsed) {
                    if (collapsed) {
                        document.documentElement.classList.add('sidebar-collapsed');
                        document.body.classList.add('sidebar-collapsed');
                        btn.setAttribute('title', 'Mở rộng menu (>>)');
                        btn.setAttribute('aria-label', 'Mở rộng menu (>>)');
                        btn.setAttribute('aria-expanded', 'false');
                    } else {
                        document.documentElement.classList.remove('sidebar-collapsed');
                        document.body.classList.remove('sidebar-collapsed');
                        btn.setAttribute('title', 'Thu gọn menu (<<)');
                        btn.setAttribute('aria-label', 'Thu gọn menu (<<)');
                        btn.setAttribute('aria-expanded', 'true');
                    }
                }

                // Đồng bộ trạng thái ban đầu
                var isInitiallyCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                setSidebarState(isInitiallyCollapsed);

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var willBeCollapsed = !document.documentElement.classList.contains('sidebar-collapsed');
                    setSidebarState(willBeCollapsed);
                    try {
                        localStorage.setItem('thl_sidebar_collapsed', willBeCollapsed ? 'true' : 'false');
                    } catch (err) {}
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSidebarToggle);
            } else {
                initSidebarToggle();
            }
        })();
    </script>
</body>
</html>
