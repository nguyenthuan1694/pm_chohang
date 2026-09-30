@extends('layouts.dashboard')

@section('title', 'DS Nhân viên | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview employee-page">
    @if (session('status'))
        <div class="alert alert-success dashboard-alert" role="alert">{{ session('status') }}</div>
    @endif

    <div class="page-heading-row">
        <div>
            <p class="dashboard-breadcrumb">Workspace / People</p>
            <h1 class="page-heading">Danh sách nhân viên</h1>
            <p class="page-subtitle">Quản lý thông tin và ngày bắt đầu làm việc của đội ngũ.</p>
        </div>
        <div class="employee-heading-actions">
            <button type="button" class="btn kilometer-add-button" data-bs-toggle="modal" data-bs-target="#createEmployeeModal"><span aria-hidden="true">+</span> Thêm nhân viên</button>
        </div>
    </div>

    <section class="kilometer-toolbar">
        <form method="GET" action="{{ route('employees.index') }}" class="kilometer-search-form">
            <label class="visually-hidden" for="employee-search">Tìm kiếm nhân viên</label>
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input id="employee-search" name="search" type="search" value="{{ $search }}" placeholder="Tìm theo tên hoặc địa chỉ...">
            @if ($search !== '')<a class="clear-search" href="{{ route('employees.index') }}" aria-label="Xóa tìm kiếm">×</a>@endif
            <button type="submit" class="kilometer-search-button">Tìm kiếm</button>
        </form>
        <span class="kilometer-updated">Cập nhật {{ now()->format('d/m/Y') }}</span>
    </section>

    <section class="kilometer-table-card">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table employee-table">
                <thead>
                    <tr>
                        <th>THUMBNAIL</th>
                        <th>TÊN NHÂN VIÊN</th>
                        <th>NGÀY VÀO LÀM</th>
                        <th>ĐỊA CHỈ</th>
                        <th>HỖ TRỢ</th>
                        <th>TÁC VỤ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td><img class="employee-thumbnail" src="{{ $employee->thumbnail_url }}" alt="{{ $employee->name }}"></td>
                            <td><strong class="employee-name">{{ $employee->name }}</strong></td>
                            <td class="date-cell">{{ $employee->started_at->format('d/m/Y') }}</td>
                            <td class="employee-address">{{ $employee->address }}</td>
                            <td>
                                @if ($employee->is_support)
                                    <span class="employee-support-badge">Hỗ trợ</span>
                                @else
                                    <span class="employee-normal-text">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="kilometer-actions">
                                    <button type="button" class="kilometer-action-button edit" data-bs-toggle="modal" data-bs-target="#editEmployeeModal-{{ $employee->id }}">Sửa</button>
                                    <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="kilometer-action-button delete">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-table-cell"><strong>Chưa có dữ liệu nhân viên</strong><span>{{ $search !== '' ? 'Không tìm thấy nhân viên phù hợp.' : 'Dữ liệu sẽ hiển thị tại đây sau khi được thêm.' }}</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($employees->hasPages())
            <div class="kilometer-pagination">{{ $employees->links() }}</div>
        @endif
    </section>
</div>

<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content">
        <div class="modal-header"><div><span class="panel-kicker">New employee</span><h2 class="modal-title" id="createEmployeeModalLabel">Thêm nhân viên</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div>
        <div class="modal-body">@include('employees._form')</div>
    </div></div>
</div>

@foreach ($employees as $employee)
    <div class="modal fade" id="editEmployeeModal-{{ $employee->id }}" tabindex="-1" aria-labelledby="editEmployeeModalLabel-{{ $employee->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content">
            <div class="modal-header"><div><span class="panel-kicker">Edit employee</span><h2 class="modal-title" id="editEmployeeModalLabel-{{ $employee->id }}">Sửa nhân viên</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div>
            <div class="modal-body">@include('employees._form', ['employee' => $employee, 'formAction' => route('employees.update', $employee), 'formMethod' => 'PUT'])</div>
        </div></div>
    </div>
@endforeach
@endsection
