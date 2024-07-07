<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function createGuest()
    {
        return response()->json([
            'message' => 'Guest contact form',
            // You can include form structure or other necessary data here if needed
        ]);
    }

    public function createAuth()
    {
        $user = Auth::user();
        return response()->json([
            'message' => 'Authenticated user contact form',
            'user' => $user,
            // Include any other necessary data
        ]);
    }

    public function storeGuest(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        try {
            Contact::create($validatedData);
            return response()->json([
                'message' => 'Your message has been sent!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeAuth(Request $request): JsonResponse
{
    $validatedData = $request->validate([
        'full_name' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
    ]);

    try {
        $contact = new Contact($validatedData);
        $contact->user_id = Auth::id(); // Associate the contact with the authenticated user
        $contact->email_address = Auth::user()->email; // Use authenticated user's email
        $contact->save();

        return response()->json([
            'message' => 'Your message has been sent!',
            'contact' => $contact, // Include the contact object in the response
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to save data: ' . $e->getMessage(),
        ], 500);
    }
}
    public function deleteContact(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Contact deleted successfully']);
    }

}

