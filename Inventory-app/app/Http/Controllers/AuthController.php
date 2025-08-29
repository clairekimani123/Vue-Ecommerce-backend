<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Supplier;

class AuthController extends Controller
{
    // ✅ Register Customer
    public function registerCustomer(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:customers',
            'phone'    => 'required|string|max:20',
            'location' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'location' => $request->location,
            'password' => Hash::make($request->password),
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'user'  => $customer,
            'token' => $token,
            'role'  => 'customer'
        ], 201);
    }

    // ✅ Login Customer
    public function loginCustomer(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'user'  => $customer,
            'token' => $token,
            'role'  => 'customer'
        ]);
    }

    // ✅ Register Supplier
    public function registerSupplier(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:suppliers',
            'phone'    => 'required|string|max:20',
            'location' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $supplier = Supplier::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'location' => $request->location,
            'password' => Hash::make($request->password),
        ]);

        $token = $supplier->createToken('supplier-token')->plainTextToken;

        return response()->json([
            'user'  => $supplier,
            'token' => $token,
            'role'  => 'supplier'
        ], 201);
    }

    // ✅ Login Supplier
    public function loginSupplier(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $supplier = Supplier::where('email', $request->email)->first();

        if (! $supplier || ! Hash::check($request->password, $supplier->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $supplier->createToken('supplier-token')->plainTextToken;

        return response()->json([
            'user'  => $supplier,
            'token' => $token,
            'role'  => 'supplier'
        ]);
    }

    // ✅ Logout (works for both)
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
