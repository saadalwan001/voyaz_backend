<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttractionController extends Controller
{
    // Return all attraction cards for admin or frontend
    public function index()
    {
        return response()->json(Attraction::orderByDesc('id')->get());
    }

    // Show only latest 6 attractions for frontend display
    public function latest()
    {
        return response()->json(Attraction::orderByDesc('id')->take(6)->get());
    }

    // Show single attraction by ID
    public function show($id)
    {
        $attraction = Attraction::findOrFail($id);
        return response()->json($attraction);
    }

    // Return all tour packages linked to this attraction
    public function tourPackages($id)
    {
        $attraction = Attraction::with('tourPackages')->findOrFail($id);
        return response()->json($attraction->tourPackages);
    }

    // Store new attraction
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:225',
            'description' => 'required|string',
            'front_img' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'back_img' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        if ($request->hasFile('front_img')) {
            $path = $request->file('front_img')->store('attractions', 'public');
            $validated['front_img'] = '/storage/' . $path;
        }

        if ($request->hasFile('back_img')) {
            $path = $request->file('back_img')->store('attractions', 'public');
            $validated['back_img'] = '/storage/' . $path;
        }

        $attraction = Attraction::create($validated);

        // Link selected tour packages (many-to-many)
        $attraction->tourPackages()->sync($request->input('tour_packages', []));

        return response()->json($attraction, 201);
    }

    // Update attraction
    public function update(Request $request, $id)
    {
        $attraction = Attraction::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'front_img' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'back_img' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        // Handle Front Image
        if ($request->hasFile('front_img')) {
            if ($attraction->front_img) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->front_img));
            }
            $path = $request->file('front_img')->store('attractions', 'public');
            $validated['front_img'] = '/storage/' . $path;
        } elseif ($request->input('remove_front_img')) {
            // Remove existing image if frontend requests deletion
            if ($attraction->front_img) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->front_img));
            }
            $validated['front_img'] = null;
        }

        // Handle Back Image
        if ($request->hasFile('back_img')) {
            if ($attraction->back_img) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->back_img));
            }
            $path = $request->file('back_img')->store('attractions', 'public');
            $validated['back_img'] = '/storage/' . $path;
        } elseif ($request->input('remove_back_img')) {
            if ($attraction->back_img) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->back_img));
            }
            $validated['back_img'] = null;
        }

        $attraction->update($validated);

        // Update linked tour packages safely
        if ($request->has('tour_packages')) {
            $attraction->tourPackages()->sync($request->input('tour_packages', []));
        }

        return response()->json($attraction);
    }

    // Delete attraction
    public function destroy($id)
    {
        $attraction = Attraction::findOrFail($id);

        if ($attraction->front_img) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->front_img));
        }

        if ($attraction->back_img) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->back_img));
        }

        $attraction->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
