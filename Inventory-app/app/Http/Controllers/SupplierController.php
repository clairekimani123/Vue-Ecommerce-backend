<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
            'name'     => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone'    => 'required|string|unique:suppliers,phone',
            'email'    => 'required|email|unique:suppliers,email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' requires password_confirmation
        ]);

        return Supplier::create($validated);
    }

    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'     => 'string|max:255',
            'location' => 'nullable|string|max:255',
            'phone'    => 'string|unique:suppliers,phone,' . $supplier->id,
            'email'    => 'email|unique:suppliers,email,' . $supplier->id,
            'password' => 'sometimes|string|min:8|confirmed', // Optional, requires password_confirmation
        ]);

        $supplier->update($validated);
        return $supplier;
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted']);
    }
}
