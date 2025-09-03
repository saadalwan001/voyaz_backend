<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    // to return all admin in dropdown in blogs creation page
    public function index(){
        $admins=Admin::select('id','name')->get();
        return response()->json($admins);
    }



}
