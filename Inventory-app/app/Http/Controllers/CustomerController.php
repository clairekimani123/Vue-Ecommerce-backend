<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $this->authorizeRole(['admin', 'supplier']);

        $customers = User::where('role', 'customer')->get();

        return response()->json($customers);
    }


    public function store(Request $request)
    {
        $this->authorizeRole(['admin', 'supplier']);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $customer = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role'     => 'customer',
        ]);

        return response()->json([
            'message'  => 'Customer created successfully',
            'customer' => $customer
        ]);
    }


    public function show($id)
    {
        $this->authorizeRole(['admin', 'supplier']);

        $customer = User::where('role', 'customer')->findOrFail($id);

        return response()->json($customer);
    }


    public function update(Request $request, $id)
    {
        $this->authorizeRole(['admin', 'supplier']);

        $customer = User::where('role', 'customer')->findOrFail($id);

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $customer->id,
            'password' => 'sometimes|min:6|confirmed',
        ]);

        $customer->update([
            'name'  => $validated['name'] ?? $customer->name,
            'email' => $validated['email'] ?? $customer->email,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $customer->password,
        ]);

        return response()->json([
            'message'  => 'Customer updated successfully',
            'customer' => $customer
        ]);
    }


    public function destroy($id)
    {
        $this->authorizeRole(['admin', 'supplier']);

        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }

    // Customer dashboard (for logged in customers)
    public function dashboard()
    {
        $this->authorizeRole(['customer']);

        return response()->json([
            'message' => 'Welcome to the Customer Dashboard',
            'user'    => auth()->user()
        ]);
    }

    // ✅ Helper: check role
    private function authorizeRole(array $roles)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
