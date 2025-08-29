<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    // List itineraries for a package
    public function index(TourPackage $package)
    {
        return response()->json(
            $package->itineraries()->orderBy('id')->get()
        );
    }

    // Add one or many itineraries to a package
    public function store(Request $request, TourPackage $package)
    {
        $request->validate([
            'itineraries'               => 'required|array|min:1',
            'itineraries.*.day_title'   => 'required|string|max:255',
            'itineraries.*.description' => 'required|string',
            'itineraries.*.include_toggle' => 'boolean',
        ]);

        $created = $package->itineraries()->createMany($request->itineraries);

        return response()->json([
            'message' => 'Itineraries added successfully',
            'itineraries' => $created
        ], 201);
    }

    // Update each individual  itinerary
    public function update(Request $request, Itinerary $itinerary)
    {

        $request->validate([
            'day_title'       => 'required|string|max:255',
            'description'     => 'required|string',
            'include_toggle'  => 'nullable|boolean',
        ]);


        $itinerary->update(
            $request->only(['day_title', 'description', 'include_toggle'])
        );

        return response()->json([
            'message' => 'Itinerary updated successfully',
            'itinerary' => $itinerary
        ]);
    }


    public function destroy(Itinerary $itinerary)
    {
        $itinerary->delete();
        return response()->json([
            'message' => 'Itinerary deleted successfully'
        ]);
    }
}
