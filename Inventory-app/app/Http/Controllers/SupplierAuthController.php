<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierAuthController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        return Supplier::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:suppliers,phone',
            'location' => 'nullable|string',
            'email' => 'nullable|email|unique:supplier,email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' requires password_confirmation
        ]);

        $validated['password'] = Hash::make($validated['password']);

        return Supplier::create($validated);
    }

    public function show(Supplier $customer)
    {
        return $supplier;
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'string',
            'phone' => 'string|unique:supplier,phone,' . $supplier->id,
            'location' => 'nullable|string',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'password' => 'sometimes|string|min:8|confirmed', // Optional, requires password_confirmation
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $supplier->update($validated);
        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Deleted']);
    }
}



