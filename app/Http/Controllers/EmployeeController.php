<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $employees = Employee::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest('started_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $created = Employee::create($this->validatedData($request));

        ActivityLog::createLog(
            description: "Thêm nhân viên: {$created->name}",
            module: 'employees',
            action: 'create',
            subject: $created,
        );

        return to_route('employees.index')->with('status', 'Đã thêm nhân viên mới.');
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($this->validatedData($request));

        ActivityLog::createLog(
            description: "Cập nhật thông tin nhân viên: {$employee->name}",
            module: 'employees',
            action: 'update',
            subject: $employee,
        );

        return to_route('employees.index')->with('status', 'Đã cập nhật nhân viên.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->name;
        $employee->delete();

        ActivityLog::createLog(
            description: "Xóa nhân viên: {$name}",
            module: 'employees',
            action: 'delete',
        );

        return to_route('employees.index')->with('status', 'Đã xóa nhân viên.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'started_at' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'is_support' => ['nullable'],
        ]);

        $data['is_support'] = $request->boolean('is_support');

        return $data;
    }
}
