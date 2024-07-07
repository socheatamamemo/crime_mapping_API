<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Crime;
use App\Models\CrimeType;
use App\Models\CrimePending;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // User Data Table
    public function displayUser()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Display Approved Crime
    public function displayCrime()
    {
        $crimes = Crime::all();
        // $user = Auth::user();
        // $crimeTypes = CrimeType::all();
        return response()->json([
            'crimes' => $crimes,
            // 'crimeTypes' => $crimeTypes,
            // 'user' => $user
        ]);
    }

    // Display Pending Crimes
    public function displayPendingCrime()
    {
        $crime_pendings = CrimePending::all();
        return response()->json(['crime_pendings' => $crime_pendings]);
    }

    public function displayCrimeReport()
    {
        // Assuming you just want to return a success message
        return response()->json(['message' => 'Display crime report view.']);
    }

    public function displayRecently()
    {
        $recentUsers = User::where('created_at', '>=', now()->subDays(7))->get();
        $recentCrimes = Crime::where('created_at', '>=', now()->subDays(7))->get();
        return response()->json([
            'recentUsers' => $recentUsers,
            'recentCrimes' => $recentCrimes
        ]);
    }

    public function addCrimeType()
    {
        // Assuming you just want to return a success message
        return response()->json(['message' => 'Add crime type view.']);
    }

    public function storeCrimeType(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName); // Move uploaded file to public/images directory
        }

        $crimeType = new CrimeType;
        $crimeType->crime_type_name = $request->input('title');
        $crimeType->image = $imageName; // Save file name to database
        $crimeType->save();

        return response()->json(['message' => 'Crime type added successfully.', 'crimeType' => $crimeType]);
    }

    public function showCrimeType()
    {
        $crimeTypes = CrimeType::all();
        return response()->json(['crimeTypes' => $crimeTypes]);
    }

    public function deleteCrimetype($id)
    {
        $crimeType = CrimeType::find($id);
        if ($crimeType) {
            $crimeType->delete();
            return response()->json(['message' => 'Crime type deleted successfully.']);
        }
        return response()->json(['error' => 'Crime type not found.'], 404);
    }
    public function displayContacts()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }
}
