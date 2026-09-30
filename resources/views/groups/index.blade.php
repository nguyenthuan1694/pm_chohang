@extends('layouts.dashboard')

@section('title', 'DS Nhóm | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview group-page">
    @if (session('status'))<div class="alert alert-success dashboard-alert" role="alert">{{ session('status') }}</div>@endif

    <div class="page-heading-row">
        <div><p class="dashboard-breadcrumb">Workspace / Groups</p><h1 class="page-heading">D.Sách thông tin giao hàng của nhóm</h1><p class="page-subtitle">Quản lý thông tin xuất phiếu và tuyến giao hàng theo nhóm.</p></div>
        <button type="button" class="btn kilometer-add-button" data-bs-toggle="modal" data-bs-target="#createGroupModal"><span aria-hidden="true">+</span> Thêm thông tin giao hàng</button>
    </div>

    <section class="kilometer-toolbar">
        <form method="GET" action="{{ route('groups.index') }}" class="kilometer-search-form"><label class="visually-hidden" for="group-search">Tìm kiếm nhóm</label><span class="search-icon" aria-hidden="true">⌕</span><input id="group-search" name="search" type="search" value="{{ $search }}" placeholder="Tìm tên phiếu, nhóm, địa chỉ...">@if ($search !== '')<a class="clear-search" href="{{ route('groups.index') }}">×</a>@endif<button type="submit" class="kilometer-search-button">Tìm kiếm</button></form>
        <span class="kilometer-updated">Cập nhật {{ now()->format('d/m/Y') }}</span>
    </section>

    <section class="kilometer-table-card">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table group-table">
                <thead><tr><th>NGÀY</th><th>TÊN XUẤT PHIẾU</th><th>NHÓM</th><th>ĐỊA CHỈ</th><th>PHƯỜNG</th><th>QUẬN</th><th>KM</th><th>TIỀN CHÀNH</th><th>XE ÔM</th><th>THÔNG TIN GHI BAO</th><th>TRẠNG THÁI</th><th>TÁC VỤ</th></tr></thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td class="date-cell">{{ $group->group_date->format('d/m/Y H:i') }}</td><td><strong>{{ $group->invoice_name }}</strong></td><td><span class="group-badge">{{ $group->group_name }}</span></td><td class="address-cell">{{ $group->address }}</td><td>{{ $group->ward }}</td><td>{{ $group->district }}</td><td class="number-cell">{{ number_format((float) $group->distance_km, 2, ',', '.') }}</td><td class="money-cell">{{ number_format((float) $group->carrier_fee, 0, ',', '.') }} đ</td><td>{{ $group->motorbike_driver ?: '—' }}</td><td class="note-cell">{{ $group->package_note ?: '—' }}</td>
                            <td><form method="POST" action="{{ route('groups.status', $group) }}">@csrf @method('PATCH')<select name="status" class="group-status-select status-{{ $group->status }}" onchange="this.form.submit()" @disabled($group->status === 'active')><option value="inactive" @selected($group->status === 'inactive')>Chưa Active</option><option value="active" @selected($group->status === 'active')>Active</option></select></form></td>
                            <td><div class="kilometer-actions"><button type="button" class="kilometer-action-button edit" data-bs-toggle="modal" data-bs-target="#editGroupModal-{{ $group->id }}">Sửa</button> @if($group->status === 'inactive')<form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('Bạn có chắc muốn xóa nhóm này?');">@csrf @method('DELETE')<button type="submit" class="kilometer-action-button delete">Xóa</button></form>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="empty-table-cell"><strong>Chưa có dữ liệu nhóm</strong><span>{{ $search !== '' ? 'Không tìm thấy nhóm phù hợp.' : 'Dữ liệu sẽ hiển thị sau khi được thêm.' }}</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($groups->hasPages())<div class="kilometer-pagination">{{ $groups->links() }}</div>@endif
    </section>
</div>

<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content"><div class="modal-header"><div><span class="panel-kicker">New delivery information</span><h2 class="modal-title" id="createGroupModalLabel">Thêm thông tin giao hàng</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body">@include('groups._form', ['group' => null, 'isEditing' => false])</div></div></div></div>

@foreach ($groups as $group)
    <div class="modal fade" id="editGroupModal-{{ $group->id }}" tabindex="-1" aria-labelledby="editGroupModalLabel-{{ $group->id }}" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content"><div class="modal-header"><div><span class="panel-kicker">Edit group</span><h2 class="modal-title" id="editGroupModalLabel-{{ $group->id }}">Sửa nhóm</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body">@include('groups._form', ['group' => $group, 'formAction' => route('groups.update', $group), 'formMethod' => 'PUT'])</div></div></div></div>
@endforeach
@endsection
