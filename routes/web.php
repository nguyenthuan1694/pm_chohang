<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware('module:kilometers,view')->group(function () {
    Route::get('/kilometers', [App\Http\Controllers\KilometerController::class, 'index'])->name('kilometers.index');
    Route::get('/kilometers/template', [App\Http\Controllers\KilometerController::class, 'downloadTemplate'])->name('kilometers.template');
});
Route::middleware('module:kilometers,create')->post('/kilometers', [App\Http\Controllers\KilometerController::class, 'store'])->name('kilometers.store');
Route::middleware('module:kilometers,create')->post('/kilometers/import', [App\Http\Controllers\KilometerController::class, 'import'])->name('kilometers.import');
Route::middleware('module:kilometers,update')->put('/kilometers/{kilometer}', [App\Http\Controllers\KilometerController::class, 'update'])->name('kilometers.update');
Route::middleware('module:kilometers,delete')->delete('/kilometers/{kilometer}', [App\Http\Controllers\KilometerController::class, 'destroy'])->name('kilometers.destroy');
Route::middleware('module:employees,view')->get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
Route::middleware('module:employees,create')->post('/employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
Route::middleware('module:employees,update')->put('/employees/{employee}', [App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
Route::middleware('module:employees,delete')->delete('/employees/{employee}', [App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::middleware('module:cargo-deliveries,view')->get('/cargo-deliveries', [App\Http\Controllers\CargoDeliveryController::class, 'index'])->name('cargo-deliveries.index');
Route::middleware('module:cargo-deliveries,create')->post('/cargo-deliveries', [App\Http\Controllers\CargoDeliveryController::class, 'store'])->name('cargo-deliveries.store');
Route::middleware('module:cargo-deliveries,update')->put('/cargo-deliveries/{cargoDelivery}', [App\Http\Controllers\CargoDeliveryController::class, 'update'])->name('cargo-deliveries.update');
Route::middleware('module:cargo-deliveries,update')->patch('/cargo-deliveries/{cargoDelivery}/status', [App\Http\Controllers\CargoDeliveryController::class, 'updateStatus'])->name('cargo-deliveries.status');
Route::middleware('module:cargo-deliveries,delete')->delete('/cargo-deliveries/{cargoDelivery}', [App\Http\Controllers\CargoDeliveryController::class, 'destroy'])->name('cargo-deliveries.destroy');
Route::middleware('module:orders,view')->get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
Route::middleware('module:groups,view')->get('/groups', [App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
Route::middleware('module:groups,create')->post('/groups', [App\Http\Controllers\GroupController::class, 'store'])->name('groups.store');
Route::middleware('module:groups,update')->put('/groups/{group}', [App\Http\Controllers\GroupController::class, 'update'])->name('groups.update');
Route::middleware('module:groups,update')->patch('/groups/{group}/status', [App\Http\Controllers\GroupController::class, 'updateStatus'])->name('groups.status');
Route::middleware('module:groups,delete')->delete('/groups/{group}', [App\Http\Controllers\GroupController::class, 'destroy'])->name('groups.destroy');
Route::middleware('module:permissions,view')->get('/permissions', [App\Http\Controllers\PermissionController::class, 'index'])->name('permissions.index');
Route::middleware('module:permissions,create')->post('/permissions/users', [App\Http\Controllers\PermissionController::class, 'store'])->name('permissions.users.store');
Route::middleware('module:permissions,update')->put('/permissions/users/{user}', [App\Http\Controllers\PermissionController::class, 'update'])->name('permissions.users.update');

Route::middleware('module:billing,view')->group(function () {
    Route::get('/billing', [App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/export', [App\Http\Controllers\BillingController::class, 'export'])->name('billing.export');
});

Route::middleware('auth')->get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

