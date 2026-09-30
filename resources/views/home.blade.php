@extends('layouts.dashboard')

@section('title', 'Dashboard | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview">
    @if (session('status'))
        <div class="alert alert-success dashboard-alert" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <section class="dashboard-welcome">
        <div>
            <span class="panel-kicker">Wednesday focus</span>
            <h2>Không gian làm việc của bạn.</h2>
            <p>Theo dõi tiến độ và giữ mọi việc quan trọng trong tầm mắt.</p>
        </div>
        <span class="dashboard-welcome-mark">01</span>
    </section>

    <section class="dashboard-stat-grid" aria-label="Tổng quan công việc">
        <article class="dashboard-stat-card stat-card-mint">
            <span class="dashboard-stat-label">Dự án đang chạy</span>
            <strong>08</strong>
            <span class="dashboard-stat-change">+2 trong tháng này</span>
        </article>
        <article class="dashboard-stat-card stat-card-white">
            <span class="dashboard-stat-label">Công việc hoàn thành</span>
            <strong>64</strong>
            <span class="dashboard-stat-change">+12% so với tuần trước</span>
        </article>
        <article class="dashboard-stat-card stat-card-coral">
            <span class="dashboard-stat-label">Việc cần chú ý</span>
            <strong>05</strong>
            <span class="dashboard-stat-change">Cần xử lý hôm nay</span>
        </article>
    </section>

    <section class="dashboard-lower-grid">
        <article class="dashboard-panel">
            <div class="dashboard-panel-heading">
                <div>
                    <span class="panel-kicker">Activity</span>
                    <h3>Hoạt động gần đây</h3>
                </div>
                @if (auth()->user()?->isAdmin())
                    <a href="{{ route('activity-logs.index') }}" class="dashboard-panel-link">Xem tất cả</a>
                @endif
            </div>

            @if (auth()->user()?->isAdmin())
                <div class="activity-list">
                    @forelse ($activities as $activity)
                        <div class="activity-item">
                            <span class="activity-dot {{ $activity->action_dot_class }}"></span>
                            <div>
                                <strong>{{ $activity->description }}</strong>
                                <span>
                                    <span class="activity-user-badge">{{ $activity->user?->name ?? $activity->user_name }}</span> · 
                                    <span class="activity-module-badge">{{ $activity->module_label }}</span> · 
                                    <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->format('d/m/Y H:i') }} ({{ $activity->created_at->diffForHumans() }})</time>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="activity-empty-state" style="padding: 1.5rem 0; text-align: center; color: var(--muted); font-size: .85rem;">
                            Chưa có hoạt động nào được ghi nhận.
                        </div>
                    @endforelse
                </div>
            @else
                <div class="activity-restricted-notice" style="display: flex; align-items: center; gap: .75rem; padding: 1.25rem; background: #f8faf8; border: 1px dashed #d1ded4; border-radius: 8px; color: var(--muted); font-size: .82rem; font-weight: 600;">
                    <span style="font-size: 1.2rem;">🔒</span>
                    <span>Chỉ Quản trị viên (Admin) mới có quyền xem danh sách nhật ký hoạt động.</span>
                </div>
            @endif
        </article>

        <article class="dashboard-panel dashboard-progress-panel">
            <span class="panel-kicker">This week</span>
            <h3>Tiến độ tuần</h3>
            <div class="progress-ring"><strong>72%</strong><span>hoàn thành</span></div>
            <p>Bạn đang đi đúng hướng. Tiếp tục nhịp làm việc hiện tại.</p>
        </article>
    </section>
</div>

<style>
.activity-dot-danger { background: #dc2626 !important; }
.activity-dot-purple { background: #9333ea !important; }
.activity-user-badge {
    font-weight: 700;
    color: var(--mint-deep);
}
.activity-module-badge {
    display: inline-block;
    padding: .1rem .4rem;
    font-size: .72rem;
    font-weight: 700;
    background: #edf3ee;
    border-radius: 4px;
    color: #244b37;
}
.activity-item strong {
    font-size: .88rem;
    color: var(--ink);
    line-height: 1.35;
    margin-bottom: .2rem;
    display: block;
}
.activity-item span {
    font-size: .78rem;
    color: var(--muted);
}
</style>
@endsection
