<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔹 Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login/admin', [AuthController::class, 'loginAdmin']);
Route::post('/login/supplier', [AuthController::class, 'loginSupplier']);
Route::post('/login/customer', [AuthController::class, 'loginCustomer']);

// 🔹 Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// 🔹 Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Route::get('/admin/dashboard', [AdminController::class, 'index']);
    Route::resource('/admin/suppliers', SupplierController::class);
    Route::resource('/admin/customers', CustomerController::class);
});

// 🔹 Supplier routes
Route::middleware(['auth:sanctum', 'role:supplier'])->group(function () {
    Route::get('/supplier/dashboard', [SupplierController::class, 'dashboard']);
    Route::resource('/supplier/customers', CustomerController::class);
});

// 🔹 Customer routes
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard']);
});
