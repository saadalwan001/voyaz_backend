<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated=$request->validate([
            'blog_id' => 'required|exists:blogs,id',
            'commenter_name' => 'required|string|max:225',
            'commenter_email' => 'required|email|max:255',
            'commenter_text' => 'required|string',
        ]);

        $comment=comment::create($validated);

        return response()->json([
            'message'=> 'Comment Posted Successfully!',
            'comment' => $comment,
        ],201);

    }
    //to get comment to the blog page

    public function index($blogId){
        $comments=comment::where('blog_id', $blogId)
            ->latest()
            ->get();
        return response()->json($comments);
    }
}
