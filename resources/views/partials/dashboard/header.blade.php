<header class="dashboard-header">
    <div>
        <p class="dashboard-breadcrumb">Workspace / Overview</p>
        <h1 class="dashboard-title">Xin chào, {{ Auth::user()->name }}</h1>
    </div>

    <div class="dashboard-header-actions">
        <span class="dashboard-date">{{ now()->format('d M Y') }}</span>
        <div class="dropdown">
            <button class="dashboard-user-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="dashboard-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <span class="dashboard-user-name">{{ Auth::user()->name }}</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end dashboard-user-menu">
                <div class="dashboard-user-email">{{ Auth::user()->email }}</div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('dashboard-logout-form').submit();">
                    Đăng xuất
                </a>
                <form id="dashboard-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>
