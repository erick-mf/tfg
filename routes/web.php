<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LoginController;
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
});
