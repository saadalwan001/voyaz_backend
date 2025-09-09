<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    // to return all admin in dropdown in blogs creation page
    public function index(){
        $admins=Admin::select('id','name')->get();
        return response()->json($admins);
    }
//to fetch data to admin_profile page
    public function getProfile(Request $request) {
        $admin = $request->user();
        return response()->json($admin);
    }

    public function updateProfile(Request $request){
        $admin = auth()->user(); // to get currently logged in admin

        $validated=$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin,email,' .$admin->id,
            'current_password'=>'nullable|string',
            'new_password' => 'nullable|string|min:6',

        ]);

        //checking current password with db if new password is provided only
        if(!empty($validated['new_password'])){
            if (!Hash::check($validated['current_password'], $admin->password)){
                return response()->json(['message'=>'current password is incorrect'],422);

            }
            $admin->password=$validated['new_password'];  //will be automatically hashed
        }

        $admin->name=$validated['name'];
        $admin->email=$validated['email'];
        $admin->save();

        return response()->json(['message'=> 'Profile Updated Successfully', 'admin' => $admin]);
    }



}
