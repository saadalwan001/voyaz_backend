<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(){
        return Blog::with('admin')->latest()->get();

    }
    public function store(Request $request){
        $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'title'=> 'required|string|max:500',
            'description'=>'required|string',
            'publisher_date'=>'required|date',
            'category'=>'required|string',
            'image'=>'required|image|max:2048',
        ]);

        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('blogs','public');
        }

        $blog = Blog::create([
            'admin_id' => $request->admin_id,
            'title' => $request->title,
            'description' => $request->description,
            'publisher_date' => $request->publisher_date,
            'img_url' => $imagePath ? "/storage/$imagePath" : null,
            'category' => $request->category,
        ]);

        return response()->json([
            'message' => 'Blog created successfully',
            'data' => $blog
        ], 201);
    }

}
