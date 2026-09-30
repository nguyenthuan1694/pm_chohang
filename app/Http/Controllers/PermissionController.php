<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\User;
use App\Support\Access;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('permissions.index', [
            'users' => User::with('group')
                ->orderByRaw("CASE WHEN role = 'admin' THEN 0 WHEN group_id IS NOT NULL THEN 1 ELSE 2 END")
                ->orderBy('group_id', 'asc')
                ->orderBy('id', 'asc')
                ->get(),
            'groups' => Group::where('status', 'active')->orderBy('group_name')->get(),
            'modules' => Access::MODULES,
            'actions' => Access::ACTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($request->filled('group') && !$request->filled('group_id')) {
            $request->merge(['group_id' => $request->input('group')]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:group,warehouse'],
            'group_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'group_id' => $data['role'] === 'group' ? ($data['group_id'] ?? null) : null,
            'permissions' => Access::defaults($data['role']),
        ]);

        ActivityLog::createLog(
            description: "Thêm tài khoản người dùng mới: {$user->name} ({$user->email}, Vai trò: {$user->role})",
            module: 'permissions',
            action: 'create',
            subject: $user,
        );

        return to_route('permissions.index')->with('status', 'Đã tạo tài khoản và phân quyền mặc định.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_if($user->isAdmin(), 403);

        $permissions = [];
        foreach (Access::MODULES as $module => $label) {
            foreach (Access::ACTIONS as $action => $actionLabel) {
                if ($request->boolean("permissions.{$module}.{$action}")) {
                    $permissions[$module][$action] = true;
                }
            }
        }

        $user->update(['permissions' => $permissions]);

        ActivityLog::createLog(
            description: "Cập nhật phân quyền cho tài khoản: {$user->name} ({$user->email})",
            module: 'permissions',
            action: 'update',
            subject: $user,
            properties: ['permissions' => $permissions],
        );

        return to_route('permissions.index')->with('status', 'Đã cập nhật quyền cho tài khoản.');
    }
}
