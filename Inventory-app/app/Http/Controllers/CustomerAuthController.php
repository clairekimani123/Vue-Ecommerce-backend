<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|unique:customers',
            'location' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
            'password' => Hash::make($request->password),
        ]);

        $token = $customer->createToken('customerToken')->plainTextToken;

        return response()->json(['customer' => $customer, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Invalid login'], 401);
        }

        $token = $customer->createToken('customerToken')->plainTextToken;

        return response()->json(['customer' => $customer, 'token' => $token]);
    }
}
