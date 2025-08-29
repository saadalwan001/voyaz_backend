<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttractionController extends Controller
{
    public function index(){
        return response()->json(Attraction::orderByDesc('id')->get());

    }

    //to show each attraction in frontend page
    public function show($id){
        $attraction=Attraction::findOrFail($id);
        return response()->json($attraction);
    }

    //store new attraction
    public function store(Request $request)
    {
        $validated=$request->validate([
            'title'=> 'required|string|max:225',
            'description'=>'required|string',
            'front_img'=> 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
            'back_img'=> 'nullable|image|mimes:jpg,png,jpeg,webp|max:5120',
        ]);

        if($request->hasFile('front_img')){
            $path=$request->file('front_img')->store('attractions', 'public');
            $validated['front_img']='/storage/'.$path;
        }

        if($request->hasFile('back_img')){
            $path=$request->file('back_img')->store('attractions', 'public');
            $validated['back_img']='/storage/'.$path;
        }

        $attraction=Attraction::create($validated);

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

        if ($request->hasFile('front_img')) {
            // Delete old image if exists
            if ($attraction->front_img) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->image));
            }
            $path = $request->file('front_img')->store('attractions', 'public');
            $validated['front_img'] = '/storage/' . $path;
        }
        if ($request->hasFile('back_img')) {
            // Delete old image if exists
            if ($attraction->back_img) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $attraction->image));
            }
            $path = $request->file('back_img')->store('attractions', 'public');
            $validated['back_img'] = '/storage/' . $path;
        }


        $attraction->update($validated);

        // Update linked tour packages
        $attraction->tourPackages()->sync($request->input('tour_packages', []));

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
