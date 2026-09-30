@extends('layouts.dashboard')

@section('title', 'DS Tính Tiền | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview billing-page">
    <div class="page-heading-row">
        <div>
            <p class="dashboard-breadcrumb">Workspace / Billing</p>
            <h1 class="page-heading">Danh sách tính tiền</h1>
            <p class="page-subtitle">Bảng kê tính cước chành xe và số kilomet giao hàng theo nhân viên từ DS chở hàng đã giao.</p>
        </div>
        <div class="billing-heading-actions">
            @if(count($dailySummaries) > 0)
                <div class="btn-group">
                    <a href="{{ route('billing.export', array_merge(request()->query(), ['mode' => 'standard'])) }}" class="btn btn-success btn-sm export-btn" title="Xuất file Excel chuẩn theo BANG TINH CHANH VA KILOMET (Gồm Sheet GỐC và Sheet riêng cho từng nhân viên)">
                        <span aria-hidden="true">📊</span> Xuất Excel (Bảng Tính Chành & KM)
                    </a>
                    <a href="{{ route('billing.export', array_merge(request()->query(), ['mode' => 'daily'])) }}" class="btn btn-outline-success btn-sm export-btn" title="Xuất file Excel tổng hợp theo ngày">
                        <span aria-hidden="true">📋</span> Xuất Excel (Theo ngày)
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- BỘ LỌC TÌM KIẾM -->
    <section class="billing-filter-card">
        <form method="GET" action="{{ route('billing.index') }}" class="billing-filter-form">
            <div class="filter-field">
                <label for="from_date">Từ ngày</label>
                <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" class="form-control form-control-sm">
            </div>

            <div class="filter-field">
                <label for="to_date">Đến ngày</label>
                <input type="date" id="to_date" name="to_date" value="{{ $toDate }}" class="form-control form-control-sm">
            </div>

            <div class="filter-field filter-field-employee">
                <label for="employee_id">Nhân viên chở hàng</label>
                <select id="employee_id" name="employee_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả nhân viên --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" @selected((string)$employeeId === (string)$emp->id)>
                            {{ $emp->name }}{{ $emp->is_support ? ' (Hỗ trợ)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="view_mode" id="view_mode_input" value="{{ $viewMode }}">

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary btn-sm billing-search-btn">
                    <span aria-hidden="true">🔍</span> Tìm kiếm
                </button>
                @if ($fromDate || $toDate || $employeeId)
                    <a href="{{ route('billing.index') }}" class="btn btn-light btn-sm billing-reset-btn">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- THẺ TỔNG HỢP / STATS CARDS -->
    <div class="billing-stats-grid">
        <div class="billing-stat-card">
            <span class="stat-label">Số ngày chở hàng</span>
            <strong class="stat-value">{{ number_format($totals['total_days']) }}</strong>
            <span class="stat-desc">
                @if ($selectedEmployee)
                    {{ $selectedEmployee->name }}
                @elseif (!empty($isToday))
                    Hôm nay ({{ now()->format('d/m/Y') }})
                @else
                    Toàn bộ nhân viên
                @endif
            </span>
        </div>
        <div class="billing-stat-card">
            <span class="stat-label">Tổng số chuyến</span>
            <strong class="stat-value">{{ number_format($totals['total_trips']) }}</strong>
            <span class="stat-desc">{{ number_format($totals['total_orders']) }} điểm giao</span>
        </div>
        <div class="billing-stat-card">
            <span class="stat-label">Tổng số kg</span>
            <strong class="stat-value">{{ number_format($totals['total_weight_kg'], 1, ',', '.') }}</strong>
            <span class="stat-desc">Khối lượng hàng</span>
        </div>
        <div class="billing-stat-card stat-card-highlight">
            <span class="stat-label">Tổng KM tính tiền</span>
            <strong class="stat-value">{{ number_format($totals['total_distance_km'], 2, ',', '.') }}</strong>
            <span class="stat-desc">
                @if ($selectedEmployee)
                    {{ $selectedEmployee->is_support ? 'Nhân viên hỗ trợ (Không trừ 2km/chuyến)' : 'Đã trừ 2km đầu / chuyến' }}
                @else
                    Hỗ trợ: Không trừ, Thường: Trừ 2km
                @endif
            </span>
        </div>
        <div class="billing-stat-card stat-card-money">
            <span class="stat-label">Tổng tiền chành</span>
            <strong class="stat-value">{{ number_format($totals['total_carrier_fee'], 0, ',', '.') }} đ</strong>
            <span class="stat-desc">Cước chành xe thực tính</span>
        </div>
    </div>

    <!-- TABS CHỌN CHẾ ĐỘ XEM -->
    <div class="billing-tab-header">
        <ul class="nav nav-pills" id="billingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $viewMode === 'daily' ? 'active' : '' }}" id="daily-tab" data-bs-toggle="pill" data-bs-target="#daily-content" type="button" role="tab" aria-controls="daily-content" aria-selected="{{ $viewMode === 'daily' ? 'true' : 'false' }}" onclick="document.getElementById('view_mode_input').value='daily'">
                    📋 Danh Sách Tính Tiền Theo Ngày ({{ count($dailySummaries) }} ngày)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $viewMode === 'detail' ? 'active' : '' }}" id="detail-tab" data-bs-toggle="pill" data-bs-target="#detail-content" type="button" role="tab" aria-controls="detail-content" aria-selected="{{ $viewMode === 'detail' ? 'true' : 'false' }}" onclick="document.getElementById('view_mode_input').value='detail'">
                    📑 Chi Tiết Từng Điểm Giao ({{ count($allDetails) }} đơn)
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="billingTabContent">
        <!-- TAB 1: BẢNG TỔNG HỢP THEO NGÀY (Mỗi ngày 1 dòng) -->
        <div class="tab-pane fade {{ $viewMode === 'daily' ? 'show active' : '' }}" id="daily-content" role="tabpanel" aria-labelledby="daily-tab">
            <section class="kilometer-table-card">
                <div class="kilometer-table-wrap">
                    <table class="kilometer-table billing-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">STT</th>
                                <th>NGÀY</th>
                                <th>TÊN CHỞ HÀNG</th>
                                <th class="number-cell">SỐ CHUYẾN</th>
                                <th class="number-cell">SỐ ĐIỂM GIAO</th>
                                <th>SỐ BAO</th>
                                <th class="number-cell">SỐ KG</th>
                                <th class="money-cell">TIỀN CHÀNH</th>
                                <th class="number-cell">TỔNG KM TÍNH TIỀN</th>
                                <th>XE ÔM</th>
                                <th style="text-align: center;">CHI TIẾT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dailySummaries as $index => $day)
                                <tr class="daily-summary-row" data-bs-toggle="collapse" data-bs-target="#collapse-day-{{ $index }}" aria-expanded="false" style="cursor: pointer;">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="date-cell">
                                        <strong>{{ $day->date_formatted }}</strong>
                                    </td>
                                    <td>
                                        <span class="employee-badge">{{ $day->employee_name }}</span>
                                        @if ($day->is_support)
                                            <span class="badge bg-warning text-dark ms-1" title="Nhân viên hỗ trợ - Không trừ 2km đầu mỗi chuyến" style="font-size: 0.68rem; vertical-align: middle;">Hỗ trợ</span>
                                        @endif
                                    </td>
                                    <td class="number-cell">
                                        <span class="badge bg-light text-dark border">{{ $day->trip_count }} chuyến</span>
                                    </td>
                                    <td class="number-cell">{{ $day->order_count }}</td>
                                    <td>{{ $day->package_count_text }}</td>
                                    <td class="number-cell">{{ number_format($day->total_weight_kg, 2, ',', '.') }}</td>
                                    <td class="money-cell">
                                        <strong>{{ number_format($day->total_carrier_fee, 0, ',', '.') }} đ</strong>
                                    </td>
                                    <td class="number-cell">
                                        <strong class="text-success">{{ number_format($day->total_distance_km, 2, ',', '.') }} km</strong>
                                    </td>
                                    <td>{{ $day->motorbike_driver }}</td>
                                    <td style="text-align: center;">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 toggle-detail-btn" title="Bấm để xem các đơn chi tiết">
                                            Chi tiết ▼
                                        </button>
                                    </td>
                                </tr>
                                <!-- DÒNG CHI TIẾT CỦA NGÀY -->
                                <tr class="collapse-row">
                                    <td colspan="11" class="p-0 border-0">
                                        <div class="collapse" id="collapse-day-{{ $index }}">
                                            <div class="daily-items-nested-table p-3 bg-light">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 text-muted">
                                                        Chi tiết các đơn ngày <strong>{{ $day->date_formatted }}</strong> ({{ $day->order_count }} điểm giao - {{ $day->trip_count }} chuyến)
                                                        @if ($day->is_support)
                                                            <span class="badge bg-warning text-dark ms-2">Nhân viên hỗ trợ - Không trừ 2km</span>
                                                        @else
                                                            <span class="badge bg-light text-secondary border ms-2">Đã trừ 2km đầu / chuyến</span>
                                                        @endif
                                                    </h6>
                                                </div>
                                                <div class="table-responsive bg-white rounded border">
                                                    <table class="table table-sm table-hover mb-0 nested-table">
                                                        <thead>
                                                            <tr class="table-secondary">
                                                                <th>Chuyến</th>
                                                                <th>Nhóm</th>
                                                                <th>Chành xe - Cửa hàng</th>
                                                                <th>Địa chỉ</th>
                                                                <th>Phường / Quận</th>
                                                                <th class="text-end">KM gốc</th>
                                                                <th class="text-end">Tiền chành gốc</th>
                                                                <th>Số bao</th>
                                                                <th class="text-end">Số kg</th>
                                                                <th class="text-end text-primary">Tiền chành tính</th>
                                                                <th class="text-end text-success">KM tính</th>
                                                                <th>Xe ôm</th>
                                                                <th>Ghi chú</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($day->items as $item)
                                                                <tr>
                                                                    <td><span class="badge bg-secondary">Chuyến {{ $item->trip_count }}</span></td>
                                                                    <td><span class="group-badge">{{ $item->group_name }}</span></td>
                                                                    <td><strong>{{ $item->invoice_name }}</strong></td>
                                                                    <td>{{ $item->address }}</td>
                                                                    <td>{{ $item->ward }}, {{ $item->district }}</td>
                                                                    <td class="text-end text-muted">{{ number_format((float) $item->distance_km, 2, ',', '.') }}</td>
                                                                    <td class="text-end text-muted">{{ number_format((float) $item->carrier_fee, 0, ',', '.') }} đ</td>
                                                                    <td>{{ $item->package_count }}</td>
                                                                    <td class="text-end">{{ number_format((float) $item->weight_kg, 2, ',', '.') }}</td>
                                                                    <td class="text-end font-weight-bold text-primary">{{ number_format((float) $item->calculated_carrier_fee, 0, ',', '.') }} đ</td>
                                                                    <td class="text-end font-weight-bold text-success">{{ number_format((float) $item->calculated_km, 2, ',', '.') }} km</td>
                                                                    <td>{{ $item->motorbike_driver ?: '—' }}</td>
                                                                    <td><small class="text-muted">{{ $item->package_note ?: '—' }}</small></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="empty-table-cell">
                                        <strong>Không có dữ liệu tính tiền</strong>
                                        <span>
                                            @if ($hasSearched)
                                                Không tìm thấy thông tin chở hàng nào ở trạng thái "Đã giao" trong khoảng thời gian đã chọn.
                                            @else
                                                Không có thông tin chở hàng nào ở trạng thái "Đã giao" trong ngày hôm nay ({{ now()->format('d/m/Y') }}).
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (count($dailySummaries) > 0)
                            <tfoot>
                                <tr class="table-total-row">
                                    <th colspan="3" class="text-center">TỔNG CỘNG ({{ $totals['total_days'] }} ngày)</th>
                                    <th class="number-cell">{{ $totals['total_trips'] }} chuyến</th>
                                    <th class="number-cell">{{ $totals['total_orders'] }} đơn</th>
                                    <th>—</th>
                                    <th class="number-cell">{{ number_format($totals['total_weight_kg'], 2, ',', '.') }}</th>
                                    <th class="money-cell">{{ number_format($totals['total_carrier_fee'], 0, ',', '.') }} đ</th>
                                    <th class="number-cell text-success">{{ number_format($totals['total_distance_km'], 2, ',', '.') }} km</th>
                                    <th>—</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </section>
        </div>

        <!-- TAB 2: CHI TIẾT TỪNG ĐIỂM GIAO (16 Cột chuẩn Excel) -->
        <div class="tab-pane fade {{ $viewMode === 'detail' ? 'show active' : '' }}" id="detail-content" role="tabpanel" aria-labelledby="detail-tab">
            <section class="kilometer-table-card">
                <div class="kilometer-table-wrap">
                    <table class="kilometer-table billing-table-detail">
                        <thead>
                            <tr>
                                <th>NGÀY</th>
                                <th>NHÓM</th>
                                <th>CHÀNH XE - CỬA HÀNG</th>
                                <th>ĐỊA CHỈ</th>
                                <th>PHƯỜNG</th>
                                <th>QUẬN</th>
                                <th class="number-cell">KM (GỐC)</th>
                                <th class="money-cell">TIỀN CHÀNH (GỐC)</th>
                                <th>TÊN CHỞ HÀNG</th>
                                <th class="number-cell">SỐ CHUYẾN</th>
                                <th class="number-cell">SỐ BAO</th>
                                <th class="number-cell">SỐ KG</th>
                                <th class="money-cell">TIỀN CHÀNH (TÍNH)</th>
                                <th class="number-cell">TỔNG KM (TÍNH)</th>
                                <th>XE ÔM</th>
                                <th>THÔNG TIN GHI BAO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allDetails as $item)
                                <tr>
                                    <td class="date-cell">{{ $item->delivery_date ? $item->delivery_date->format('d/m/Y') : '—' }}</td>
                                    <td><span class="group-badge">{{ $item->group_name }}</span></td>
                                    <td><strong>{{ $item->invoice_name }}</strong></td>
                                    <td class="address-cell" title="{{ $item->address }}">{{ $item->address }}</td>
                                    <td>{{ $item->ward }}</td>
                                    <td>{{ $item->district }}</td>
                                    <td class="number-cell text-muted">{{ number_format((float) $item->distance_km, 2, ',', '.') }}</td>
                                    <td class="money-cell text-muted">{{ number_format((float) $item->carrier_fee, 0, ',', '.') }} đ</td>
                                    <td>
                                        {{ $item->employee?->name ?? '—' }}
                                        @if ($item->is_support ?? false)
                                            <span class="badge bg-warning text-dark ms-1" title="Nhân viên hỗ trợ - Không trừ 2km đầu" style="font-size: 0.68rem; vertical-align: middle;">Hỗ trợ</span>
                                        @endif
                                    </td>
                                    <td class="number-cell">{{ $item->trip_count }}</td>
                                    <td class="number-cell">{{ $item->package_count }}</td>
                                    <td class="number-cell">{{ number_format((float) $item->weight_kg, 2, ',', '.') }}</td>
                                    <td class="money-cell">
                                        <strong>{{ number_format((float) $item->calculated_carrier_fee, 0, ',', '.') }} đ</strong>
                                    </td>
                                    <td class="number-cell">
                                        <strong class="text-success">{{ number_format((float) $item->calculated_km, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>{{ $item->motorbike_driver ?: '—' }}</td>
                                    <td class="note-cell" title="{{ $item->package_note }}">{{ $item->package_note ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="empty-table-cell">
                                        <strong>Chưa có thông tin chở hàng đã giao</strong>
                                        <span>Không tìm thấy dữ liệu phù hợp với điều kiện tìm kiếm.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (count($allDetails) > 0)
                            <tfoot>
                                <tr class="table-total-row">
                                    <th colspan="6" class="text-center">TỔNG CỘNG ({{ count($allDetails) }} điểm giao)</th>
                                    <th class="number-cell">{{ number_format((float) collect($allDetails)->sum('distance_km'), 2, ',', '.') }}</th>
                                    <th class="money-cell">{{ number_format((float) collect($allDetails)->sum('carrier_fee'), 0, ',', '.') }} đ</th>
                                    <th>—</th>
                                    <th class="number-cell">{{ $totals['total_trips'] }} chuyến</th>
                                    <th>—</th>
                                    <th class="number-cell">{{ number_format($totals['total_weight_kg'], 2, ',', '.') }}</th>
                                    <th class="money-cell">{{ number_format($totals['total_carrier_fee'], 0, ',', '.') }} đ</th>
                                    <th class="number-cell text-success">{{ number_format($totals['total_distance_km'], 2, ',', '.') }} km</th>
                                    <th>—</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
