<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourPackageController extends Controller
{
    // List all tour packages with itinerary count
    public function index()
    {
        return response()->json(
            TourPackage::withCount('itineraries')->orderByDesc('id')->get()
        );
    }

    // Show single package with itineraries
    public function show($id)
    {
        $package = TourPackage::with('itineraries')->findOrFail($id);
        return response()->json($package);
    }

    // Store new package or add itineraries to existing package
    public function store(Request $request)
    {
        // Decode itineraries JSON if it's a string
        if ($request->has('itineraries') && is_string($request->itineraries)) {
            $request->merge(['itineraries' => json_decode($request->itineraries, true)]);
        }

        $isNew = filter_var($request->input('is_new_package', true), FILTER_VALIDATE_BOOLEAN);

        if ($isNew) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'total_days' => 'required|integer|min:1',
                'description' => 'required|string',
                'enabled' => 'nullable|boolean',

                'main_image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image1' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image2' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image3' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image4' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',

                'itineraries' => 'sometimes|array',
                'itineraries.*.day_title' => 'required_with:itineraries|string|max:255',
                'itineraries.*.description' => 'required_with:itineraries|string',
                'itineraries.*.include_toggle' => 'nullable|boolean',
                'itineraries.*.included_items' => 'nullable|array',
                'itineraries.*.excluded_items' => 'nullable|array',
            ]);

            $data = $validated;

            // Upload images if present
            foreach (['main_image', 'sub_image1', 'sub_image2', 'sub_image3', 'sub_image4'] as $key) {
                if ($request->hasFile($key)) {
                    $path = $request->file($key)->store('packages', 'public');
                    $data[$key] = '/storage/' . $path;
                }
            }

            // Create package
            $package = TourPackage::create($data);
        } else {
            // Existing package
            $validated = $request->validate([
                'existing_package_id' => 'required|exists:tour_packages,id',
                'itineraries' => 'required|array',
                'itineraries.*.day_title' => 'required|string|max:255',
                'itineraries.*.description' => 'required|string',
                'itineraries.*.include_toggle' => 'nullable|boolean',
                'itineraries.*.included_items' => 'nullable|array',
                'itineraries.*.excluded_items' => 'nullable|array',
            ]);

            $package = TourPackage::findOrFail($validated['existing_package_id']);
        }

        // Create itineraries with proper defaults
        if (!empty($validated['itineraries'])) {
            $itineraries = array_map(function($it) {
                return [
                    'day_title' => $it['day_title'] ?? '',
                    'description' => $it['description'] ?? '',
                    'include_toggle' => $it['include_toggle'] ?? false,
                    'included_items' => $it['included_items'] ?? [],
                    'excluded_items' => $it['excluded_items'] ?? [],
                ];
            }, $validated['itineraries']);

            $package->itineraries()->createMany($itineraries);
        }

        return response()->json($package->load('itineraries'), 201);
    }

    // Update package details
    public function update(Request $request, $id)
    {
        $package = TourPackage::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'total_days'   => 'sometimes|required|integer|min:1',
            'description'  => 'sometimes|required|string',
            'enabled'      => 'sometimes|boolean',

            'main_image'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image1'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image2'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image3'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image4'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        $data = $validated;

        // Upload main image if exists
        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('packages', 'public');
            $data['main_image'] = '/storage/' . $path;
        }

        // Upload sub-images if exist
        foreach (['sub_image1','sub_image2','sub_image3','sub_image4'] as $key) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('packages', 'public');
                $data[$key] = '/storage/' . $path;
            }
        }

        $package->update($data);

        return response()->json($package);
    }

    // Delete a package
    public function destroy($id)
    {
        $package = TourPackage::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // Toggle package enabled/disabled
    public function toggle($id)
    {
        $package = TourPackage::findOrFail($id);
        $package->enabled = !$package->enabled;
        $package->save();

        return response()->json($package);
    }

    // Fetch all packages (for dropdown in frontend)
    public function allPackages()
    {
        return response()->json(
            TourPackage::select('id','title')->orderByDesc('id')->get()
        );
    }
    //to display the recent three packages
    public function latest()
    {
        $packages = TourPackage::orderByDesc('created_at')
            ->take(3)
            ->get();

        return response()->json($packages);
    }

   // to fetch package details on by one in new page publicly

    public function showPublic($id)
    {
        $package = TourPackage::with('itineraries')->findOrFail($id);
        return response()->json($package);
    }
}
