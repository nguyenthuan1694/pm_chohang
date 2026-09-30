<?php

namespace App\Http\Controllers;

use App\Models\CargoDelivery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $orders = CargoDelivery::query()
            ->with(['employee', 'group'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_name', 'like', "%{$search}%")
                        ->orWhere('group_name', 'like', "%{$search}%")
                        ->orWhere('delivery_status', 'like', "%{$search}%")
                        ->orWhereHas('employee', fn ($employee) => $employee->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders', 'search'));
    }
}
