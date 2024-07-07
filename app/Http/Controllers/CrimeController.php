<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CrimePending;
use App\Models\CrimeType;
use App\Models\Crime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CrimeController extends Controller
{
    public function displayCrimereport(Request $request): JsonResponse
{
    $user = $request->user();
    $crimeTypes = CrimeType::all();

    return response()->json([
        'user' => $user,
        'crime_types' => $crimeTypes,
    ], 200);
}
public function reportCrime(Request $request): JsonResponse
{
    // Validate the request data
    $validated = $request->validate([
        'crime_type' => 'required|string',
        'date' => 'required|date',
        'description' => 'required|string',
        'address' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    $crimeType = CrimeType::where('crime_type_name', $validated['crime_type'])->first();

    if (!$crimeType) {
        return response()->json(['error' => 'Invalid crime type selected.'], 400);
    }

    // Create the pending crime record
    $crimePending = CrimePending::create([
        'description' => $validated['description'],
        'date' => $validated['date'],
        'status' => 'pending',
        'longitude' => $validated['longitude'],
        'latitude' => $validated['latitude'],
        'address' => $validated['address'],
        'crime_type' => $crimeType->crime_type_name,
        'reportedby_user_id' => auth()->user()->id,
    ]);

    return response()->json([
        'message' => 'Crime reported successfully!',
        'crime' => [
            'id' => $crimePending->id,
            'crime_type' => $crimePending->crime_type,
            'date' => $crimePending->date,
            'description' => $crimePending->description,
        ]
    ], 201);
}
    public function deleteCrime(Crime $crime)
    {
        if (!$crime) {
            return response()->json(['error' => 'Crime not found.'], 404);
        }

        $crime->delete();

        return response()->json(['message' => 'Crime deleted successfully.']);
    }
    public function update(Request $request, Crime $crime)
{
    // Validate the request data (add validation rules as necessary)
    $validatedData = $request->validate([
        // Add your validation rules here
            'crime_type' => 'required|string',
            'date' => 'required|date',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
    ]);

    // Update the crime record with validated data
    $crime->update($validatedData);

    // Return a JSON response instead of redirecting
    return response()->json(['message' => 'Crime updated successfully', 'crime' => $crime]);
}
    public function approvePendingCrime(CrimePending $crimePending)
{
    $crime = new Crime([
        'description' => $crimePending->description,
        'date' => $crimePending->date,
        'status' => $crimePending->status,
        'longitude' => $crimePending->longitude,
        'latitude' => $crimePending->latitude,
        'address' => $crimePending->address,
        'reportedby_user_id' => $crimePending->reportedby_user_id,
        'crime_type' => $crimePending->crime_type,
        'approvedby_admin_id' => Auth::guard('admin')->id(),
    ]);

    $crime->save();
    $crimePending->delete();

    return response()->json(['message' => 'Crime confirmed successfully', 'crime' => $crime], 200);
}


    public function deletePendingCrime(CrimePending $crimePending)
    {
        $crimePending->delete();

        // Return a JSON response indicating successful deletion
        return response()->json([
            'message' => 'Pending crime deleted successfully',
        ]);
    }
}
