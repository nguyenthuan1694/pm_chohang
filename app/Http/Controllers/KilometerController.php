<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Kilometer;
use App\Models\User;
use App\Services\KilometerImportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KilometerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedGroupId = $request->query('group_id');
        $user = auth()->user() ?: $request->user();

        $kilometers = $this->scopedKilometers()
            ->when($selectedGroupId, fn ($q) => $q->where('group_id', $selectedGroupId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('address', 'like', "%{$search}%")
                        ->orWhere('ward', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('motorbike_driver', 'like', "%{$search}%")
                        ->orWhere('package_note', 'like', "%{$search}%");
                });
            })
            ->latest('date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $groupList = [];
        if ($user?->role === 'group') {
            $groupList[] = [
                'id' => $user->group_id,
                'name' => 'Nhóm ' . $user->group_id,
            ];
        } else {
            $activeGroups = User::query()
                ->where('role', 'group')
                ->whereNotNull('group_id')
                ->orderBy('group_id')
                ->get(['id', 'group_id', 'name']);
            foreach ($activeGroups as $ag) {
                $groupList[] = [
                    'id' => $ag->group_id,
                    'name' => 'Nhóm ' . $ag->group_id,
                ];
            }
        }

        return view('kilometers.index', [
            'kilometers' => $kilometers,
            'search' => $search,
            'groups' => $groupList,
            'selectedGroupId' => $selectedGroupId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $user = auth()->user() ?: $request->user();

        if ($user && $user->role === 'group') {
            $data['group_id'] = $user->group_id;
            $data['owner_user_id'] = $user->id;
        } else {
            $data['owner_user_id'] = $user?->id;
        }

        $created = Kilometer::create($data);

        ActivityLog::createLog(
            description: "Thêm kilomet: {$created->address} (Quận: {$created->district}, {$created->distance_km} km)",
            module: 'kilometers',
            action: 'create',
            subject: $created,
        );

        return to_route('kilometers.index')->with('status', 'Đã thêm kilomet mới.');
    }

    public function update(Request $request, Kilometer $kilometer): RedirectResponse
    {
        $this->ensureOwnsKilometer($request, $kilometer);
        $kilometer->update($this->validatedData($request));

        ActivityLog::createLog(
            description: "Cập nhật kilomet: {$kilometer->address} (Quận: {$kilometer->district}, {$kilometer->distance_km} km)",
            module: 'kilometers',
            action: 'update',
            subject: $kilometer,
        );

        return to_route('kilometers.index')->with('status', 'Đã cập nhật kilomet.');
    }

    public function destroy(Kilometer $kilometer): RedirectResponse
    {
        $this->ensureOwnsKilometer(request(), $kilometer);
        $addr = $kilometer->address;
        $km = $kilometer->distance_km;
        $kilometer->delete();

        ActivityLog::createLog(
            description: "Xóa kilomet: {$addr} ({$km} km)",
            module: 'kilometers',
            action: 'delete',
        );

        return to_route('kilometers.index')->with('status', 'Đã xóa kilomet.');
    }

    private function scopedKilometers()
    {
        $query = Kilometer::query();
        $user = auth()->user();

        if ($user?->role === 'group') {
            $query->where(function ($q) use ($user) {
                if ($user->group_id) {
                    $q->where('group_id', $user->group_id);
                }
                $q->orWhere('owner_user_id', $user->id)
                  ->orWhereNull('group_id');
            });
        }

        return $query;
    }

    private function ensureOwnsKilometer(Request $request, Kilometer $kilometer): void
    {
        $user = auth()->user() ?: $request->user();
        if ($user?->role === 'group') {
            $owns = ($kilometer->owner_user_id && $kilometer->owner_user_id === $user->id)
                || ($user->group_id && (int) $kilometer->group_id === (int) $user->group_id);

            abort_unless($owns, 403);
        }
    }

    public function import(Request $request, KilometerImportService $importService): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
            'group_id' => ['nullable', 'string'],
            'duplicate_mode' => ['nullable', 'in:skip,update,append'],
        ], [
            'file.required' => 'Vui lòng chọn file Excel để import.',
            'file.mimes' => 'File tải lên phải có định dạng .xlsx, .xls hoặc .csv.',
            'file.max' => 'Dung lượng file không được vượt quá 20MB.',
        ]);

        $user = auth()->user() ?: $request->user();
        $targetGroupId = null;

        if ($user?->role === 'group') {
            $targetGroupId = (int) $user->group_id;
        } else {
            $rawGroup = $request->input('group_id');
            if ($rawGroup && $rawGroup !== 'all') {
                $targetGroupId = (int) $rawGroup;
            }
        }

        $duplicateMode = $request->input('duplicate_mode', 'skip');
        $file = $request->file('file');

        try {
            $result = $importService->import(
                filePath: $file->getRealPath(),
                targetGroupId: $targetGroupId,
                duplicateMode: $duplicateMode,
                currentUser: $user
            );

            $msg = "Đã import thành công: Thêm mới {$result['imported']} dòng";
            if ($result['updated'] > 0) {
                $msg .= ", cập nhật {$result['updated']} dòng";
            }
            if ($result['skipped'] > 0) {
                $msg .= ", bỏ qua {$result['skipped']} dòng trùng";
            }
            $msg .= ". ({$result['group_summary']})";

            return to_route('kilometers.index')->with('status', $msg);
        } catch (\Throwable $e) {
            return to_route('kilometers.index')->withErrors(['file' => 'Lỗi khi xử lý file Excel: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $path = base_path('ds_kilomet.xlsx');
        abort_unless(file_exists($path), 404, 'File mẫu không tồn tại trên hệ thống.');

        return response()->download($path, 'ds_kilomet.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function validatedData(Request $request): array
    {
        // Support fallback if request has old field names
        $map = [
            'ngay' => 'date',
            'dia_chi' => 'address',
            'phuong' => 'ward',
            'quan' => 'district',
            'km' => 'distance_km',
            'tien_chanh' => 'carrier_fee',
            'xe_om' => 'motorbike_driver',
            'thong_tin_ghi_bao' => 'package_note',
        ];
        foreach ($map as $old => $new) {
            if ($request->has($old) && !$request->has($new)) {
                $request->merge([$new => $request->input($old)]);
            }
        }

        return $request->validate([
            'date' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'distance_km' => ['required', 'numeric', 'min:0'],
            'carrier_fee' => ['required', 'numeric', 'min:0'],
            'motorbike_driver' => ['nullable', 'string', 'max:150'],
            'package_note' => ['nullable', 'string'],
        ]);
    }
}

