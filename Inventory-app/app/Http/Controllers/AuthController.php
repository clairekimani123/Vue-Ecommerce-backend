<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,supplier,customer',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role'     => $validated['role'],
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ]);
    }

    /**
     * Login as Admin
     */
    public function loginAdmin(Request $request)
    {
        return $this->loginByRole($request, 'admin');
    }

    /**
     * Login as Supplier
     */
    public function loginSupplier(Request $request)
    {
        return $this->loginByRole($request, 'supplier');
    }

    /**
     * Login as Customer
     */
    public function loginCustomer(Request $request)
    {
        return $this->loginByRole($request, 'customer');
    }

    /**
     * Shared login function with role validation
     */
    private function loginByRole(Request $request, $role)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== $role) {
                return response()->json(['message' => 'Unauthorized for this login type'], 403);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => ucfirst($role) . ' login successful',
                'token'   => $token,
                'user'    => $user
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
