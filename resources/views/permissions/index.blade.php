@extends('layouts.dashboard')

@section('title', 'Phân quyền | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview permissions-page">
    @if (session('status'))<div class="alert alert-success dashboard-alert" role="alert">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger dashboard-alert" role="alert">{{ $errors->first() }}</div>@endif

    <div class="page-heading-row">
        <div><p class="dashboard-breadcrumb">Workspace / Security</p><h1 class="page-heading">Phân quyền</h1><p class="page-subtitle">Quản lý tài khoản và quyền truy cập theo từng module.</p></div>
        <button type="button" class="btn kilometer-add-button" data-bs-toggle="modal" data-bs-target="#createPermissionUserModal"><span aria-hidden="true">+</span> Tạo tài khoản</button>
    </div>

    <section class="permission-account-list">
        @forelse ($users as $user)
            @php
                $permissionCount = 0;
                $moduleCount = 0;
                foreach ((array) $user->permissions as $modulePermissions) {
                    $enabledCount = count(array_filter((array) $modulePermissions));
                    $permissionCount += $enabledCount;
                    $moduleCount += $enabledCount > 0 ? 1 : 0;
                }
            @endphp
            <article class="permission-account-card">
                @if ($user->isAdmin())
                    <div class="permission-account-heading permission-account-heading-admin">
                        <div class="permission-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div class="permission-account-identity"><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></div>
                        <span class="permission-role role-admin">Admin</span>
                        <span class="permission-access-summary">Toàn quyền hệ thống</span>
                    </div>
                    <div class="permission-admin-note">Admin có toàn quyền Xem, Thêm, Sửa, Xóa trên tất cả module.</div>
                @else
                    <details class="permission-account-details">
                        <summary class="permission-account-heading">
                            <div class="permission-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div class="permission-account-identity"><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></div>
                            <span class="permission-role role-{{ $user->role }}">{{ $user->role === 'group' ? 'Nhóm kinh doanh' : 'Kho ngoài' }}</span>
                            <!-- @if ($user->group)<span class="permission-scope">{{ $user->group->group_name }}</span>@elseif ($user->group_id)<span class="permission-scope">Nhóm {{ $user->group_id }}</span>@endif -->
                            <span class="permission-access-summary">{{ $moduleCount }} module / {{ $permissionCount }} quyền</span>
                            <span class="permission-expand-label">Chỉnh sửa</span>
                        </summary>
                        <form method="POST" action="{{ route('permissions.users.update', $user) }}" class="permission-matrix-form">
                            @csrf @method('PUT')
                            <div class="permission-matrix-wrap"><table class="permission-matrix"><thead><tr><th>MODULE</th>@foreach ($actions as $actionLabel)<th>{{ $actionLabel }}</th>@endforeach</tr></thead><tbody>
                                @foreach ($modules as $module => $moduleLabel)
                                    <tr><td>{{ $moduleLabel }}</td>@foreach ($actions as $action => $actionLabel)<td><label class="permission-check"><input type="checkbox" name="permissions[{{ $module }}][{{ $action }}]" value="1" @checked(data_get($user->permissions, "$module.$action", false))><span></span><b class="visually-hidden">{{ $actionLabel }}</b></label></td>@endforeach</tr>
                                @endforeach
                            </tbody></table></div>
                            <div class="permission-save-row"><span>Chọn quyền cần cấp cho tài khoản này.</span><button type="submit" class="btn kilometer-submit-button">Lưu quyền</button></div>
                        </form>
                    </details>
                @endif
            </article>
        @empty
            <div class="dashboard-panel">Chưa có tài khoản.</div>
        @endforelse
    </section>
</div>

<div class="modal fade" id="createPermissionUserModal" tabindex="-1" aria-labelledby="createPermissionUserModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content"><div class="modal-header"><div><span class="panel-kicker">New account</span><h2 class="modal-title" id="createPermissionUserModalLabel">Tạo tài khoản</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body"><form method="POST" action="{{ route('permissions.users.store') }}" class="permission-create-form">@csrf
    <div class="permission-create-grid"><div class="group-form-field"><label>Họ tên</label><input name="name" type="text" required placeholder="Tên người dùng"></div><div class="group-form-field"><label>Email</label><input name="email" type="email" required placeholder="email@example.com"></div><div class="group-form-field"><label>Mật khẩu</label><input name="password" type="password" required minlength="8" placeholder="Tối thiểu 8 ký tự"></div><div class="group-form-field"><label>Loại tài khoản</label><select name="role" data-permission-role required><option value="group">Nhóm kinh doanh</option><option value="warehouse">Kho ngoài</option></select></div><div class="group-form-field" data-permission-group><label for="create-user-group">Nhóm</label><input id="create-user-group" name="group_id" type="number" min="1" step="1" placeholder="Nhập số nhóm (ví dụ: 1)"></div></div>
    <div class="group-modal-actions"><button type="button" class="btn kilometer-cancel-button" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn kilometer-submit-button">Tạo tài khoản</button></div>
</form></div></div></div></div>
@endsection
