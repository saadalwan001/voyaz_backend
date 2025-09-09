<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\companyContact;
use Illuminate\Http\Request;

class CompanyContactController extends Controller
{
    //fetching contact info
    public function index()
    {
       $contact=companyContact::first();
       return response()->json($contact);

    }

    //updating contact info
    public function update(Request $request, $id){
        $validated = $request->validate([
            'address'=>'required|string|max:500',
            'phone1'=> 'required|string|max:15',
            'phone2'=>'required|string|max:15',
            'land_p'=>'required|string|max:15',
            'whatsapp'=>'required|string|max:15',
            'email'=>'required|email|max:255',

        ]);

        $contact=companyContact::findOrFail($id);
        $contact -> update($validated);

        return response()->json([
            'message' => 'contact info is updated successfully!',
            'contact' => $contact

        ]);
    }
}
