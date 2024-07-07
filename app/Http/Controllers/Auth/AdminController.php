<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function adminRegister(Request $request)
    {
        $registerField = $request->validate([
            'admin_username' => ['required', 'min:3', 'max:10', 'unique:admin'],
            'admin_email' => ['required', 'email', 'unique:admins'],
            'admin_pass' => ['required', 'min:8', 'confirmed'],
        ]);
    
        $registerField['admin_pass'] = Hash::make($registerField['admin_pass']);
        $admin = Admin::create($registerField);
    
        // Generate a token for the new admin
        $token = $admin->createToken('admin_token')->plainTextToken;
    
        return response()->json(['message' => 'Admin registered successfully!', 'admin' => $admin, 'token' => $token], 201);
    }

    public function adminLogin(Request $request)
    {
        $loginField = $request->validate([
            'adminloginName' => 'required',
            'adminloginPass' => 'required',
        ]);

        $admin = Admin::where('admin_username', $loginField['adminloginName'])->first();

        if ($admin && Hash::check($loginField['adminloginPass'], $admin->admin_pass)) {
            $token = $admin->createToken('admin_token')->plainTextToken;
            return response()->json(['token' => $token, 'admin' => $admin], 200);
        }

        return response()->json(['error' => 'Unauthorized.'], 401);
    }

    public function adminLogout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $admin->tokens()->delete();

        return response()->json(['message' => 'Admin logged out successfully.'], 200);
    }
}
