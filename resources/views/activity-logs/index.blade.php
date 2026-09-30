@extends('layouts.dashboard')

@section('title', 'Nhật ký hoạt động | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview activity-logs-page">
    <div class="page-heading-row">
        <div>
            <p class="dashboard-breadcrumb">Workspace / Nhật ký hoạt động</p>
            <h1 class="page-heading">Nhật ký hoạt động</h1>
            <p class="page-subtitle">Theo dõi chi tiết tất cả thao tác của người dùng trên hệ thống theo thời gian thực.</p>
        </div>
        <div class="activity-logs-badge-wrap">
            <span class="activity-logs-total-badge">
                Tổng cộng: <strong>{{ number_format($logs->total(), 0, ',', '.') }}</strong> nhật ký
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <section class="activity-filter-card">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="activity-filter-form">
            <div class="activity-filter-field activity-filter-search">
                <label for="filter-search">Tìm kiếm</label>
                <input id="filter-search" type="search" name="search" value="{{ $search }}" placeholder="Tìm theo nội dung, người dùng, IP...">
            </div>

            <div class="activity-filter-field">
                <label for="filter-module">Chức năng</label>
                <select id="filter-module" name="module">
                    <option value="">Tất cả chức năng</option>
                    @foreach ($modules as $modKey => $modLabel)
                        <option value="{{ $modKey }}" @selected($module === $modKey)>{{ $modLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="activity-filter-field">
                <label for="filter-action">Hành động</label>
                <select id="filter-action" name="action">
                    <option value="">Tất cả hành động</option>
                    @foreach ($actions as $actKey => $actLabel)
                        <option value="{{ $actKey }}" @selected($action === $actKey)>{{ $actLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="activity-filter-field">
                <label for="filter-user">Người thực hiện</label>
                <select id="filter-user" name="user_id">
                    <option value="">Tất cả người dùng</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected((string)$userId === (string)$u->id)>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>

            <div class="activity-filter-field">
                <label for="filter-from">Từ ngày</label>
                <input id="filter-from" type="date" name="from_date" value="{{ $fromDate }}">
            </div>

            <div class="activity-filter-field">
                <label for="filter-to">Đến ngày</label>
                <input id="filter-to" type="date" name="to_date" value="{{ $toDate }}">
            </div>

            <div class="activity-filter-actions">
                <button type="submit" class="btn activity-search-btn">Lọc nhật ký</button>
                @if ($search !== '' || $module !== '' || $action !== '' || $userId !== '' || $fromDate !== '' || $toDate !== '')
                    <a href="{{ route('activity-logs.index') }}" class="btn activity-reset-btn">Đặt lại</a>
                @endif
            </div>
        </form>
    </section>

    <!-- Table Card -->
    <section class="kilometer-table-card">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table activity-table">
                <thead>
                    <tr>
                        <th style="width: 170px;">THỜI GIAN</th>
                        <th style="width: 180px;">NGƯỜI THỰC HIỆN</th>
                        <th style="width: 130px;">CHỨC NĂNG</th>
                        <th style="width: 130px;">HÀNH ĐỘNG</th>
                        <th>NỘI DUNG CHI TIẾT THAY ĐỔI</th>
                        <th style="width: 120px;">ĐỊA CHỈ IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="date-cell">
                                <span class="activity-time-main">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                <small class="table-subtext">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <strong class="activity-user-name">{{ $log->user?->name ?? $log->user_name }}</strong>
                                <small class="table-subtext">
                                    @if ($log->user)
                                        {{ $log->user->email }} ({{ $log->user->role }})
                                    @else
                                        {{ $log->user_name }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span class="activity-module-tag">{{ $log->module_label }}</span>
                            </td>
                            <td>
                                <span class="activity-action-tag {{ $log->action_badge_class }}">
                                    {{ $log->action_label }}
                                </span>
                            </td>
                            <td class="activity-desc-cell">
                                <span class="activity-desc-text" title="{{ $log->description }}">{{ $log->description }}</span>
                            </td>
                            <td class="activity-ip-cell">
                                <code>{{ $log->ip_address ?: '—' }}</code>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-table-cell">
                                <strong>Chưa có nhật ký hoạt động nào</strong>
                                <span>{{ ($search !== '' || $module !== '' || $action !== '' || $userId !== '' || $fromDate !== '' || $toDate !== '') ? 'Không tìm thấy nhật ký phù hợp với bộ lọc.' : 'Nhật ký sẽ tự động ghi lại khi người dùng thao tác.' }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="kilometer-pagination">
                {{ $logs->links() }}
            </div>
        @endif
    </section>
</div>

<style>
.activity-logs-total-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .5rem 1rem;
    background: #eaf5ec;
    border: 1px solid #cde4d3;
    border-radius: 8px;
    font-size: .82rem;
    color: var(--mint-deep);
}
.activity-logs-total-badge strong {
    font-size: 1rem;
    font-weight: 800;
}
.activity-filter-card {
    margin-bottom: 1.25rem;
    padding: 1.25rem 1.5rem;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(23, 34, 31, .03);
}
.activity-filter-form {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 1rem;
}
.activity-filter-field {
    display: flex;
    flex-direction: column;
    gap: .35rem;
    min-width: 150px;
}
.activity-filter-search {
    flex: 1;
    min-width: 220px;
}
.activity-filter-field label {
    color: var(--ink);
    font-size: .75rem;
    font-weight: 700;
}
.activity-filter-field input,
.activity-filter-field select {
    width: 100%;
    padding: .55rem .75rem;
    border: 1px solid var(--line);
    border-radius: 6px;
    font-size: .8rem;
    color: var(--ink);
    background: #fff;
    outline: 0;
}
.activity-filter-field input:focus,
.activity-filter-field select:focus {
    border-color: var(--mint-deep);
    box-shadow: 0 0 0 .15rem rgba(36, 107, 75, .12);
}
.activity-filter-actions {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-left: auto;
}
.activity-search-btn {
    padding: .55rem 1.25rem;
    color: #fff;
    background: var(--mint-deep);
    border: 1px solid var(--mint-deep);
    border-radius: 6px;
    font-size: .8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s ease;
}
.activity-search-btn:hover {
    background: var(--ink);
    border-color: var(--ink);
    color: #fff;
}
.activity-reset-btn {
    padding: .55rem .95rem;
    color: var(--muted);
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 6px;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s ease;
}
.activity-reset-btn:hover {
    background: #f0f3ef;
    color: var(--ink);
}
.activity-table {
    min-width: 1100px;
}
.activity-time-main {
    font-weight: 700;
    color: var(--ink);
}
.activity-user-name {
    font-weight: 800;
    color: var(--ink);
}
.activity-module-tag {
    display: inline-block;
    padding: .25rem .55rem;
    border-radius: 5px;
    font-size: .72rem;
    font-weight: 700;
    color: #2b4366;
    background: #e6eef8;
    border: 1px solid #c8d8ec;
}
.activity-action-tag {
    display: inline-block;
    padding: .25rem .55rem;
    border-radius: 5px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.badge-action-create {
    color: #166534;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
}
.badge-action-update {
    color: #1e40af;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
}
.badge-action-status {
    color: #854d0e;
    background: #fef9c3;
    border: 1px solid #fef08a;
}
.badge-action-delete {
    color: #991b1b;
    background: #fee2e2;
    border: 1px solid #fecaca;
}
.badge-action-login {
    color: #581c87;
    background: #f3e8ff;
    border: 1px solid #e9d5ff;
}
.badge-action-default {
    color: var(--muted);
    background: #f1f5f9;
}
.activity-desc-cell {
    white-space: normal !important;
    min-width: 300px;
    max-width: 480px;
    line-height: 1.45;
}
.activity-desc-text {
    font-size: .8rem;
    color: #17221f;
    font-weight: 600;
}
.activity-ip-cell code {
    font-size: .75rem;
    color: #64748b;
    background: #f8fafc;
    padding: .2rem .4rem;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}
@media (max-width: 767.98px) {
    .activity-filter-form {
        flex-direction: column;
        align-items: stretch;
    }
    .activity-filter-field,
    .activity-filter-search {
        width: 100%;
        min-width: 0;
    }
    .activity-filter-actions {
        margin-left: 0;
        justify-content: flex-end;
    }
}
</style>
@endsection
