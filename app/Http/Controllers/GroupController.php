<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\Kilometer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $groups = $this->scopedGroups()
            ->with('kilometer')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_name', 'like', "%{$search}%")
                        ->orWhere('group_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('ward', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%");
                });
            })
            ->latest('group_date')
            ->latest('updated_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $user = auth()->user();
        $kilometers = Kilometer::query()
            ->when($user?->role === 'group', function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    if ($user->group_id) {
                        $sub->where('group_id', $user->group_id);
                    }
                    $sub->orWhere('owner_user_id', $user->id)
                        ->orWhereNull('group_id');
                });
            })
            ->latest('date')
            ->latest('id')
            ->get();

        return view('groups.index', [
            'groups' => $groups,
            'kilometers' => $kilometers,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['group_date'] = now();
        $created = Group::create($data);

        ActivityLog::createLog(
            description: "Thêm nhóm kinh doanh: {$created->group_name} - {$created->invoice_name}",
            module: 'groups',
            action: 'create',
            subject: $created,
        );

        return to_route('groups.index')->with('status', 'Đã thêm nhóm mới.');
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $this->ensureOwnsGroup($request, $group);
        $data = $this->validatedData($request);
        unset($data['status']);
        unset($data['group_date']);

        $group->update($data);

        ActivityLog::createLog(
            description: "Cập nhật nhóm kinh doanh: {$group->group_name} - {$group->invoice_name}",
            module: 'groups',
            action: 'update',
            subject: $group,
        );

        return to_route('groups.index')->with('status', 'Đã cập nhật nhóm.');
    }

    public function updateStatus(Request $request, Group $group): RedirectResponse
    {
        $this->ensureOwnsGroup($request, $group);
        if ($group->status === 'active') {
            return to_route('groups.index')->with('status', 'Nhóm đã Active và không thể cập nhật lại trạng thái.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $group->update($data);
        $statusText = $group->status === 'active' ? 'Đang hoạt động' : 'Tạm dừng';

        ActivityLog::createLog(
            description: "Đổi trạng thái nhóm {$group->group_name}: {$statusText}",
            module: 'groups',
            action: 'status',
            subject: $group,
        );

        return to_route('groups.index')->with('status', 'Đã cập nhật trạng thái nhóm.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $this->ensureOwnsGroup(request(), $group);
        $desc = "{$group->group_name} - {$group->invoice_name}";
        $group->delete();

        ActivityLog::createLog(
            description: "Xóa nhóm kinh doanh: {$desc}",
            module: 'groups',
            action: 'delete',
        );

        return to_route('groups.index')->with('status', 'Đã xóa nhóm.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'kilometer_id' => ['required', 'exists:kilometers,id'],
            'invoice_name' => ['required', 'string', 'max:150'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $kilometer = Kilometer::findOrFail($data['kilometer_id']);
        $data['group_date'] = now();
        $data['address'] = $kilometer->address;
        $data['ward'] = $kilometer->ward;
        $data['district'] = $kilometer->district;
        $data['distance_km'] = $kilometer->distance_km;
        $data['carrier_fee'] = $kilometer->carrier_fee;
        $data['motorbike_driver'] = $kilometer->motorbike_driver;
        $data['package_note'] = $kilometer->package_note;
        $data['status'] = $data['status'] ?? 'inactive';

        $user = auth()->user() ?: $request->user();
        if ($user && $user->role === 'group') {
            $userGroupName = $user->group_id ? ('Nhóm ' . $user->group_id) : ($user->name ?: 'Nhóm kinh doanh');
            $data['group_name'] = $userGroupName;
            $data['group_id'] = $user->group_id;
            $data['owner_user_id'] = $user->id;
        } else {
            $data['group_name'] = $request->input('group_name') ?: ($user?->name ?: 'Nhóm kinh doanh');
            $data['owner_user_id'] = $user?->id;
        }

        return $data;
    }

    private function scopedGroups()
    {
        $query = Group::query();
        $user = auth()->user();
        if ($user?->role === 'group') {
            $query->where(function ($q) use ($user) {
                if ($user->group_id) {
                    $q->where('group_id', $user->group_id)
                      ->orWhere('group_name', 'Nhóm ' . $user->group_id)
                      ->orWhere('group_name', (string) $user->group_id);
                }
                if ($user->name) {
                    $q->orWhere('group_name', $user->name);
                }
                $q->orWhere('owner_user_id', $user->id);
            });
        }
        return $query;
    }

    private function ensureOwnsGroup(Request $request, Group $group): void
    {
        $user = auth()->user() ?: $request->user();
        if ($user?->role === 'group') {
            $owns = ($group->owner_user_id && $group->owner_user_id === $user->id)
                || ($user->group_id && (int) $group->group_id === (int) $user->group_id)
                || ($user->group_id && str_contains($group->group_name, (string) $user->group_id))
                || ($user->name && $group->group_name === $user->name)
                || ($group->id === $user->group_id);

            abort_unless($owns, 403);
        }
    }
}
