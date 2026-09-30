<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'module',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function createLog(
        string $description,
        string $module = 'general',
        string $action = 'info',
        ?Model $subject = null,
        ?array $properties = null,
        ?User $user = null
    ): self {
        $user = $user ?? auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Hệ thống',
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'properties' => $properties,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    public function getModuleLabelAttribute(): string
    {
        return match ($this->module) {
            'cargo-deliveries' => 'TT Chở hàng',
            'kilometers' => 'Kilomet',
            'groups' => 'Nhóm KD',
            'employees' => 'Nhân viên',
            'permissions' => 'Phân quyền',
            'orders' => 'Đơn hàng',
            'billing' => 'Tính tiền',
            'auth' => 'Hệ thống',
            default => ucfirst($this->module),
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Thêm mới',
            'update' => 'Cập nhật',
            'status' => 'Đổi trạng thái',
            'delete' => 'Xóa',
            'login' => 'Đăng nhập',
            default => ucfirst($this->action),
        };
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'create' => 'badge-action-create',
            'update' => 'badge-action-update',
            'status' => 'badge-action-status',
            'delete' => 'badge-action-delete',
            'login' => 'badge-action-login',
            default => 'badge-action-default',
        };
    }

    public function getActionDotClassAttribute(): string
    {
        return match ($this->action) {
            'create' => 'activity-dot-mint',
            'update' => 'activity-dot-ink',
            'status' => 'activity-dot-coral',
            'delete' => 'activity-dot-danger',
            'login' => 'activity-dot-purple',
            default => 'activity-dot-mint',
        };
    }
}
