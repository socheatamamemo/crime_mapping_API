<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Delete user in table view
    public function deleteUser(User $user)
    {
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    // Edit user in table view
    public function editUser(User $user){
        return view('admin_page/edit-user', );
    }
    public function update(Request $request, User $user)
    {
        // Validate incoming request data if needed
        // Example: $request->validate([...]);

        // Update the user with validated or all request data
        $user->update($request->all());

        // Return a JSON response indicating success
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }
    // Registering user
    public function registerUser(Request $request){
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = User::create([
            'first_name' => $validatedData['firstName'],
            'last_name' => $validatedData['lastName'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);
    
        return redirect('/login');
    }
    

    // Logging in user
    public function loginUser(Request $request){
        // Validate the input
        $credentials = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
    
        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication passed, regenerate the session
            $request->session()->regenerate();
    
            // Redirect to the home page with a success message
            return redirect()->intended('/home')->with('success', 'You are logged in!');
        }
    
        // Authentication failed, redirect back with an error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function logoutUser(){
        auth() -> logout();
        return redirect('/');
    }
}
