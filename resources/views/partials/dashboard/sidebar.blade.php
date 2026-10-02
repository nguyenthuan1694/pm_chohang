<aside class="dashboard-sidebar" aria-label="Thanh menu chính">
    <!-- Header: Brand & Logo + Nút thu gọn / mở rộng menu -->
    <div class="sidebar-header-row">
        <a class="dashboard-sidebar-brand" href="{{ route('home') }}" title="Tân Hòa Lợi - Phần Mềm Chở Hàng">
            <div class="thl-sidebar-logo-emblem">
                <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="10" fill="#ffffff"/>
                    <path d="M10 14C10 12.8954 10.8954 12 12 12H23C24.1046 12 25 12.8954 25 14V26C25 27.1046 24.1046 28 23 28H12C10.8954 28 10 27.1046 10 26V14Z" fill="#193a77" fill-opacity="0.18"/>
                    <path d="M12 23L18 15L24 23H12Z" fill="#193a77"/>
                    <path d="M22 17H28C29.1046 17 30 17.8954 30 19V26C30 27.1046 29.1046 28 28 28H24V19C24 17.8954 23.1046 17 22 17Z" fill="#193a77" fill-opacity="0.9"/>
                    <circle cx="16" cy="28" r="2.5" fill="#193a77"/>
                    <circle cx="26" cy="28" r="2.5" fill="#193a77"/>
                </svg>
            </div>
            <div class="thl-sidebar-brand-text">
                <span class="thl-sidebar-brand-name">TÂN HÒA LỢI</span>
                <span class="thl-sidebar-brand-sub">PHẦN MỀM CHỞ HÀNG</span>
            </div>
        </a>

        <!-- Nút thu gọn / mở rộng menu (<< và >>) -->
        <button type="button" class="sidebar-collapse-toggle-btn" id="sidebarCollapseToggleBtn" title="Thu gọn menu (&laquo;)" aria-label="Thu gọn hoặc mở rộng thanh menu">
            <span class="toggle-icon-collapse" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="11 17 6 12 11 7"></polyline>
                    <polyline points="18 17 13 12 18 7"></polyline>
                </svg>
            </span>
            <span class="toggle-icon-expand" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="13 17 18 12 13 7"></polyline>
                    <polyline points="6 17 11 12 6 7"></polyline>
                </svg>
            </span>
        </button>
    </div>

    <!-- Menu Section: Workspace -->
    <div class="sidebar-section-label">WORKSPACE</div>
    <nav class="dashboard-nav">
        <a class="dashboard-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" title="Dashboard">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </span>
            <span class="dashboard-nav-label">Dashboard</span>
        </a>

        @if (auth()->user()?->canModule('kilometers'))
        <a class="dashboard-nav-link {{ request()->routeIs('kilometers.*') ? 'active' : '' }}" href="{{ route('kilometers.index') }}" title="DS Kilomet">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                </svg>
            </span>
            <span class="dashboard-nav-label">DS Kilomet</span>
        </a>
        @endif

        @if (auth()->user()?->canModule('cargo-deliveries'))
        <a class="dashboard-nav-link {{ request()->routeIs('cargo-deliveries.*') ? 'active' : '' }}" href="{{ route('cargo-deliveries.index') }}" title="DS TT Chở Hàng">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </span>
            <span class="dashboard-nav-label">DS TT Chở Hàng</span>
        </a>
        @endif

        @if (auth()->user()?->canModule('orders'))
        <a class="dashboard-nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}" title="DS Đơn Hàng">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </span>
            <span class="dashboard-nav-label">DS Đơn Hàng</span>
        </a>
        @endif

        @if (auth()->user()?->canModule('groups'))
        <a class="dashboard-nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}" href="{{ route('groups.index') }}" title="DS Nhóm Kinh Doanh">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <span class="dashboard-nav-label">DS Nhóm Kinh Doanh</span>
        </a>
        @endif

        @if (auth()->user()?->canModule('employees'))
        <a class="dashboard-nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}" title="DS Nhân Viên">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </span>
            <span class="dashboard-nav-label">DS Nhân Viên</span>
        </a>
        @endif

        @if (auth()->user()?->canModule('billing'))
        <a class="dashboard-nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.index') }}" title="DS Tính Kilomet">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </span>
            <span class="dashboard-nav-label">DS Tính Kilomet</span>
        </a>
        @endif

        @if (auth()->user()?->canModule('permissions'))
        <a class="dashboard-nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}" title="Phân quyền">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </span>
            <span class="dashboard-nav-label">Phân quyền</span>
        </a>
        @endif

        @if (auth()->user()?->isAdmin())
        <a class="dashboard-nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}" title="Nhật ký hoạt động">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </span>
            <span class="dashboard-nav-label">Nhật ký hoạt động</span>
        </a>
        @endif
    </nav>

    <!-- Menu Section: System & Settings -->
    <div class="sidebar-section-label sidebar-section-label-bottom">HỆ THỐNG</div>
    <nav class="dashboard-nav">
        <a class="dashboard-nav-link" href="#settings" title="Cài đặt">
            <span class="dashboard-nav-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </span>
            <span class="dashboard-nav-label">Cài đặt</span>
        </a>
    </nav>

    <!-- Bottom Status Card -->
    <div class="sidebar-quote">
        <div class="sidebar-quote-status">
            <span class="status-live-dot"></span>
            <span>Hệ thống trực tuyến 24/7</span>
        </div>
        <p>Tân Hòa Lợi — Đồng hành trên từng chuyến hàng.</p>
    </div>
</aside>
