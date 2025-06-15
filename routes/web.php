<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Bar\BarController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\Waiter\WaiterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'store'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::prefix('admin')->middleware('role:admin,encargado')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/users', UserController::class);
    Route::resource('/locations', LocationController::class);
    Route::resource('/categories', MenuCategoryController::class);
    Route::resource('/products', ProductController::class);
    Route::resource('/menu-items', MenuItemController::class);
    Route::resource('/tables', TableController::class);
    Route::resource('/orders', OrderController::class)->except('create', 'show', 'store');
});

Route::prefix('kitchen')->middleware('role:cocinero')->group(function () {
    Route::get('/view', [KitchenController::class, 'index'])->name('kitchen.view');
    Route::get('/orders-data', [KitchenController::class, 'getOrdersData'])->name('kitchen.orders.data');
    Route::put('/menu-item/{menuItem}/toggle', [KitchenController::class, 'toggleAvailability'])->name('kitchen.menu-items.toggle');
    Route::put('/order-item/{orderItem}/status', [KitchenController::class, 'updateItemStatus'])->name('kitchen.updateItemStatus');
});

Route::prefix('waiter')->middleware('role:camarero')->group(function () {
    Route::get('/view', [WaiterController::class, 'index'])->name('waiter.view');
    Route::post('/orders', [WaiterController::class, 'storeOrder'])->name('waiter.orders.store');
    Route::put('/orders/{order}', [WaiterController::class, 'updateOrder'])->name('waiter.orders.update');
});

Route::prefix('bar')->middleware('role:barman')->group(function () {
    Route::get('/view', [BarController::class, 'index'])->name('bar.view');
    Route::get('/orders-data', [BarController::class, 'getOrdersData'])->name('bar.orders.data');
    Route::put('/menu-item/{menuItem}/toggle', [BarController::class, 'toggleAvailability'])->name('bar.menu-items.toggle');
    Route::put('/order-item/{orderItem}/status', [BarController::class, 'updateItemStatus'])->name('bar.updateItemStatus');
});
