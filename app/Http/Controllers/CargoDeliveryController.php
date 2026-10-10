<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CargoDelivery;
use App\Models\Employee;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CargoDeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $note = trim((string) $request->query('note', ''));

        $deliveries = CargoDelivery::query()
            ->with(['employee', 'kilometer'])
            ->when($note !== '', function ($query) use ($note) {
                $query->where('note', 'like', "%{$note}%");
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_name', 'like', "%{$search}%")
                        ->orWhere('group_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('package_note', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('employee', fn ($employee) => $employee->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('trip_count')
            ->oldest('updated_at')
            ->oldest('id')
            ->paginate(10)
            ->withQueryString();

        $groups = Group::query()
            ->where('status', 'active')
            ->oldest('group_date')
            ->oldest('id')
            ->get();

        return view('cargo-deliveries.index', [
            'deliveries' => $deliveries,
            'employees' => Employee::query()->orderBy('name')->get(),
            'groups' => $groups,
            'search' => $search,
            'note' => $note,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validatedData($request);

        $selectedDate = !empty($data['delivery_date'])
            ? \Illuminate\Support\Carbon::parse($data['delivery_date'])->format('Y-m-d')
            : now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');
        $data['delivery_date'] = \Illuminate\Support\Carbon::parse("{$selectedDate} {$currentTime}");

        $cargoDelivery = CargoDelivery::create($data);

        ActivityLog::createLog(
            description: "Thêm thông tin chở hàng: {$cargoDelivery->invoice_name} (Nhóm: {$cargoDelivery->group_name}, Địa chỉ: {$cargoDelivery->address}, Số bao: {$cargoDelivery->package_count}, TL: {$cargoDelivery->weight_kg} kg)",
            module: 'cargo-deliveries',
            action: 'create',
            subject: $cargoDelivery,
        );

        if ($request->expectsJson() || $request->ajax()) {
            $cargoDelivery->load(['employee', 'kilometer']);
            $groups = Group::query()->where('status', 'active')->oldest('group_date')->oldest('id')->get();
            $employees = Employee::query()->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm thông tin chở hàng thành công.',
                'delivery' => $cargoDelivery,
                'row_html' => view('cargo-deliveries._row', ['delivery' => $cargoDelivery])->render(),
                'modal_html' => view('cargo-deliveries._edit_modal', [
                    'delivery' => $cargoDelivery,
                    'employees' => $employees,
                    'groups' => $groups,
                ])->render(),
            ]);
        }

        return to_route('cargo-deliveries.index')->with('status', 'Đã thêm thông tin chở hàng.');
    }

    public function update(Request $request, CargoDelivery $cargoDelivery): RedirectResponse
    {
        $data = $this->validatedData($request);
        unset($data['delivery_date']);
        unset($data['delivery_status']);

        $cargoDelivery->update($data);

        ActivityLog::createLog(
            description: "Cập nhật thông tin chở hàng: {$cargoDelivery->invoice_name} (Nhóm: {$cargoDelivery->group_name})",
            module: 'cargo-deliveries',
            action: 'update',
            subject: $cargoDelivery,
        );

        return to_route('cargo-deliveries.index')->with('status', 'Đã cập nhật thông tin chở hàng.');
    }

    public function updateStatus(Request $request, CargoDelivery $cargoDelivery): RedirectResponse|JsonResponse
    {
        $statusLabels = [
            'pending' => 'Chưa giao',
            'delivered' => 'Đã giao',
            'failed' => 'Đang giao',
        ];
        $oldStatus = $statusLabels[$cargoDelivery->delivery_status] ?? $cargoDelivery->delivery_status;

        $validated = $request->validate([
            'delivery_status' => ['required', 'in:pending,delivered,failed'],
        ]);
        $cargoDelivery->update($validated);
        $newStatus = $statusLabels[$cargoDelivery->delivery_status] ?? $cargoDelivery->delivery_status;

        ActivityLog::createLog(
            description: "Đổi trạng thái đơn chở hàng {$cargoDelivery->invoice_name}: {$oldStatus} → {$newStatus}",
            module: 'cargo-deliveries',
            action: 'status',
            subject: $cargoDelivery,
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái đơn.',
                'delivery_status' => $cargoDelivery->delivery_status,
                'delivery_status_label' => $newStatus,
            ]);
        }

        return to_route('cargo-deliveries.index')->with('status', 'Đã cập nhật trạng thái đơn.');
    }

    public function destroy(CargoDelivery $cargoDelivery): RedirectResponse
    {
        $name = $cargoDelivery->invoice_name;
        $group = $cargoDelivery->group_name;
        $cargoDelivery->delete();

        ActivityLog::createLog(
            description: "Xóa thông tin chở hàng: {$name} (Nhóm: {$group})",
            module: 'cargo-deliveries',
            action: 'delete',
        );

        return to_route('cargo-deliveries.index')->with('status', 'Đã xóa thông tin chở hàng.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'delivery_date' => ['nullable', 'date'],
            'trip_count' => ['required', 'integer', 'min:1'],
            'package_count' => ['required', 'string', 'max:50'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'package_note' => ['nullable', 'string'],
            'delivery_status' => ['nullable', 'in:pending,delivered,failed'],
        ]);

        $group = Group::where('status', 'active')->findOrFail($data['group_id']);
        $data['kilometer_id'] = $group->kilometer_id;
        $data['invoice_name'] = $group->invoice_name;
        $data['group_name'] = $group->group_name;
        $data['address'] = $group->address;
        $data['ward'] = $group->ward;
        $data['district'] = $group->district;
        $data['distance_km'] = $group->distance_km;
        $data['carrier_fee'] = $group->carrier_fee;
        $data['motorbike_driver'] = $group->motorbike_driver;
        $data['package_note'] = $group->package_note;
        $data['note'] = $data['note'] ?? null;
        $data['delivery_status'] = $data['delivery_status'] ?? 'pending';

        return $data;
    }
}
