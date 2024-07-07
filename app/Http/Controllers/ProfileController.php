<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile information.
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }

    /**
     * Show the form for editing the user's profile information.
     */
    public function edit(Request $request): JsonResponse
    {
        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $validatedData = $request->validated();

        // Update email verification flag if email is changed
        if (array_key_exists('email', $validatedData) && $validatedData['email'] !== $user->email) {
            $validatedData['email_verified_at'] = null;
        }

        // Update password if filled
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->input('password'));
        } else {
            unset($validatedData['password']); // Remove password field from update if empty
        }

        // Handle profile image update
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }

            $image = $request->file('profile_image');
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('images/profile'), $imageName);
            $validatedData['profile_image'] = 'images/profile/'.$imageName;
        }

        // Update phone and address
        $validatedData['phone'] = $request->input('phone');
        $validatedData['address'] = $request->input('address');

        // Save the updated user
        $user->update($validatedData);

        return response()->json(['message' => 'Profile updated successfully!', 'user' => $user], 200);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Account deleted successfully!'], 200);
    }
}
