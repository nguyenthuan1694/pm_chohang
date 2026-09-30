@extends('layouts.dashboard')

@section('title', 'DS Kilomet | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview kilometer-page">
    @if (session('status'))
        <div class="alert alert-success dashboard-alert" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger dashboard-alert" role="alert">
            <ul class="mb-0" style="padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-heading-row">
        <div>
            <p class="dashboard-breadcrumb">Workspace / Data</p>
            <h1 class="page-heading">Danh sách kilomet</h1>
            <p class="page-subtitle">Theo dõi quãng đường và thông tin giao nhận.</p>
        </div>
        
        <div class="page-heading-actions">
            @if (auth()->user()->canModule('kilometers', 'create'))
                <!-- <button type="button" class="btn kilometer-import-button" data-bs-toggle="modal" data-bs-target="#importKilometerModal">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Import Excel
                </button> -->
                <button type="button" class="btn kilometer-add-button" data-bs-toggle="modal" data-bs-target="#createKilometerModal">
                    <span aria-hidden="true">+</span> Thêm kilomet
                </button>
            @endif
        </div>
    </div>

    <section class="kilometer-toolbar">
        <form method="GET" action="{{ route('kilometers.index') }}" class="kilometer-search-form">
            @if (auth()->user()->isAdmin() && !empty($groups))
                <select name="group_id" class="kilometer-group-filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả các nhóm</option>
                    @foreach ($groups as $g)
                        <option value="{{ $g['id'] }}" @selected((string)$selectedGroupId === (string)$g['id'])>{{ $g['name'] }}</option>
                    @endforeach
                </select>
            @endif
            <label class="visually-hidden" for="kilometer-search">Tìm kiếm kilomet</label>
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input id="kilometer-search" name="search" type="search" value="{{ $search }}" placeholder="Tìm theo địa chỉ, phường, quận, ghi bao...">
            @if ($search !== '' || $selectedGroupId)
                <a class="clear-search" href="{{ route('kilometers.index') }}" aria-label="Xóa tìm kiếm">×</a>
            @endif
            <button type="submit" class="kilometer-search-button">Tìm kiếm</button>
        </form>
        <span class="kilometer-updated">Cập nhật {{ now()->format('d/m/Y') }}</span>
    </section>

    <section class="kilometer-table-card">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table">
                <thead>
                    <tr>
                        <th>NGÀY</th>
                        @if (auth()->user()->isAdmin())
                            <th>NHÓM</th>
                        @endif
                        <th>ĐỊA CHỈ</th>
                        <th>PHƯỜNG</th>
                        <th>QUẬN</th>
                        <th class="text-end">KM</th>
                        <th class="text-end">TIỀN CHÀNH</th>
                        <th>XE ÔM</th>
                        <th>THÔNG TIN GHI BAO</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kilometers as $kilometer)
                        <tr>
                            <td class="date-cell">{{ $kilometer->date->format('d/m/Y') }}</td>
                            @if (auth()->user()->isAdmin())
                                <td>
                                    @if ($kilometer->group_id)
                                        <span class="group-badge">Nhóm {{ $kilometer->group_id }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endif
                            <td class="address-cell">{{ $kilometer->address }}</td>
                            <td>{{ $kilometer->ward }}</td>
                            <td>{{ $kilometer->district }}</td>
                            <td class="number-cell">{{ number_format((float) $kilometer->distance_km, 2, ',', '.') }}</td>
                            <td class="money-cell">{{ number_format((float) $kilometer->carrier_fee, 0, ',', '.') }} đ</td>
                            <td>{{ $kilometer->motorbike_driver ?: '—' }}</td>
                            <td class="note-cell">{{ $kilometer->package_note ?: '—' }}</td>
                            <td>
                                @php
                                    $canModify = auth()->user()?->isAdmin()
                                        || ($kilometer->group_id && (int) $kilometer->group_id === (int) auth()->user()?->group_id)
                                        || ($kilometer->owner_user_id && $kilometer->owner_user_id === auth()->id());
                                @endphp
                                @if ($canModify)
                                    <div class="kilometer-actions">
                                        <button type="button" class="kilometer-action-button edit" data-bs-toggle="modal" data-bs-target="#editKilometerModal-{{ $kilometer->id }}">Sửa</button>
                                        <form method="POST" action="{{ route('kilometers.destroy', $kilometer) }}" onsubmit="return confirm('Bạn có chắc muốn xóa kilomet này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="kilometer-action-button delete">Xóa</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 13px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 10 : 9 }}" class="empty-table-cell">
                                <strong>Chưa có dữ liệu kilomet</strong>
                                <span>{{ $search !== '' || $selectedGroupId ? 'Không tìm thấy bản ghi phù hợp với từ khóa hoặc bộ lọc.' : 'Dữ liệu sẽ hiển thị tại đây sau khi được thêm hoặc import từ file Excel.' }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kilometers->hasPages())
            <div class="kilometer-pagination">
                {{ $kilometers->links() }}
            </div>
        @endif
    </section>
</div>

<!-- Modal Create Single Kilometer -->
<div class="modal fade" id="createKilometerModal" tabindex="-1" aria-labelledby="createKilometerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content kilometer-modal-content">
            <div class="modal-header">
                <div><span class="panel-kicker">New record</span><h2 class="modal-title" id="createKilometerModalLabel">Thêm kilomet</h2></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">@include('kilometers._form')</div>
        </div>
    </div>
</div>

<!-- Modal Edit Kilometer -->
@foreach ($kilometers as $kilometer)
    <div class="modal fade" id="editKilometerModal-{{ $kilometer->id }}" tabindex="-1" aria-labelledby="editKilometerModalLabel-{{ $kilometer->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content kilometer-modal-content">
                <div class="modal-header">
                    <div><span class="panel-kicker">Edit record</span><h2 class="modal-title" id="editKilometerModalLabel-{{ $kilometer->id }}">Sửa kilomet</h2></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">@include('kilometers._form', ['kilometer' => $kilometer, 'formAction' => route('kilometers.update', $kilometer), 'formMethod' => 'PUT'])</div>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Import Kilomet from Excel -->
<div class="modal fade" id="importKilometerModal" tabindex="-1" aria-labelledby="importKilometerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content kilometer-modal-content">
            <div class="modal-header">
                <div>
                    <span class="panel-kicker">Import from Excel</span>
                    <h2 class="modal-title" id="importKilometerModalLabel">Import Kilomet theo nhóm</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form method="POST" action="{{ route('kilometers.import') }}" enctype="multipart/form-data" class="kilometer-form">
                @csrf
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="import-guide-box" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-size: .85rem; color: #166534; line-height: 1.5;">
                        <strong style="display: block; margin-bottom: .3rem; font-size: .9rem;">Hướng dẫn cấu trúc file Excel:</strong>
                        - Hệ thống hỗ trợ file mẫu <code>ds_kilomet.xlsx</code> gồm nhiều sheet theo từng nhóm (nhóm 1, nhóm 2, ...) hoặc file 1 sheet.<br>
                        - Các cột chuẩn: <strong>NHÓM</strong>, <strong>ĐỊA CHỈ</strong>, <strong>PHƯỜNG</strong>, <strong>QUẬN</strong>, <strong>KILOMET</strong>, <strong>TIỀN CHÀNH</strong>, <strong>THÔNG TIN GHI BAO</strong>.
                    </div>

                    <div class="kilometer-form-grid" style="grid-template-columns: 1fr;">
                        <div class="kilometer-form-field">
                            <label for="import-group-select"><strong>Nhóm áp dụng khi import</strong></label>
                            @if (auth()->user()->isAdmin())
                                <select id="import-group-select" name="group_id" class="form-select" style="padding: .6rem; border: 1px solid var(--line); border-radius: 6px; font-size: .88rem;">
                                    <option value="all">★ Tất cả các nhóm (Tự động theo từng sheet / cột NHÓM trong file)</option>
                                    @foreach ($groups as $g)
                                        <option value="{{ $g['id'] }}" @selected((string)$selectedGroupId === (string)$g['id'])>Chỉ import riêng: {{ $g['name'] }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="margin-top: .3rem; font-size: .78rem;">Chọn "Tất cả các nhóm" nếu file có nhiều sheet hoặc muốn import toàn bộ danh sách.</small>
                            @else
                                <input type="hidden" name="group_id" value="{{ auth()->user()->group_id }}">
                                <input type="text" class="form-control" value="Nhóm {{ auth()->user()->group_id }}" disabled style="background: #f8fafc; font-weight: 700; color: #1e3a8a;">
                                <small class="text-muted" style="margin-top: .3rem; font-size: .78rem;">Bạn chỉ có quyền import dữ liệu cho Nhóm {{ auth()->user()->group_id }}.</small>
                            @endif
                        </div>

                        <div class="kilometer-form-field" style="margin-top: 1rem;">
                            <label for="import-file"><strong>Chọn file Excel (.xlsx, .xls, .csv) <span style="color: #dc2626;">*</span></strong></label>
                            <input id="import-file" type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-control" style="padding: .5rem; border: 1px solid var(--line); border-radius: 6px;">
                        </div>

                        <div class="kilometer-form-field" style="margin-top: 1rem;">
                            <label for="import-duplicate-mode"><strong>Xử lý dữ liệu trùng lặp</strong></label>
                            <select id="import-duplicate-mode" name="duplicate_mode" class="form-select" style="padding: .6rem; border: 1px solid var(--line); border-radius: 6px; font-size: .88rem;">
                                <option value="skip" selected>Bỏ qua dòng đã tồn tại (Khuyên dùng - tránh nhân đôi dữ liệu)</option>
                                <option value="update">Cập nhật lại thông tin nếu đã tồn tại</option>
                                <option value="append">Thêm mới toàn bộ (Cho phép thêm dòng trùng)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background: #fafcfa; border-top: 1px solid var(--line);">
                    <div>
                        <a href="{{ route('kilometers.template') }}" class="btn btn-outline-secondary btn-sm" style="display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .85rem; font-size: .82rem; font-weight: 600;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Tải file mẫu Excel
                        </a>
                    </div>
                    <div style="display: flex; gap: .5rem;">
                        <button type="button" class="btn kilometer-cancel-button" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn kilometer-submit-button" style="display: inline-flex; align-items: center; gap: .4rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Bắt đầu Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.page-heading-actions {
    display: flex;
    align-items: center;
    gap: .65rem;
}
.kilometer-import-button {
    display: inline-flex;
    align-items: center;
    padding: .65rem 1.15rem;
    font-size: .85rem;
    font-weight: 700;
    color: #15803d;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 8px;
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none;
}
.kilometer-import-button:hover {
    background: #d1fae5;
    border-color: #6ee7b7;
    color: #047857;
}
.kilometer-group-filter-select {
    padding: .5rem .85rem;
    font-size: .82rem;
    border: 1px solid var(--line);
    border-radius: 6px;
    background: #fff;
    color: var(--ink);
    font-weight: 700;
    outline: 0;
    min-width: 160px;
}
.kilometer-group-filter-select:focus {
    border-color: var(--mint-deep);
}
.group-badge {
    display: inline-block;
    padding: .25rem .55rem;
    border-radius: 5px;
    font-size: .75rem;
    font-weight: 700;
    color: #1e40af;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    white-space: nowrap;
}
@media (max-width: 767.98px) {
    .page-heading-actions {
        width: 100%;
        justify-content: flex-start;
        margin-top: .5rem;
    }
}
</style>
@endsection
