<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnquiryMail;

class EnquiryController extends Controller
{
    public function send(Request $request){
        $request ->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'contactNumber' => 'required|string|max:255',
            'adults' => 'required|string|max:255',
            'children' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:255',

        ]);

        $data = $request->all();

        //below is the place where we used to specify the mail address to which the mail has to be sent
        Mail::to('saadalwan765@gmail.com')->send(new EnquiryMail($data));

        return response()->json(['message' => 'Enquiry sent successfully!']);
}
}
