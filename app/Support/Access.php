<?php

namespace App\Support;

use App\Models\User;

class Access
{
    public const MODULES = [
        'groups' => 'DS Nhóm kinh doanh',
        'cargo-deliveries' => 'DS TT Chở hàng',
        'kilometers' => 'DS Kilomet',
        'orders' => 'DS Đơn hàng',
        'employees' => 'DS Nhân viên',
        'addresses' => 'DS Địa chỉ',
        'billing' => 'DS Tính tiền',
        'permissions' => 'Phân quyền',
    ];

    public const ACTIONS = [
        'view' => 'Xem',
        'create' => 'Thêm',
        'update' => 'Sửa',
        'delete' => 'Xóa',
    ];

    public static function defaults(string $role): array
    {
        if ($role === 'admin') {
            return array_fill_keys(array_keys(self::MODULES), array_fill_keys(array_keys(self::ACTIONS), true));
        }

        $module = $role === 'group' ? 'groups' : 'cargo-deliveries';
        return [$module => array_fill_keys(array_keys(self::ACTIONS), true)];
    }

    public static function can(?User $user, string $module, string $action = 'view'): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        return (bool) data_get($user->permissions, "{$module}.{$action}", false);
    }
}
