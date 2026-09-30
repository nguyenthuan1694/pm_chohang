@extends('layouts.dashboard')

@section('title', 'DS Đơn hàng | ' . config('app.name', 'Laravel'))

@section('content')
<div class="order-page-pure">
    <!-- TIÊU ĐỀ VÀ SỐ ĐƠN (CHỈ GIỮ LẠI THEO YÊU CẦU) -->
    <div class="order-pure-heading-row">
        <div class="order-heading-left">
            <button type="button" class="btn order-menu-toggle-btn" onclick="window.toggleOrdersSidebar()" title="Đóng / Mở menu điều hướng">
                <span class="menu-icon" aria-hidden="true">☰</span>
                <span class="menu-text">Menu</span>
            </button>
            <h1 class="order-pure-title">DANH SÁCH ĐƠN HÀNG</h1>
        </div>
        <div class="order-heading-right">
            <div class="order-live-indicator" title="Dữ liệu tự động cập nhật liên tục">
                <span class="live-pulse-dot"></span>
                <span>Tự động cập nhật: <strong>10s</strong></span>
            </div>
            <div class="order-pure-count-badge">
                <span class="count-number">{{ $orders->total() }}</span>
                <span class="count-label">đơn hàng</span>
            </div>
        </div>
    </div>

    <!-- LIST DANH SÁCH ĐƠN HÀNG (FULL MÀN HÌNH - SIZE CHỮ 22PX) -->
    <section class="kilometer-table-card order-table-stage-pure">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table order-table order-table-large">
                <thead>
                    <tr>
                        <th>T.GIAN TẠO ĐƠN</th>
                        <th>TÊN XUẤT PHIẾU</th>
                        <th>NHÓM</th>
                        <th>TÌNH TRẠNG ĐƠN HÀNG</th>
                        <th>T.GIAN GIAO HÀNG</th>
                        <th>TÊN CHỞ HÀNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="date-cell">{{ $order->group?->group_date ? $order->group->group_date->format('d/m/Y H:i') : ($order->created_at ? $order->created_at->format('d/m/Y H:i') : '—') }}</td>
                            <td><strong class="order-invoice-name">{{ $order->invoice_name }}</strong></td>
                            <td><span class="group-badge order-group-badge">{{ $order->group_name }}</span></td>
                            <td><span class="order-status order-status-large status-{{ $order->delivery_status }}">{{ $order->delivery_status_label }}</span></td>
                            <td class="date-cell">{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y H:i') : '—' }}</td>
                            <td class="order-employee-name">{{ $order->employee?->name ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-table-cell empty-table-cell-large">
                                <strong>Chưa có đơn hàng</strong>
                                <span>Dữ liệu sẽ hiển thị khi có thông tin chở hàng được tạo.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="kilometer-pagination order-pagination-large">{{ $orders->links() }}</div>
        @endif
    </section>
</div>

<script>
    // Tự động load trang mỗi 10 giây
    (function() {
        const RELOAD_INTERVAL_MS = 10000;
        setInterval(function() {
            const sidebar = document.querySelector('.dashboard-sidebar');
            // Nếu người dùng đang mở menu sidebar thì tạm hoãn reload để không gián đoạn
            if (sidebar && sidebar.classList.contains('sidebar-open')) {
                return;
            }
            window.location.reload();
        }, RELOAD_INTERVAL_MS);
    })();

    window.toggleOrdersSidebar = function(forceState) {
        const sidebar = document.querySelector('.dashboard-sidebar');
        const backdrop = document.getElementById('ordersSidebarBackdrop');
        if (!sidebar) return;
        
        const isOpen = sidebar.classList.contains('sidebar-open');
        const nextState = typeof forceState === 'boolean' ? forceState : !isOpen;
        
        if (nextState) {
            sidebar.classList.add('sidebar-open');
            if (backdrop) backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('sidebar-open');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    };
</script>
@endsection
