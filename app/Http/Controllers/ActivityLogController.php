<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Chỉ Quản trị viên (Admin) mới có quyền truy cập Nhật ký hoạt động.');

        $search = trim((string) $request->query('search', ''));
        $module = trim((string) $request->query('module', ''));
        $action = trim((string) $request->query('action', ''));
        $userId = trim((string) $request->query('user_id', ''));
        $fromDate = trim((string) $request->query('from_date', ''));
        $toDate = trim((string) $request->query('to_date', ''));

        $logs = ActivityLog::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('user_name', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%");
                });
            })
            ->when($module !== '', fn ($query) => $query->where('module', $module))
            ->when($action !== '', fn ($query) => $query->where('action', $action))
            ->when($userId !== '', fn ($query) => $query->where('user_id', $userId))
            ->when($fromDate !== '', fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate !== '', fn ($query) => $query->whereDate('created_at', '<=', $toDate))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $users = User::query()->orderBy('name')->get(['id', 'name', 'role']);

        $modules = [
            'cargo-deliveries' => 'TT Chở hàng',
            'kilometers' => 'Kilomet',
            'groups' => 'Nhóm KD',
            'employees' => 'Nhân viên',
            'permissions' => 'Phân quyền',
            'orders' => 'Đơn hàng',
            'auth' => 'Hệ thống',
        ];

        $actions = [
            'create' => 'Thêm mới',
            'update' => 'Cập nhật',
            'status' => 'Đổi trạng thái',
            'delete' => 'Xóa',
            'login' => 'Đăng nhập',
        ];

        return view('activity-logs.index', compact(
            'logs',
            'search',
            'module',
            'action',
            'userId',
            'fromDate',
            'toDate',
            'users',
            'modules',
            'actions'
        ));
    }
}
