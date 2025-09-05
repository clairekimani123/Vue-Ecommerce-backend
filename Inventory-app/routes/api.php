<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login/admin', [AuthController::class, 'loginAdmin']);
Route::post('/login/supplier', [AuthController::class, 'loginSupplier']);
Route::post('/login/customer', [AuthController::class, 'loginCustomer']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');



Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('suppliers', SupplierController::class);
});

Route::middleware(['auth:sanctum', 'role:admin,supplier'])->group(function () {
    Route::apiResource('customers', CustomerController::class);
});


Route::middleware('auth:sanctum')->get('/profile', function (\Illuminate\Http\Request $request) {
    return $request->user();
});
