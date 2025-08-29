<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        // Decode JSON arrays if sent as strings
        foreach (['itineraries', 'included_items', 'excluded_items'] as $field) {
            if ($request->has($field) && is_string($request->$field)) {
                $request->merge([$field => json_decode($request->$field, true)]);
            }
        }

        $isNew = filter_var($request->input('is_new_package', true), FILTER_VALIDATE_BOOLEAN);

        if ($isNew) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'total_days' => 'required|integer|min:1',
                'description' => 'required|string',
                'enabled' => 'nullable|boolean',
                'included_items' => 'nullable|array',
                'excluded_items' => 'nullable|array',
                'main_image' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image1' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image2' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image3' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'sub_image4' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
                'itineraries' => 'sometimes|array',
                'itineraries.*.day_title' => 'required_with:itineraries|string|max:255',
                'itineraries.*.description' => 'required_with:itineraries|string',
                'itineraries.*.include_toggle' => 'nullable|boolean',
            ]);

            $data = $validated;

            // Upload images
            foreach (['main_image','sub_image1','sub_image2','sub_image3','sub_image4'] as $key) {
                if ($request->hasFile($key)) {
                    $path = $request->file($key)->store('packages', 'public');
                    $data[$key] = '/storage/' . $path;
                }
            }

            $data['included_items'] = $validated['included_items'] ?? [];
            $data['excluded_items'] = $validated['excluded_items'] ?? [];

            $package = TourPackage::create($data);

        } else {
            $validated = $request->validate([
                'existing_package_id' => 'required|exists:tour_packages,id',
                'itineraries' => 'required|array',
                'itineraries.*.day_title' => 'required|string|max:255',
                'itineraries.*.description' => 'required|string',
                'itineraries.*.include_toggle' => 'nullable|boolean',
            ]);

            $package = TourPackage::findOrFail($validated['existing_package_id']);
        }

        if (!empty($validated['itineraries'])) {
            $itineraries = array_map(function($it) {
                return [
                    'day_title' => $it['day_title'] ?? '',
                    'description' => $it['description'] ?? '',
                    'include_toggle' => $it['include_toggle'] ?? false,
                ];
            }, $validated['itineraries']);

            $package->itineraries()->createMany($itineraries);
        }

        return response()->json($package->load('itineraries'), 201);
    }

    // Update package details along with itineraries
    public function update(Request $request, $id)
    {
        $package = TourPackage::findOrFail($id);

        // Decode JSON arrays if sent as strings
        foreach (['itineraries', 'included_items', 'excluded_items'] as $field) {
            if ($request->has($field) && is_string($request->$field)) {
                $request->merge([$field => json_decode($request->$field, true)]);
            }
        }

        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'total_days'   => 'sometimes|required|integer|min:1',
            'description'  => 'sometimes|required|string',
            'enabled'      => 'sometimes|boolean',
            'included_items' => 'nullable|array',
            'excluded_items' => 'nullable|array',
            'main_image'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image1'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image2'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image3'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'sub_image4'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'itineraries'  => 'nullable|array',
        ]);

        DB::transaction(function() use ($request, $package, $validated) {

            $data = $validated;

            // Upload images if exists
            foreach (['main_image','sub_image1','sub_image2','sub_image3','sub_image4'] as $key) {
                if ($request->hasFile($key)) {
                    $path = $request->file($key)->store('packages', 'public');
                    $data[$key] = '/storage/' . $path;
                }
            }

            $data['included_items'] = $validated['included_items'] ?? $package->included_items ?? [];
            $data['excluded_items'] = $validated['excluded_items'] ?? $package->excluded_items ?? [];

            $package->update($data);

            // Update itineraries if provided
            if (!empty($validated['itineraries'])) {
                foreach ($validated['itineraries'] as $it) {
                    if (isset($it['id'])) {
                        $existingIt = $package->itineraries()->find($it['id']);
                        if ($existingIt) {
                            $existingIt->update([
                                'day_title' => $it['day_title'] ?? $existingIt->day_title,
                                'description' => $it['description'] ?? $existingIt->description,
                                'include_toggle' => $it['include_toggle'] ?? false,
                            ]);
                        }
                    } else {
                        $package->itineraries()->create([
                            'day_title' => $it['day_title'] ?? 'Day',
                            'description' => $it['description'] ?? '',
                            'include_toggle' => $it['include_toggle'] ?? false,
                        ]);
                    }
                }
            }
        });

        return response()->json($package->load('itineraries'));
    }

    public function destroy($id)
    {
        $package = TourPackage::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function toggle($id)
    {
        $package = TourPackage::findOrFail($id);
        $package->enabled = !$package->enabled;
        $package->save();
        return response()->json($package);
    }

    public function allPackages()
    {
        return response()->json(
            TourPackage::select('id','title')->orderByDesc('id')->get()
        );
    }

    public function latest()
    {
        $packages = TourPackage::orderByDesc('created_at')->take(3)->get();
        return response()->json($packages);
    }

    public function showPublic($id)
    {
        $package = TourPackage::with('itineraries')->findOrFail($id);
        return response()->json($package);
    }
}
