<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        return Blog::with('admin')->latest()->get();
    }

    public function show($id)
    {
        $blog = Blog::with('admin')->find($id);
        if (!$blog) return response()->json(['message' => 'Blog not found'], 404);
        return response()->json($blog);
    }

    public function store(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'title' => 'required|string|max:500',
            'description' => 'required|string',
            'publisher_date' => 'required|date',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('blogs', 'public') : null;

        $blog = Blog::create([
            'admin_id' => $request->admin_id,
            'title' => $request->title,
            'description' => $request->description,
            'publisher_date' => $request->publisher_date,
            'category' => $request->category,
            'img_url' => $imagePath ? "/storage/$imagePath" : null,
        ]);

        return response()->json(['message' => 'Blog created successfully', 'data' => $blog], 201);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) return response()->json(['message' => 'Blog not found'], 404);

        $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'title' => 'required|string|max:500',
            'description' => 'required|string',
            'publisher_date' => 'required|date',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($blog->img_url) Storage::disk('public')->delete(str_replace('/storage/', '', $blog->img_url));
            $imagePath = $request->file('image')->store('blogs', 'public');
            $blog->img_url = "/storage/$imagePath";
        }

        $blog->update([
            'admin_id' => $request->admin_id,
            'title' => $request->title,
            'description' => $request->description,
            'publisher_date' => $request->publisher_date,
            'category' => $request->category,
        ]);

        return response()->json(['message' => 'Blog updated successfully', 'data' => $blog]);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog) return response()->json(['message' => 'Blog not found'], 404);

        if ($blog->img_url) Storage::disk('public')->delete(str_replace('/storage/', '', $blog->img_url));

        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }
}
