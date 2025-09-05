<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard
     */
    public function index()
    {
        return response()->json([
            'message' => 'Welcome to the Admin Dashboard',
            'user'    => auth()->user()
        ]);
    }
}
